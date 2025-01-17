<?php
require_once 'includes/config.php';

$slug = $_GET['slug'] ?? '';

// Get book details
$sql = "SELECT * FROM book_posts WHERE slug = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$slug]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['meta_title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($book['meta_description']); ?>">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($book['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($book['meta_description']); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($book['cover_image']); ?>">
    
    <link rel="canonical" href="https://yoursite.com/book/<?php echo $book['slug']; ?>/">
</head>
<body>
    <article class="book-single">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Book Cover -->
                <div class="book-cover">
                    <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" 
                         alt="<?php echo htmlspecialchars($book['title']); ?> cover"
                         class="rounded-lg shadow-lg">
                </div>
                
                <!-- Book Info -->
                <div class="book-info">
                    <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($book['title']); ?></h1>
                    <p class="text-xl text-gray-600 mb-4">by <?php echo htmlspecialchars($book['author']); ?></p>
                    
                    <div class="book-meta grid grid-cols-2 gap-4 mb-6">
                        <?php if($book['publish_year']): ?>
                        <div>
                            <span class="text-gray-500">Published:</span>
                            <span><?php echo $book['publish_year']; ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($book['publisher']): ?>
                        <div>
                            <span class="text-gray-500">Publisher:</span>
                            <span><?php echo htmlspecialchars($book['publisher']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($book['isbn']): ?>
                        <div>
                            <span class="text-gray-500">ISBN:</span>
                            <span><?php echo htmlspecialchars($book['isbn']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($book['language']): ?>
                        <div>
                            <span class="text-gray-500">Language:</span>
                            <span><?php echo htmlspecialchars($book['language']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description -->
                    <div class="book-description prose max-w-none">
                        <?php echo nl2br(htmlspecialchars($book['description'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </article>
</body>
</html> 