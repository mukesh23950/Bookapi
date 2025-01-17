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
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <!-- Website Logo -->
                    <div class="flex items-center py-4">
                        <a href="/" class="text-2xl font-bold text-blue-600">
                            Online Library
                        </a>
                    </div>
                    <!-- Primary Navbar items -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="index.php" class="py-4 px-2 text-gray-500 hover:text-blue-600 transition duration-300">Home</a>
                        <a href="books.php" class="py-4 px-2 text-gray-500 hover:text-blue-600 transition duration-300">Books</a>
                        <a href="categories.php" class="py-4 px-2 text-gray-500 hover:text-blue-600 transition duration-300">Categories</a>
                        <a href="about.php" class="py-4 px-2 text-gray-500 hover:text-blue-600 transition duration-300">About</a>
                    </div>
                </div>
                <!-- Secondary Navbar items -->
                <div class="hidden md:flex items-center space-x-3">
                    <!-- Search Bar -->
                    <div class="relative">
                        <input type="text" class="w-64 p-2 pl-8 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" placeholder="Search books...">
                        <svg class="w-4 h-4 absolute left-2.5 top-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 focus:outline-none">
                                <span><?php echo $_SESSION['user_name']; ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute right-0 w-48 mt-2 py-2 bg-white rounded-lg shadow-xl hidden group-hover:block">
                                <a href="<?php echo $_SESSION['user_role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php'; ?>" 
                                   class="block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white">
                                    Dashboard
                                </a>
                                <a href="/logout.php" 
                                   class="block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white">
                                    Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Login/Register Buttons -->
                        <a href="login.php" class="py-2 px-4 text-gray-500 hover:text-blue-600 transition duration-300">Login</a>
                        <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-lg">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="outline-none mobile-menu-button">
                        <svg class="w-6 h-6 text-gray-500 hover:text-blue-600"
                            fill="none" stroke-linecap="round" 
                            stroke-linejoin="round" stroke-width="2" 
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="hidden mobile-menu md:hidden">
            <ul class="px-4 py-3 space-y-2">
                <li><a href="index.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Home</a></li>
                <li><a href="books.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Books</a></li>
                <li><a href="categories.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Categories</a></li>
                <li><a href="about.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">About</a></li>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="login.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Login</a></li>
                    <li><a href="register.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Register</a></li>
                <?php else: ?>
                    <li><a href="dashboard.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Dashboard</a></li>
                    <li><a href="logout.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</body>
</html> 