<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirectTo('../login.php');
}

$page_title = "Manage Books";
$page_name = "books";
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
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Manage Books</h1>
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
                <!-- Search Section -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-6 overflow-hidden">
                    <div class="p-6 bg-gradient-to-r from-blue-500 to-indigo-600">
                        <h2 class="text-2xl font-bold text-white mb-2">Search Books</h2>
                        <p class="text-blue-100">Search for books by title, author, or ISBN</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Search Type -->
                            <div class="form-group">
                                <label class="inline-flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Search Type
                                </label>
                                <div class="relative">
                                    <select id="searchType" class="appearance-none w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                        <option value="title">Title</option>
                                        <option value="author">Author</option>
                                        <option value="isbn">ISBN</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Language -->
                            <div class="form-group">
                                <label class="inline-flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                    </svg>
                                    Language
                                </label>
                                <div class="relative">
                                    <select id="language" class="appearance-none w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                        <option value="all">All Languages</option>
                                        <option value="eng">English</option>
                                        <option value="hin">Hindi</option>
                                        <option value="mar">Marathi</option>
                                        <option value="guj">Gujarati</option>
                                        <option value="ben">Bengali</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="form-group">
                                <label class="inline-flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Category
                                </label>
                                <div class="relative">
                                    <select id="category" class="appearance-none w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                        <option value="all">All Categories</option>
                                        <option value="fiction">Fiction</option>
                                        <option value="science">Science</option>
                                        <option value="history">History</option>
                                        <option value="biography">Biography</option>
                                        <option value="technology">Technology</option>
                                        <option value="business">Business</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Input -->
                        <div class="mt-6">
                            <div class="relative">
                                <input type="text" 
                                       id="searchQuery" 
                                       placeholder="Enter book title, author or ISBN..." 
                                       class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                                >
                                <svg class="w-6 h-6 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="mt-6 flex justify-end">
                            <button onclick="searchBooks()" 
                                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search Books
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 mt-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Search Results</h2>
                            <button onclick="fetchSelectedBooks()" 
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Fetch Selected Books
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="px-4 py-3 text-left">
                                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cover</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBN</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="searchResults" class="divide-y divide-gray-200">
                                    <!-- Results will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/books.js"></script>
</body>
</html> 