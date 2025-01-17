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
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-3xl p-8 mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">Search Books</h2>
                <p class="text-blue-100 mb-8">Search for books by title, author, or ISBN</p>

                <div class="bg-white rounded-3xl p-8">
                    <div class="flex items-center gap-8 mb-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-search text-blue-600"></i>
                                <span class="text-sm text-gray-600">Search Type</span>
                            </div>
                            <select id="searchType" class="w-full bg-gray-50 border-0 rounded-lg py-3 px-4">
                                <option value="title">Title</option>
                                <option value="author">Author</option>
                                <option value="isbn">ISBN</option>
                            </select>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-globe text-blue-600"></i>
                                <span class="text-sm text-gray-600">Language</span>
                            </div>
                            <select id="language" class="w-full bg-gray-50 border-0 rounded-lg py-3 px-4">
                                <option value="all">All Languages</option>
                                <option value="eng">English</option>
                                <option value="hin">Hindi</option>
                                <option value="spa">Spanish</option>
                            </select>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-tag text-blue-600"></i>
                                <span class="text-sm text-gray-600">Category</span>
                            </div>
                            <select id="category" class="w-full bg-gray-50 border-0 rounded-lg py-3 px-4">
                                <option value="all">All Categories</option>
                                <option value="fiction">Fiction</option>
                                <option value="non-fiction">Non-Fiction</option>
                                <option value="science">Science</option>
                                <option value="technology">Technology</option>
                            </select>
                        </div>
                    </div>

                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchQuery" 
                               class="w-full bg-gray-50 border-0 rounded-lg pl-12 pr-32 py-3"
                               placeholder="Enter book title, author or ISBN">
                        <button id="searchButton"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700">
                            Search Books
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium text-gray-800">Search Results</h3>
                    <button id="fetchSelectedButton"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>
                        Fetch Selected Books
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300">
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">COVER</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">TITLE</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">AUTHOR</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">LANGUAGE</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">ISBN</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">PUBLISHED</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="searchResults" class="divide-y divide-gray-100">
                            <!-- Results will be populated by JavaScript -->
                        </tbody>
                    </table>

                    <!-- Pagination Controls -->
                    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                        <div class="flex flex-1 justify-between sm:hidden">
                            <button id="prevPage" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</button>
                            <button id="nextPage" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</button>
                        </div>
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span id="startRecord" class="font-medium">0</span> to <span id="endRecord" class="font-medium">0</span> of <span id="totalRecords" class="font-medium">0</span> results
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <select id="itemsPerPage" class="rounded-md border-gray-300 py-1.5 text-sm font-medium text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                </select>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <button id="prevPage" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </button>
                                    <div id="pageNumbers" class="flex">
                                        <!-- Page numbers will be populated by JavaScript -->
                                    </div>
                                    <button id="nextPage" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal Template -->
<div id="loadingModalTemplate" class="hidden">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mx-auto"></div>
            <p class="mt-4 text-gray-600">Loading...</p>
        </div>
    </div>
</div>

<script src="js/books.js"></script>

<?php include '../includes/footer.php'; ?>