<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check login
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirectTo('../login.php');
}

$page_title = "View Books";
$page_name = "view-books";

// Database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = '';
if (!empty($search)) {
    $where_clause = " WHERE title LIKE '%$search%' 
                      OR author LIKE '%$search%' 
                      OR isbn LIKE '%$search%'
                      OR categories LIKE '%$search%'";
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM books" . $where_clause;
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with pagination
$query = "SELECT * FROM books" . $where_clause . " 
          ORDER BY title LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

include '../includes/header.php';
?>

<div class="flex min-h-screen bg-gray-100">
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Navigation -->
        <div class="bg-white shadow-sm">
            <div class="flex items-center justify-between px-8 h-16">
                <div class="flex items-center">
                    <button id="menu-toggle" class="text-gray-500 hover:text-gray-600 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-800 ml-4">View Books</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>" 
                             alt="Profile" class="w-8 h-8 rounded-full">
                        <span class="font-medium text-gray-700"><?php echo $_SESSION['user_name']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-8">
            <!-- Books List Section -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium text-gray-800">Library Books</h3>
                    <div class="flex items-center gap-4">
                        <!-- Search Box -->
                        <form action="" method="GET" class="flex items-center">
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="Search books..." 
                                   class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" 
                                    class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <!-- Export/Print Buttons -->
                        <div class="flex gap-3">
                            <button onclick="exportBooks()" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-download mr-2"></i>
                                Export
                            </button>
                            <button onclick="printBooks()" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                <i class="fas fa-print mr-2"></i>
                                Print
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Cover</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Author</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Language</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">ISBN</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Publisher</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Published Year</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Pages</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Rating</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($book = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <?php 
                                            $imagePath = $book['local_cover_path'];
                                            // Remove leading slash if present
                                            $imagePath = ltrim($imagePath, '/');
                                            // Add ../ to make the path relative to admin directory
                                            $imagePath = '../' . $imagePath;
                                            ?>
                                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                                 alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                                 class="w-16 h-20 object-cover rounded-lg shadow-sm"
                                                 onerror="this.src='../assets/images/default-book.png'">
                                        </td>
                                        <td class="px-6 py-4 text-gray-900"><?php echo htmlspecialchars($book['title']); ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($book['author']); ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($book['categories']); ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($book['language']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($book['isbn']); ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($book['publisher']); ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($book['published_year']); ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($book['page_count']); ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1">
                                                <span class="text-yellow-400">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                                <span class="text-gray-600">
                                                    <?php echo htmlspecialchars($book['rating'] ?? 'N/A'); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $book['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo ucfirst($book['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <a href="view-book-details.php?id=<?php echo $book['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit-book.php?id=<?php echo $book['id']; ?>" 
                                                   class="text-yellow-600 hover:text-yellow-800">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteBook(<?php echo $book['id']; ?>)" 
                                                        class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="12" class="px-6 py-4 text-center text-gray-500">
                                        No books found in the library.
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/books.js"></script>

<?php
// Close the database connection
mysqli_close($conn);
?> 