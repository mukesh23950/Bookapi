<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// Check authentication
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    die('Unauthorized access');
}

// Connect to database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Drop the existing table if it exists
$drop_table_sql = "DROP TABLE IF EXISTS books";
if (!mysqli_query($conn, $drop_table_sql)) {
    die("Error dropping table: " . mysqli_error($conn));
}

// Create the table with the correct schema and ISBN as UNIQUE
$create_table_sql = "CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    isbn VARCHAR(50) UNIQUE,
    published_year VARCHAR(10),
    cover_id VARCHAR(50),
    language VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_isbn (isbn)
)";

if (!mysqli_query($conn, $create_table_sql)) {
    die("Error creating table: " . mysqli_error($conn));
}

mysqli_close($conn);
echo "Table reset successfully!";
?>
