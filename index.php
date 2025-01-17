<?php
$page_title = "Home - Online Library";
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Welcome to Online Library</h1>
            <p class="text-xl mb-8">Discover millions of books at your fingertips</p>
            <a href="/books" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                Browse Books
            </a>
        </div>
    </div>
</div>

<!-- Featured Books Section -->
<div class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold text-gray-800 mb-8">Featured Books</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Book Card Example -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
            <img src="https://via.placeholder.com/300x400" alt="Book Cover" class="w-full h-64 object-cover">
            <div class="p-4">
                <h3 class="text-xl font-semibold mb-2">Book Title</h3>
                <p class="text-gray-600 text-sm mb-2">Author Name</p>
                <div class="flex justify-between items-center">
                    <span class="text-blue-600 font-semibold">ISBN: 1234567890</span>
                    <a href="#" class="text-blue-600 hover:text-blue-800">Details →</a>
                </div>
            </div>
        </div>
        <!-- Add more book cards here -->
    </div>
</div>

<!-- Categories Section -->
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Popular Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="#" class="bg-white p-4 rounded-lg text-center shadow hover:shadow-md transition duration-300">
                <i class="fas fa-book text-2xl text-blue-600 mb-2"></i>
                <p class="text-gray-800">Fiction</p>
            </a>
            <!-- Add more category cards -->
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 