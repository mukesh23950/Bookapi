function createBookPost($bookData) {
    global $conn;
    
    // Create SEO friendly slug
    $slug = createSlug($bookData['title']);
    
    // Generate meta description
    $metaDesc = substr(strip_tags($bookData['description']), 0, 160);
    
    // Prepare SQL
    $sql = "INSERT INTO book_posts (
        book_key,
        title,
        slug,
        author,
        cover_image,
        description,
        isbn,
        publish_year,
        publisher,
        language,
        page_count,
        meta_title,
        meta_description
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $bookData['key'],
            $bookData['title'],
            $slug,
            $bookData['author_name'][0] ?? '',
            $bookData['cover_i'] ? "https://covers.openlibrary.org/b/id/{$bookData['cover_i']}-L.jpg" : '',
            $bookData['description'] ?? '',
            $bookData['isbn'][0] ?? '',
            $bookData['first_publish_year'] ?? null,
            $bookData['publisher'][0] ?? '',
            $bookData['language'][0] ?? '',
            $bookData['number_of_pages'] ?? null,
            $bookData['title'] . ' by ' . ($bookData['author_name'][0] ?? 'Unknown Author'),
            $metaDesc
        ]);
        
        return $conn->lastInsertId();
    } catch(PDOException $e) {
        error_log("Error creating book post: " . $e->getMessage());
        return false;
    }
} 