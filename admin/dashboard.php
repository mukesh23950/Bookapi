<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirectTo('../login.php');
}

$page_title = "Admin Dashboard";
$page_name = "dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Online Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
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
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Dashboard</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <input type="text" placeholder="Search..." 
                                   class="w-64 pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
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
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-book text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Books</h3>
                                <p class="text-2xl font-bold text-gray-800">0</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Users</h3>
                                <p class="text-2xl font-bold text-gray-800">1</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-tags text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Categories</h3>
                                <p class="text-2xl font-bold text-gray-800">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow mb-8">
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="add-book.php" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus"></i>
                                Add New Book
                            </a>
                            <a href="users.php" class="flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-user-cog"></i>
                                Manage Users
                            </a>
                            <a href="settings.php" class="flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Books Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Recent Books</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left bg-gray-50">
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Title</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Author</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">ISBN</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <!-- Books will be listed here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.w-64').classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html> 