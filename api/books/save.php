<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Function to download and save cover image
function saveCoverImage($coverId, $isbn) {
    if (empty($coverId)) return '';
    
    $saveDir = '../../assets/images/covers/';
    if (!file_exists($saveDir)) {
        mkdir($saveDir, 0777, true);
    }
    
    // Use ISBN as filename to avoid duplicates
    $filename = $isbn . '.jpg';
    $savePath = $saveDir . $filename;
    
    // If file already exists, return existing path
    if (file_exists($savePath)) {
        return '/assets/images/covers/' . $filename;
    }
    
    // Get HD version of cover
    $imageUrl = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
    
    // Download image
    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) {
        error_log("Failed to download cover image for ISBN: $isbn");
        return '';
    }
    
    // Save image
    if (file_put_contents($savePath, $imageData)) {
        return '/assets/images/covers/' . $filename;
    }
    
    return '';
}

try {
    // Get JSON data
    $raw_data = file_get_contents('php://input');
    $books = json_decode($raw_data, true);
    
    if (!is_array($books)) {
        throw new Exception('Invalid data format');
    }

    // Connect to database
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // First, create the table if it doesn't exist with ISBN as unique key
    $create_table_sql = "CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255),
        isbn VARCHAR(50) UNIQUE,
        published_year VARCHAR(10),
        cover_id VARCHAR(50),
        local_cover_path VARCHAR(255),
        language VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_isbn (isbn)
    )";

    if (!mysqli_query($conn, $create_table_sql)) {
        throw new Exception("Error creating table: " . mysqli_error($conn));
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    $duplicates = [];
    $saved = [];
    
    foreach ($books as $book) {
        // Skip if ISBN is empty
        if (empty($book['isbn']) || $book['isbn'] === 'N/A') {
            continue;
        }

        // Check if book already exists
        $check_sql = "SELECT id, title FROM books WHERE isbn = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, 's', $book['isbn']);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($result) > 0) {
            $existing = mysqli_fetch_assoc($result);
            $duplicates[] = $book['title'];
            continue;
        }

        // Download and save cover image
        $localCoverPath = '';
        if (!empty($book['cover_id'])) {
            $localCoverPath = saveCoverImage($book['cover_id'], $book['isbn']);
        }

        // Insert new book with local cover path
        $insert_sql = "INSERT INTO books (title, author, isbn, published_year, cover_id, local_cover_path, language) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        
        if (!$insert_stmt) {
            throw new Exception("Error preparing statement");
        }

        mysqli_stmt_bind_param($insert_stmt, 'sssssss',
            $book['title'],
            $book['author'],
            $book['isbn'],
            $book['published_year'],
            $book['cover_id'],
            $localCoverPath,
            $book['language']
        );
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $saved[] = [
                'title' => $book['title'],
                'cover_path' => $localCoverPath
            ];
        } else {
            throw new Exception("Error saving book: " . mysqli_error($conn));
        }

        mysqli_stmt_close($insert_stmt);
    }

    // Commit transaction
    mysqli_commit($conn);
    
    // Prepare response message
    $message = '';
    if (count($saved) > 0) {
        $titles = array_column($saved, 'title');
        $message .= "Successfully saved: " . implode(", ", $titles) . ". ";
    }
    if (count($duplicates) > 0) {
        $message .= "Already in database: " . implode(", ", $duplicates);
    }
    if (empty($message)) {
        $message = "No books were saved. Please select books to save.";
    }

    echo json_encode([
        'success' => count($saved) > 0,
        'message' => $message,
        'saved' => $saved,
        'duplicates' => $duplicates
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    
    echo json_encode([
        'success' => false,
        'message' => "Error: " . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}