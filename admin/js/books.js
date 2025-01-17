let currentBooks = [];
let currentPage = 1;
const resultsPerPage = 20;
let totalResults = 0;
let lastSearchQuery = '';
let lastSearchType = '';
let availableLanguages = new Set();
let availableCategories = new Set();

// Show loading modal
function showLoadingModal(message = 'Loading...') {
    // Remove existing modal if any
    closeLoadingModal();
    
    const modalHtml = `
        <div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 flex items-center gap-4">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                <p class="text-gray-700">${message}</p>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeLoadingModal() {
    const loadingModal = document.getElementById('loadingModal');
    if (loadingModal) {
        loadingModal.remove();
    }
}

// Add this function to initialize dropdowns
function initializeDropdowns() {
    const languageSelect = document.getElementById('language');
    const categorySelect = document.getElementById('category');
    
    // Clear existing options except first one
    languageSelect.innerHTML = '<option value="all">All Languages</option>';
    categorySelect.innerHTML = '<option value="all">All Categories</option>';
    
    // Add languages
    Array.from(availableLanguages).sort().forEach(lang => {
        languageSelect.add(new Option(lang, lang));
    });
    
    // Add categories
    Array.from(availableCategories).sort().forEach(cat => {
        categorySelect.add(new Option(cat, cat));
    });
}

async function searchBooks(page = 1) {
    currentPage = page;
    const searchType = document.getElementById('searchType').value;
    const query = document.getElementById('searchQuery').value.trim();
    const language = document.getElementById('language').value;
    const category = document.getElementById('category').value;
    
    if (!query) {
        alert('Please enter a search term');
        return;
    }

    try {
        showLoadingModal('Searching books...');
        
        // Build base search URL based on search type
        let searchUrl;
        if (searchType === 'q') {
            // Global search - will search across all fields
            searchUrl = `https://openlibrary.org/search.json?q=${encodeURIComponent(query)}`;
        } else if (searchType === 'isbn') {
            // ISBN specific search
            searchUrl = `https://openlibrary.org/search.json?isbn=${encodeURIComponent(query)}`;
        } else {
            // Title or Author specific search
            searchUrl = `https://openlibrary.org/search.json?${searchType}=${encodeURIComponent(query)}`;
        }

        // Add common parameters
        searchUrl += `&page=${page}&limit=${resultsPerPage}`;
        
        // Add filters if selected
        if (language !== 'all') {
            searchUrl += `&language=${encodeURIComponent(language)}`;
        }
        if (category !== 'all') {
            searchUrl += `&subject=${encodeURIComponent(category)}`;
        }

        console.log('Search URL:', searchUrl); // Debug log

        const response = await fetch(searchUrl);
        const data = await response.json();
        
        // Process and display results
        totalResults = data.numFound;
        currentBooks = data.docs;
        
        // Update available languages and categories from results
        currentBooks.forEach(book => {
            if (book.language && Array.isArray(book.language)) {
                book.language.forEach(lang => availableLanguages.add(lang));
            }
            if (book.subject && Array.isArray(book.subject)) {
                book.subject.forEach(cat => availableCategories.add(cat));
            }
        });

        displaySearchResults(currentBooks);
        displayPagination();
        
        closeLoadingModal();
    } catch (error) {
        closeLoadingModal();
        console.error('Error:', error);
        alert('Error searching books: ' + error.message);
    }
}

function displaySearchResults(books) {
    currentBooks = books;
    const tbody = document.getElementById('searchResults');
    
    tbody.innerHTML = books.map((book, index) => {
        // Get description text
        let description = '';
        if (book.description) {
            description = book.description;
        } else if (book.first_sentence) {
            description = book.first_sentence;
        } else if (book.text && Array.isArray(book.text)) {
            description = book.text[0];
        } else if (book.notes) {
            description = book.notes;
        }

        // Ensure description is a string
        description = String(description || 'No description available').trim();

        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <input type="checkbox" class="book-select w-4 h-4 rounded border-gray-300" data-index="${index}">
                </td>
                <td class="px-6 py-4">
                    ${book.cover_i ? 
                        `<img src="https://covers.openlibrary.org/b/id/${book.cover_i}-M.jpg" 
                            alt="Book cover" 
                            class="w-16 h-20 object-cover rounded-lg shadow-sm"
                            onerror="this.parentElement.innerHTML='<div class=\'w-16 h-20 bg-gray-100 rounded-lg flex items-center justify-center\'><i class=\'fas fa-book text-gray-400 text-2xl\'></i></div>'">` :
                        `<div class="w-16 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-gray-400 text-2xl"></i>
                        </div>`
                    }
                </td>
                <td class="px-6 py-4 text-gray-900 font-medium">${book.title}</td>
                <td class="px-6 py-4 text-gray-600">${book.author_name?.[0] || 'Unknown'}</td>
                <td class="px-6 py-4">
                    <div class="w-64 text-sm text-gray-600 line-clamp-2">${description}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${book.language?.[0] || 'Unknown'}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-600">${book.isbn?.[0] || 'N/A'}</td>
                <td class="px-6 py-4 text-gray-600">${book.first_publish_year || 'N/A'}</td>
                <td class="px-6 py-4 text-gray-600">${book.number_of_pages_median || book.number_of_pages || 'N/A'}</td>
                <td class="px-6 py-4">
                    <button onclick="viewBookDetails(${index})" 
                            class="text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-eye mr-1"></i> View
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function showBookModal(modalHtml) {
    closeBookModal(); // Close any existing modal first
    const modalContainer = document.createElement('div');
    modalContainer.id = 'bookModal';
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer);
}

function closeBookModal() {
    const modal = document.getElementById('bookModal');
    if (modal) {
        modal.remove();
    }
    // Remove event listener
    document.removeEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeBookModal();
    });
}

function viewBookDetails(index) {
    const book = currentBooks[index];
    if (!book) return;

    // Get description using same exact logic as search results
    let description = '';
    if (book.description) {
        description = book.description;
    } else if (book.first_sentence) {
        description = book.first_sentence;
    } else if (book.text && Array.isArray(book.text)) {
        description = book.text[0];
    } else if (book.notes) {
        description = book.notes;
    }

    // Debug log
    console.log('Modal Book Data:', book);
    console.log('Modal Description:', description);

    // Ensure description is a string
    description = String(description || 'No description available').trim();

    const modalHtml = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="bookModal">
            <div class="bg-white rounded-lg max-w-2xl w-full mx-4">
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Book Details</h3>
                    <button onclick="closeBookModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="flex gap-6">
                        <div class="w-32 h-40 bg-gray-100 rounded-lg flex items-center justify-center">
                            ${book.cover_i ? `
                                <img src="https://covers.openlibrary.org/b/id/${book.cover_i}-L.jpg" 
                                    alt="Book cover"
                                    class="w-32 h-40 object-cover rounded-lg shadow-sm"
                                    onerror="this.parentElement.innerHTML='<i class=\'fas fa-book text-gray-400 text-3xl\'></i>'">
                            ` : `
                                <i class="fas fa-book text-gray-400 text-3xl"></i>
                            `}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">${book.title}</h4>
                            <p class="text-gray-600 mb-1">Author: ${book.author_name?.[0] || 'Unknown Author'}</p>
                            <p class="text-gray-600 mb-1">ISBN: ${book.isbn?.[0] || 'N/A'}</p>
                            <p class="text-gray-600 mb-1">Published: ${book.first_publish_year || 'N/A'}</p>
                            <p class="text-gray-600 mb-1">Language: ${book.language?.[0] || 'Unknown'}</p>
                            <p class="text-gray-600 mb-1">Publisher: ${book.publisher?.[0] || 'N/A'}</p>
                            <p class="text-gray-600 mb-1">Pages: ${book.number_of_pages_median || book.number_of_pages || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h5 class="font-bold text-gray-900 mb-2">Description</h5>
                        <p class="text-gray-600">${description}</p>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button onclick="saveBookFromModal(${index})" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Save Book
                    </button>
                    <button onclick="closeBookModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Function to save book from modal
async function saveBookFromModal(index) {
    const book = currentBooks[index];
    
    try {
        showLoadingModal('Saving book...');
        
        // Get description from modal
        const modalDescription = document.querySelector('#bookModal .text-gray-600').textContent;
        
        // Convert page count to integer or null
        const pageCount = book.number_of_pages_median || book.number_of_pages;
        const parsedPageCount = pageCount ? parseInt(pageCount) : null;
        
        const bookData = {
            books: [{
                title: book.title,
                author: book.author_name?.[0] || '',
                isbn: book.isbn?.[0] || '',
                published_year: book.first_publish_year?.toString() || '',
                cover_id: book.cover_i?.toString() || '',
                local_cover_path: book.cover_i ? 
                    `/assets/images/covers/${book.cover_i}.jpg` : '',
                description: modalDescription,
                publisher: book.publisher?.[0] || '',
                page_count: parsedPageCount, // Send as integer or null
                categories: book.subject?.[0] || '',
                rating: null,
                language: book.language?.[0] || '',
                status: 'active'
            }]
        };

        console.log('Sending book data:', bookData); // Debug log

        const response = await fetch('../api/books/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookData)
        });

        const result = await response.json();
        console.log('Save result:', result); // Debug log
        
        closeLoadingModal();
        closeBookModal();
        
        if (result.success) {
            alert('Book saved successfully!');
        } else {
            throw new Error(result.message);
        }

    } catch (error) {
        closeLoadingModal();
        closeBookModal();
        console.error('Error:', error);
        alert('Error saving book: ' + error.message);
    }
}

async function saveSelectedBooks() {
    const selected = document.querySelectorAll('.book-select:checked');
    
    if (selected.length === 0) {
        alert('Please select at least one book');
        return;
    }

    try {
        showLoadingModal('Saving selected books...');
        
        const books = Array.from(selected).map(checkbox => {
            const index = parseInt(checkbox.dataset.index);
            const book = currentBooks[index];
            
            // Get description from the table cell
            const row = checkbox.closest('tr');
            const descriptionCell = row.querySelector('td:nth-child(5)');
            const description = descriptionCell.textContent.trim();
            
            // Convert page count to integer or null
            const pageCount = book.number_of_pages_median || book.number_of_pages;
            const parsedPageCount = pageCount ? parseInt(pageCount) : null;
            
            return {
                title: book.title,
                author: book.author_name?.[0] || '',
                isbn: book.isbn?.[0] || '',
                published_year: book.first_publish_year?.toString() || '',
                cover_id: book.cover_i?.toString() || '',
                local_cover_path: book.cover_i ? 
                    `/assets/images/covers/${book.cover_i}.jpg` : '',
                description: description,
                publisher: book.publisher?.[0] || '',
                page_count: parsedPageCount, // Send as integer or null
                categories: book.subject?.[0] || '',
                rating: null,
                language: book.language?.[0] || '',
                status: 'active'
            };
        });

        console.log('Sending books data:', { books }); // Debug log

        const response = await fetch('../api/books/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ books: books })
        });

        const result = await response.json();
        console.log('Save result:', result); // Debug log
        
        closeLoadingModal();
        
        if (result.success) {
            document.querySelectorAll('.book-select').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            
            alert(`${result.saved.length} books saved successfully!` + 
                  (result.duplicates.length > 0 ? `\n${result.duplicates.length} duplicates skipped.` : ''));
        } else {
            throw new Error(result.message);
        }

    } catch (error) {
        closeLoadingModal();
        console.error('Error:', error);
        alert('Error saving books: ' + error.message);
    }
}

// Initialize select all functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', (e) => {
            document.querySelectorAll('.book-select').forEach(cb => cb.checked = e.target.checked);
        });
    }

    // Add enter key support for search
    document.getElementById('searchQuery').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBooks();
        }
    });

    // Add change listeners for filters
    document.getElementById('language').addEventListener('change', () => searchBooks(1));
    document.getElementById('category').addEventListener('change', () => searchBooks(1));
    document.getElementById('searchType').addEventListener('change', function() {
        const searchInput = document.getElementById('searchQuery');
        switch(this.value) {
            case 'q':
                searchInput.placeholder = 'Search across all fields...';
                break;
            case 'isbn':
                searchInput.placeholder = 'Enter ISBN...';
                break;
            case 'author':
                searchInput.placeholder = 'Enter author name...';
                break;
            case 'title':
                searchInput.placeholder = 'Enter book title...';
                break;
        }
    });

    // Add search button click handler
    document.querySelector('button[onclick="searchBooks()"]').addEventListener('click', () => searchBooks(1));
});

function displayPagination() {
    const totalPages = Math.ceil(totalResults / resultsPerPage);
    const paginationContainer = document.getElementById('pagination');
    
    if (!paginationContainer) {
        // Create pagination container if it doesn't exist
        const container = document.createElement('div');
        container.id = 'pagination';
        container.className = 'flex justify-center items-center gap-2 mt-6';
        document.querySelector('.overflow-x-auto').appendChild(container);
    }

    let paginationHTML = `
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">
                Showing ${((currentPage - 1) * resultsPerPage) + 1} - 
                ${Math.min(currentPage * resultsPerPage, totalResults)} 
                of ${totalResults} results
            </span>
            <div class="flex items-center gap-2">
    `;

    // Previous button
    paginationHTML += `
        <button onclick="searchBooks(${currentPage - 1})" 
                class="px-3 py-1 rounded-lg border ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-gray-50'}"
                ${currentPage === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }

    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button onclick="searchBooks(${i})" 
                    class="px-3 py-1 rounded-lg border ${i === currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-50'}">
                ${i}
            </button>
        `;
    }

    // Next button
    paginationHTML += `
        <button onclick="searchBooks(${currentPage + 1})" 
                class="px-3 py-1 rounded-lg border ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-gray-50'}"
                ${currentPage === totalPages ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationHTML += `
            </div>
        </div>
    `;

    paginationContainer.innerHTML = paginationHTML;
}