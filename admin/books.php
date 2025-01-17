<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirectTo('../login.php');
}

$page_title = "Manage Books";
$page_name = "books";

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
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 mb-6">
                <div class="flex flex-col gap-2 mb-4">
                    <h2 class="text-2xl font-bold text-white">Search Books</h2>
                    <p class="text-blue-100/80 text-sm">Discover millions of books in our library</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5">
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="group">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/10">
                                    <i class="fas fa-search text-white text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-white">Search Type</span>
                            </div>
                            <select id="searchType" class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-2.5 px-3 text-sm hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="q">Global Search</option>
                                <option value="title">Title</option>
                                <option value="author">Author</option>
                                <option value="isbn">ISBN</option>
                            </select>
                        </div>

                        <div class="group">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/10">
                                    <i class="fas fa-globe text-white text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-white">Language</span>
                            </div>
                            <select id="language" class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-2.5 px-3 text-sm hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="all">All Languages</option>
                                <option value="eng">English</option>
                                <option value="hin">Hindi</option>
                                <option value="spa">Spanish</option>
                                <option value="fre">French</option>
                                <option value="ger">German</option>
                                <option value="rus">Russian</option>
                                <option value="chi">Chinese</option>
                                <option value="jpn">Japanese</option>
                            </select>
                        </div>

                        <div class="group">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/10">
                                    <i class="fas fa-tag text-white text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-white">Category</span>
                            </div>
                            <select id="category" class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-2.5 px-3 text-sm hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="all">All Categories</option>
                                <option value="fiction">Fiction</option>
                                <option value="nonfiction">Non-Fiction</option>
                                <option value="science">Science</option>
                                <option value="history">History</option>
                                <option value="biography">Biography</option>
                                <option value="technology">Technology</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-2 shadow-lg">
                        <div class="relative">
                            <input type="text" id="searchQuery" 
                                   class="w-full bg-gray-50 border-0 rounded-lg pl-11 pr-32 py-3 text-sm"
                                   placeholder="Search for books...">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button onclick="searchBooks()" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                                Search Books
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium text-gray-800">Search Results</h3>
                    <button onclick="saveSelectedBooks()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i>
                        Save Selected Books
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300">
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Cover</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Author</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Language</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">ISBN</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Published</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Pages</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="searchResults" class="divide-y divide-gray-100">
                            <!-- Example row structure for JavaScript -->
                            <!--
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="book-select w-4 h-4 rounded border-gray-300">
                                </td>
                                <td class="px-6 py-4">
                                    <img src="cover_url" class="w-16 h-20 object-cover rounded-lg shadow-sm">
                                </td>
                                <td class="px-6 py-4 text-gray-900">Book Title</td>
                                <td class="px-6 py-4 text-gray-600">Author Name</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        English
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">ISBN Number</td>
                                <td class="px-6 py-4 text-gray-600">Year</td>
                                <td class="px-6 py-4">
                                    <button class="text-blue-600 hover:text-blue-800 font-medium">
                                        View
                                    </button>
                                </td>
                            </tr>
                            -->
                        </tbody>
                    </table>
                    <!-- Pagination will be inserted here -->
                    <div id="pagination" class="flex justify-center items-center gap-2 mt-6"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/books.js"></script>

