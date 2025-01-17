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
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-bold mb-4">Search Books</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Search Type</label>
                            <select id="searchType" class="w-full rounded-lg border-gray-300">
                                <option value="title">Title</option>
                                <option value="author">Author</option>
                                <option value="isbn">ISBN</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Language</label>
                            <select id="language" class="w-full rounded-lg border-gray-300">
                                <option value="eng">English</option>
                                <option value="hin">Hindi</option>
                                <option value="mar">Marathi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Category</label>
                            <select id="category" class="w-full rounded-lg border-gray-300">
                                <option value="fiction">Fiction</option>
                                <option value="science">Science</option>
                                <option value="history">History</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <input type="text" id="searchQuery" placeholder="Enter search term..." 
                               class="w-full rounded-lg border-gray-300 mb-4">
                        <button onclick="searchBooks()" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Search Books
                        </button>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold">Search Results</h2>
                        <button onclick="fetchSelectedBooks()" 
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                            Fetch Selected Books
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2"><input type="checkbox" id="selectAll"></th>
                                    <th class="px-4 py-2 text-left">Cover</th>
                                    <th class="px-4 py-2 text-left">Title</th>
                                    <th class="px-4 py-2 text-left">Author</th>
                                    <th class="px-4 py-2 text-left">ISBN</th>
                                    <th class="px-4 py-2 text-left">Published</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="searchResults">
                                <!-- Results will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentBooks = [];

        async function searchBooks() {
            const searchType = document.getElementById('searchType').value;
            const query = document.getElementById('searchQuery').value;
            const language = document.getElementById('language').value;
            
            if (!query) return;

            try {
                const response = await fetch(`https://openlibrary.org/search.json?q=${encodeURIComponent(query)}&limit=10&language=${language}`);
                const data = await response.json();
                currentBooks = data.docs;
                displayResults(data.docs);
            } catch (error) {
                console.error('Error fetching books:', error);
            }
        }

        function displayResults(books) {
            const tbody = document.getElementById('searchResults');
            tbody.innerHTML = '';

            books.forEach((book, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-2">
                        <input type="checkbox" class="book-select" data-index="${index}">
                    </td>
                    <td class="px-4 py-2">
                        <img src="https://covers.openlibrary.org/b/id/${book.cover_i}-S.jpg" 
                             class="w-16 h-20 object-cover" onerror="this.src='../assets/images/no-cover.png'">
                    </td>
                    <td class="px-4 py-2">${book.title}</td>
                    <td class="px-4 py-2">${book.author_name?.[0] || 'Unknown'}</td>
                    <td class="px-4 py-2">${book.isbn?.[0] || 'N/A'}</td>
                    <td class="px-4 py-2">${book.first_publish_year || 'N/A'}</td>
                    <td class="px-4 py-2">
                        <button onclick="viewBookDetails(${index})" 
                                class="text-blue-600 hover:text-blue-800">
                            View Details
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        async function fetchSelectedBooks() {
            const selected = document.querySelectorAll('.book-select:checked');
            const books = Array.from(selected).map(checkbox => {
                const index = checkbox.dataset.index;
                return currentBooks[index];
            });

            // Prepare books data for database
            const booksData = books.map(book => ({
                title: book.title,
                author: book.author_name?.[0] || 'Unknown',
                isbn: book.isbn?.[0] || null,
                published_year: book.first_publish_year || null,
                cover_id: book.cover_i || null,
                description: book.description || null,
                language: book.language?.[0] || null
            }));

            try {
                const response = await fetch('../api/save-books.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(booksData)
                });

                const result = await response.json();
                if (result.success) {
                    alert('Selected books have been saved to database!');
                } else {
                    alert('Error saving books: ' + result.message);
                }
            } catch (error) {
                console.error('Error saving books:', error);
                alert('Error saving books to database');
            }
        }

        function viewBookDetails(index) {
            const book = currentBooks[index];
            // Implement a modal or redirect to show full book details
        }

        // Handle select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.book-select');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>
</body>
</html> 