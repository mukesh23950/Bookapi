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
<body class="font-[Poppins] flex flex-col min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Primary Nav -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="text-2xl font-bold text-blue-600 flex items-center space-x-2">
                            <i class="fas fa-book-reader"></i>
                            <span>Online Library</span>
                        </a>
                    </div>
                    <!-- Desktop Navigation -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="index.php" 
                           class="<?php echo $_SERVER['PHP_SELF'] == '/index.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> 
                           inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Home
                        </a>
                        <a href="books.php"
                           class="<?php echo $_SERVER['PHP_SELF'] == '/books.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> 
                           inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Books
                        </a>
                        <a href="categories.php"
                           class="<?php echo $_SERVER['PHP_SELF'] == '/categories.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> 
                           inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Categories
                        </a>
                        <a href="about.php"
                           class="<?php echo $_SERVER['PHP_SELF'] == '/about.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> 
                           inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            About
                        </a>
                    </div>
                </div>

                <!-- Search Bar and Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <!-- Search Bar -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <input type="text" 
                                   class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-2.5" 
                                   placeholder="Search books...">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Auth Buttons -->
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Sign in
                        </a>
                        <a href="login.php?tab=register" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Register
                        </a>
                    <?php else: ?>
                        <!-- User Dropdown -->
                        <div class="relative inline-block text-left">
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                                    id="user-menu-button">
                                <span class="mr-2"><?php echo $_SESSION['user_name']; ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100" 
                                 id="user-dropdown">
                                <div class="py-1">
                                    <a href="<?php echo $_SESSION['user_role'] === 'admin' ? 'admin/dashboard.php' : 'profile.php'; ?>" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?php echo $_SESSION['user_role'] === 'admin' ? 'Dashboard' : 'Profile'; ?>
                                    </a>
                                </div>
                                <div class="py-1">
                                    <a href="logout.php" 
                                       class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Sign out
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile menu button -->
                    <button type="button" 
                            class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" 
                            id="mobile-menu-button">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="index.php" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Home</a>
                <a href="books.php" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Books</a>
                <a href="categories.php" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Categories</a>
                <a href="about.php" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">About</a>
            </div>
        </div>
    </nav>

    <script>
        // Toggle mobile menu
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Toggle user dropdown
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        
        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', function() {
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html> 