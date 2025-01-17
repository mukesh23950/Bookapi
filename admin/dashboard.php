<?php
require_once '../includes/config.php';
redirectIfNotAdmin();

// Fetch basic statistics
$stats = [
    'total_books' => $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(),
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
];

// Fetch recent books
$recent_books = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 5")->fetchAll();

$page_title = "Admin Dashboard";
include '../includes/header.php';
?>

<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Dashboard Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800">Total Books</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total_books']; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800">Total Users</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total_users']; ?></p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="add-book.php" class="bg-blue-600 text-white rounded-lg px-4 py-2 text-center hover:bg-blue-700 transition duration-300">
                    Add New Book
                </a>
                <a href="manage-users.php" class="bg-green-600 text-white rounded-lg px-4 py-2 text-center hover:bg-green-700 transition duration-300">
                    Manage Users
                </a>
                <a href="settings.php" class="bg-gray-600 text-white rounded-lg px-4 py-2 text-center hover:bg-gray-700 transition duration-300">
                    Settings
                </a>
            </div>
        </div>

        <!-- Recent Books -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Books</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ISBN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recent_books as $book): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($book['title']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($book['author']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($book['isbn']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="edit-book.php?id=<?php echo $book['id']; ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                                <a href="delete-book.php?id=<?php echo $book['id']; ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 