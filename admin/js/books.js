let currentBooks = [];

async function searchBooks() {
    const searchType = document.getElementById('searchType').value;
    const searchQuery = document.getElementById('searchQuery').value;
    const language = document.getElementById('language').value;
    const category = document.getElementById('category').value;

    if (!searchQuery) {
        alert('Please enter a search term');
        return;
    }

    try {
        document.getElementById('searchResults').innerHTML = '<tr><td colspan="8" class="text-center py-4">Loading...</td></tr>';

        // Build the Open Library API URL based on search type
        let apiUrl = 'https://openlibrary.org/search.json?';
        
        // Add search parameters based on search type
        switch(searchType) {
            case 'title':
                apiUrl += `title=${encodeURIComponent(searchQuery)}`;
                break;
            case 'author':
                apiUrl += `author=${encodeURIComponent(searchQuery)}`;
                break;
            case 'isbn':
                apiUrl += `isbn=${encodeURIComponent(searchQuery)}`;
                break;
        }

        // Add language filter if specified
        if (language !== 'all') {
            apiUrl += `&language=${language}`;
        }

        // Add category/subject filter if specified
        if (category !== 'all') {
            apiUrl += `&subject=${category}`;
        }

        // Limit results
        apiUrl += '&limit=10';

        const response = await fetch(apiUrl);
        const data = await response.json();
        
        // Filter results based on selected language
        let filteredBooks = data.docs;
        if (language !== 'all') {
            filteredBooks = filteredBooks.filter(book => {
                return book.language && book.language.includes(language);
            });
        }

        // Store filtered books for later use
        currentBooks = filteredBooks;
        
        // Display results
        displaySearchResults(filteredBooks);

    } catch (error) {
        console.error('Error fetching books:', error);
        document.getElementById('searchResults').innerHTML = 
            '<tr><td colspan="8" class="text-center py-4 text-red-600">Error fetching books. Please try again.</td></tr>';
    }
}

function displaySearchResults(books) {
    const tbody = document.getElementById('searchResults');
    tbody.innerHTML = '';

    if (!books || books.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No books found</td></tr>';
        return;
    }

    // Filter out books without covers
    const booksWithCovers = books.filter(book => book.cover_i);

    if (booksWithCovers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No books found with covers</td></tr>';
        return;
    }

    booksWithCovers.forEach((book, index) => {
        const languageName = getLanguageName(book.language ? book.language[0] : '');
        
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors duration-200';
        tr.innerHTML = `
            <td class="px-4 py-4">
                <input type="checkbox" class="book-select rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" data-index="${index}">
            </td>
            <td class="px-4 py-4">
                <img src="https://covers.openlibrary.org/b/id/${book.cover_i}-M.jpg" 
                     class="w-16 h-20 object-cover rounded-lg shadow-sm" 
                     alt="Book cover">
            </td>
            <td class="px-4 py-4 font-medium text-gray-900">${book.title}</td>
            <td class="px-4 py-4 text-gray-700">${book.author_name ? book.author_name[0] : 'Unknown'}</td>
            <td class="px-4 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getLanguageClass(languageName)}">
                    ${languageName}
                </span>
            </td>
            <td class="px-4 py-4 text-gray-700">${book.isbn ? book.isbn[0] : 'N/A'}</td>
            <td class="px-4 py-4 text-gray-700">${book.first_publish_year || 'N/A'}</td>
            <td class="px-4 py-4">
                <button onclick="viewBookDetails('${book.key}')" 
                        class="inline-flex items-center px-3 py-1 border border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Helper function to get language name from code
function getLanguageName(langCode) {
    const languages = {
        'eng': 'English',
        'hin': 'Hindi',
        'mar': 'Marathi',
        'guj': 'Gujarati',
        'ben': 'Bengali',
        'san': 'Sanskrit',
        'tam': 'Tamil',
        'tel': 'Telugu',
        'kan': 'Kannada',
        'mal': 'Malayalam'
    };
    return languages[langCode] || langCode || 'Unknown';
}

// Helper function to get language badge class
function getLanguageClass(language) {
    const classes = {
        'English': 'bg-blue-100 text-blue-800',
        'Hindi': 'bg-green-100 text-green-800',
        'Marathi': 'bg-yellow-100 text-yellow-800',
        'Gujarati': 'bg-purple-100 text-purple-800',
        'Bengali': 'bg-pink-100 text-pink-800',
        'Sanskrit': 'bg-red-100 text-red-800',
        'Tamil': 'bg-indigo-100 text-indigo-800',
        'Telugu': 'bg-orange-100 text-orange-800',
        'Kannada': 'bg-teal-100 text-teal-800',
        'Malayalam': 'bg-cyan-100 text-cyan-800'
    };
    return classes[language] || 'bg-gray-100 text-gray-800';
}

async function fetchSelectedBooks() {
    const selected = document.querySelectorAll('.book-select:checked');
    
    if (selected.length === 0) {
        alert('Please select at least one book');
        return;
    }

    const books = Array.from(selected).map(checkbox => {
        const index = checkbox.dataset.index;
        const book = currentBooks[index];
        return {
            title: book.title,
            author: book.author_name ? book.author_name[0] : 'Unknown',
            isbn: book.isbn ? book.isbn[0] : null,
            published_year: book.first_publish_year || null,
            cover_id: book.cover_i || null,
            description: book.description || null,
            language: book.language ? book.language[0] : null
        };
    });

    try {
        const response = await fetch('../api/save-books.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(books)
        });

        const result = await response.json();
        
        if (result.success) {
            alert('Selected books have been saved successfully!');
            // Clear selections
            document.querySelectorAll('.book-select').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
        } else {
            alert('Error saving books: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving books:', error);
        alert('Error saving books to database');
    }
}

// Book details modal function
async function viewBookDetails(bookKey) {
    try {
        showModal('Loading book details...');
        
        // Get book data from current books array
        const book = currentBooks.find(b => b.key === bookKey);
        
        if (!book) {
            throw new Error('Book not found');
        }

        // Fetch additional book details including description
        const detailsResponse = await fetch(`https://openlibrary.org${bookKey}.json`);
        const additionalDetails = await detailsResponse.json();

        // Create an array of available details
        const details = [];

        // Add description if available from additional details
        if (additionalDetails.description) {
            const description = typeof additionalDetails.description === 'object' ? 
                additionalDetails.description.value : additionalDetails.description;
            details.push(`
                <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 leading-relaxed">${description}</dd>
                </div>
            `);
        }

        // Add author if available
        if (book.author_name && book.author_name.length > 0) {
            details.push(`
                <div>
                    <dt class="text-sm font-medium text-gray-500">Author</dt>
                    <dd class="mt-1 text-sm text-gray-900">${book.author_name.join(', ')}</dd>
                </div>
            `);
        }

        // Add publish year if available
        if (book.first_publish_year) {
            details.push(`
                <div>
                    <dt class="text-sm font-medium text-gray-500">Published Year</dt>
                    <dd class="mt-1 text-sm text-gray-900">${book.first_publish_year}</dd>
                </div>
            `);
        }

        // Add ISBN if available
        if (book.isbn && book.isbn.length > 0) {
            details.push(`
                <div>
                    <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                    <dd class="mt-1 text-sm text-gray-900">${book.isbn[0]}</dd>
                </div>
            `);
        }

        // Add languages if available
        if (book.language && book.language.length > 0) {
            details.push(`
                <div>
                    <dt class="text-sm font-medium text-gray-500">Languages</dt>
                    <dd class="mt-1 text-sm text-gray-900">${book.language.map(lang => getLanguageName(lang)).join(', ')}</dd>
                </div>
            `);
        }

        // Add publishers if available from additional details
        if (additionalDetails.publishers && additionalDetails.publishers.length > 0) {
            details.push(`
                <div>
                    <dt class="text-sm font-medium text-gray-500">Publishers</dt>
                    <dd class="mt-1 text-sm text-gray-900">${additionalDetails.publishers.join(', ')}</dd>
                </div>
            `);
        }

        // Add number of pages if available from additional details
        if (additionalDetails.number_of_pages) {
            details.push(`
                <div>
                    <dt class="text-sm font-medium text-gray-500">Number of Pages</dt>
                    <dd class="mt-1 text-sm text-gray-900">${additionalDetails.number_of_pages}</dd>
                </div>
            `);
        }

        const modalContent = `
            <div class="bg-white rounded-xl shadow-lg max-w-2xl mx-auto overflow-auto max-h-[90vh]">
                <div class="flex items-start p-6 border-b sticky top-0 bg-white">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">${book.title}</h3>
                        ${book.author_name ? `
                            <p class="mt-1 text-sm text-gray-500">${book.author_name[0]}</p>
                        ` : ''}
                    </div>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <img src="https://covers.openlibrary.org/b/id/${book.cover_i}-L.jpg" 
                                 class="w-full rounded-lg shadow-lg" 
                                 alt="Book cover">
                        </div>
                        <div>
                            <dl class="space-y-4">
                                ${details.join('')}
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        `;

        updateModal(modalContent);

    } catch (error) {
        console.error('Error showing book details:', error);
        updateModal(`
            <div class="bg-white rounded-xl p-6 text-center">
                <p class="text-red-600">Error loading book details. Please try again.</p>
                <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-gray-200 rounded-lg">Close</button>
            </div>
        `);
    }
}

// Helper function to get author names
async function getAuthorNames(authors) {
    try {
        const authorPromises = authors.map(async (author) => {
            const response = await fetch(`https://openlibrary.org${author.author.key}.json`);
            const authorData = await response.json();
            return authorData.name;
        });
        const authorNames = await Promise.all(authorPromises);
        return authorNames.join(', ');
    } catch (error) {
        return 'Unknown Author';
    }
}

// Modal functions
function showModal(content) {
    let modal = document.getElementById('bookModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'bookModal';
        modal.className = 'fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50';
        document.body.appendChild(modal);
    }
    modal.innerHTML = `
        <div class="bg-white rounded-xl p-6">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-2 text-center">${content}</p>
        </div>
    `;
    modal.style.display = 'flex';
}

function updateModal(content) {
    const modal = document.getElementById('bookModal');
    if (modal) {
        modal.innerHTML = content;
    }
}

function closeModal() {
    const modal = document.getElementById('bookModal');
    if (modal) {
        modal.remove();
    }
}

// Handle select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.book-select');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});

// Add event listener for search button
document.getElementById('searchQuery').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchBooks();
    }
});

// Update search type options
const searchTypeOptions = {
    title: {
        placeholder: "Enter book title...",
        icon: "book"
    },
    author: {
        placeholder: "Enter author name...",
        icon: "user"
    },
    isbn: {
        placeholder: "Enter ISBN number...",
        icon: "barcode"
    }
};

// Update placeholder text when search type changes
document.getElementById('searchType').addEventListener('change', function() {
    const searchType = this.value;
    const searchInput = document.getElementById('searchQuery');
    searchInput.placeholder = searchTypeOptions[searchType].placeholder;
}); 