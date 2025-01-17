<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Book Posts</h1>
    
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Author</th>
                    <th class="px-6 py-3 text-left">Published</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php
                $sql = "SELECT * FROM book_posts ORDER BY created_at DESC";
                $posts = $conn->query($sql)->fetchAll();
                
                foreach($posts as $post):
                ?>
                <tr>
                    <td class="px-6 py-4">
                        <a href="/book/<?php echo $post['slug']; ?>/" 
                           class="text-blue-600 hover:underline"
                           target="_blank">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($post['author']); ?></td>
                    <td class="px-6 py-4"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-sm rounded-full <?php echo $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo ucfirst($post['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="edit-book.php?id=<?php echo $post['id']; ?>" 
                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 