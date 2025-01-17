<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Online Library'; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-[Poppins] bg-gray-50">

<?php 
// Show navbar only for non-admin pages
if (!str_contains($_SERVER['REQUEST_URI'], '/admin/')): 
?>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <a href="index.php" class="flex items-center text-blue-600 font-bold text-xl">
                        <i class="fas fa-book-reader mr-2"></i>
                        Online Library
                    </a>
                    
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="index.php" class="inline-flex items-center px-1 pt-1 text-gray-700">Home</a>
                        <a href="books.php" class="inline-flex items-center px-1 pt-1 text-gray-700">Books</a>
                        <a href="categories.php" class="inline-flex items-center px-1 pt-1 text-gray-700">Categories</a>
                        <a href="about.php" class="inline-flex items-center px-1 pt-1 text-gray-700">About</a>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <?php if (!isLoggedIn()): ?>
                            <a href="<?php echo getBaseUrl(); ?>login.php" class="text-gray-700 hover:text-gray-900 px-3 py-2">Login</a>
                            <a href="<?php echo getBaseUrl(); ?>register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Register</a>
                        <?php else: ?>
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <a href="<?php echo getBaseUrl(); ?>admin/dashboard.php" class="text-blue-600 hover:text-blue-700">Dashboard</a>
                                <?php endif; ?>
                                <a href="<?php echo getBaseUrl(); ?>logout.php" class="text-red-600 hover:text-red-700">Logout</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>