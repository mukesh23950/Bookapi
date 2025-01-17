<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO books (
            title, author, isbn, published_year, 
            cover_id, description, language,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, 
            ?, ?, ?,
            NOW(), NOW()
        )
    ");

    foreach ($data as $book) {
        $stmt->execute([
            $book['title'],
            $book['author'],
            $book['isbn'],
            $book['published_year'],
            $book['cover_id'],
            $book['description'],
            $book['language']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Books saved successfully']);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 