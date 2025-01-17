<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    // Get JSON data
    $raw_data = file_get_contents('php://input');
    $data = json_decode($raw_data, true);
    
    // Validate books array
    if (!isset($data['books']) || empty($data['books'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'No books were saved. Please select books to save.',
            'saved' => [],
            'duplicates' => []
        ]);
        exit;
    }

    // Connect to database
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    $saved = [];
    $duplicates = [];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        foreach ($data['books'] as $book) {
            // Check if ISBN already exists
            if (!empty($book['isbn'])) {
                $check_sql = "SELECT id FROM books WHERE isbn = ?";
                $check_stmt = mysqli_prepare($conn, $check_sql);
                mysqli_stmt_bind_param($check_stmt, 's', $book['isbn']);
                mysqli_stmt_execute($check_stmt);
                mysqli_stmt_store_result($check_stmt);
                
                if (mysqli_stmt_num_rows($check_stmt) > 0) {
                    $duplicates[] = $book['title'];
                    continue;
                }
            }

            $sql = "INSERT INTO books (
                title, 
                author, 
                isbn, 
                published_year, 
                cover_id,
                local_cover_path,
                description,
                publisher,
                page_count,
                categories,
                rating,
                language,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . mysqli_error($conn));
            }

            // Set default values for optional fields
            $title = $book['title'] ?? '';
            $author = $book['author'] ?? '';
            $isbn = $book['isbn'] ?? '';
            $published_year = $book['published_year'] ?? '';
            $cover_id = $book['cover_id'] ?? '';
            $local_cover_path = $book['local_cover_path'] ?? '';
            $description = $book['description'] ?? '';
            $publisher = $book['publisher'] ?? '';
            $page_count = $book['page_count'] ?? null;
            $categories = $book['categories'] ?? '';
            $rating = $book['rating'] ?? null;
            $language = $book['language'] ?? '';
            $status = $book['status'] ?? 'active';

            mysqli_stmt_bind_param($stmt, 'ssssssssissss', 
                $title, 
                $author, 
                $isbn, 
                $published_year, 
                $cover_id,
                $local_cover_path,
                $description,
                $publisher,
                $page_count,
                $categories,
                $rating,
                $language,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                $saved[] = $book['title'];
            }

            mysqli_stmt_close($stmt);
        }

        // Commit transaction if we have any saved books
        if (!empty($saved)) {
            mysqli_commit($conn);
            echo json_encode([
                'success' => true,
                'message' => count($saved) . ' books saved successfully' . 
                            (count($duplicates) > 0 ? ' (' . count($duplicates) . ' duplicates skipped)' : ''),
                'saved' => $saved,
                'duplicates' => $duplicates
            ]);
        } else {
            throw new Exception('No books were saved. ' . 
                (count($duplicates) > 0 ? 'All selected books were duplicates.' : 'Please select books to save.'));
        }

    } catch (Exception $e) {
        mysqli_rollback($conn);
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'saved' => $saved ?? [],
        'duplicates' => $duplicates ?? []
    ]);
}

if (isset($conn)) {
    mysqli_close($conn);
}