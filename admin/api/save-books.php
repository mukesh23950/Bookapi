<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');

try {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Debug: Print the received data
    error_log("Received data: " . print_r($data, true));

    $pdo->beginTransaction();

    $sql = "INSERT INTO book_posts (
        title, author, cover_image, description, isbn, 
        publish_year, publisher, language, page_count, 
        book_key, slug, meta_title, meta_description, status
    ) VALUES (
        :title, :author, :cover_image, :description, :isbn,
        :publish_year, :publisher, :language, :page_count,
        :book_key, :slug, :meta_title, :meta_description, :status
    )";

    // Debug: Print the SQL query
    error_log("SQL Query: " . $sql);
    
    $stmt = $pdo->prepare($sql);
    
    foreach ($data as $book) {
        // Debug: Print each book's data
        error_log("Processing book: " . print_r($book, true));
        
        $stmt->execute($book);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Books saved successfully']);
    
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log("Error in save-books.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?> 