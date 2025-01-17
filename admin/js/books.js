// Global variables
let currentBooks = [];
let searchCache = new Map();
let isLoading = false;
let currentPage = 1;
let itemsPerPage = 10;
let totalPages = 0;
let allBooksData = []; // Store all fetched books

// Default cover image (base64 encoded)
const defaultCoverImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAEYCAMAAADCuiwhAAAAP1BMVEX///+tra3CwsKpqan7+/vPz8/4+PjLy8v19fW2trby8vLl5eXIyMjd3d3i4uLr6+u8vLzU1NS5ubmxsbGlpaXyg4TBAAACxUlEQVR4nO3c7XKCMBCGYUwCBAhfAd7/rRattZ1xZxeH7jlxn1+O8w7JGiAgPB4AAAAAAAAAAAAAAAAAAAAAAAAAAADwO8s0X4p4vU7XorhM0+rdiyndHRNWJ2U+LH0MIaQY+2XOVSJPHu7lNnVZZc+qrrsuvtxCzMdyTKlrlJhzp0gZqfsuVGXqFu6S8nJKHcNd0lBNqVO4S6omqeQu7TRVK6kc7hLbSYW7zDDNUqEu7TqpUJeVTirU5TWDVKjLW3upUJfXDlKhLq/rpUJdXi8V6tKOUqEubRykQl3eIBXq8qJUqMtrpEJdXlRSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1KhLi9Khbq8KBXq8qJUqMuLUqEuL0qFurwoFeryolSoy4tSoS4vSoW6vCgV6vKiVKjLi1Kh/nf9A/sYQfWy5H+EAAAAAElFTkSuQmCC';

// Show loading modal
function showLoadingModal(message = 'Loading...') {
    if (isLoading) return;
    isLoading = true;
    const template = document.getElementById('loadingModalTemplate');
    if (!template) return;

    const modal = template.cloneNode(true);
    modal.id = 'loadingModal';
    modal.classList.remove('hidden');
    
    const messageElement = modal.querySelector('p');
    if (messageElement) {
        messageElement.textContent = message;
    }
    
    document.body.appendChild(modal);
}

// Close loading modal
function closeLoadingModal() {
    isLoading = false;
    const modal = document.getElementById('loadingModal');
    if (modal) {
        modal.remove();
    }
}

// Get paginated data
function getPaginatedData(data) {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    return data.slice(startIndex, endIndex);
}

// Get book cover URL
function getBookCoverUrl(book) {
    // If book has local cover path, use it
    if (book.local_cover_path) {
        return book.local_cover_path;
    }
    
    // Otherwise use OpenLibrary cover or default
    return book.cover_i 
        ? `https://covers.openlibrary.org/b/id/${book.cover_i}-M.jpg`
        : defaultCoverImage;
}

// Display search results with pagination
function displaySearchResults(books = [], isNewSearch = false) {
    const tbody = document.getElementById('searchResults');
    if (!tbody) return;

    // If it's a new search, update the full dataset
    if (isNewSearch) {
        allBooksData = books;
        currentPage = 1; // Reset to first page on new search
    }
    
    // Store the full results
    currentBooks = allBooksData;
    
    // Calculate total pages
    totalPages = Math.ceil(currentBooks.length / itemsPerPage);
    
    // Get paginated data
    const paginatedBooks = getPaginatedData(currentBooks);
    
    // Create fragment for better performance
    const fragment = document.createDocumentFragment();
    
    if (!currentBooks || currentBooks.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="8" class="text-center py-4">No books found</td>';
        fragment.appendChild(tr);
    } else {
        paginatedBooks.forEach((book, index) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';
            
            const coverUrl = getBookCoverUrl(book);
                
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <input type="checkbox" class="book-select w-4 h-4 rounded border-gray-300" 
                           data-index="${(currentPage - 1) * itemsPerPage + index}">
                </td>
                <td class="px-6 py-4">
                    <img src="${coverUrl}" 
                         class="w-16 h-20 object-cover rounded-lg shadow-sm"
                         onerror="this.src='${defaultCoverImage}'">
                </td>
                <td class="px-6 py-4 text-gray-900 font-medium">${book.title || 'Unknown Title'}</td>
                <td class="px-6 py-4 text-gray-600">${book.author_name?.[0] || 'Unknown'}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                        book.language === 'eng' ? 'bg-blue-100 text-blue-800' : 
                        'bg-gray-100 text-gray-800'
                    }">
                        ${book.language || 'Unknown'}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-600">${book.isbn?.[0] || 'N/A'}</td>
                <td class="px-6 py-4 text-gray-600">${book.first_publish_year || 'N/A'}</td>
                <td class="px-6 py-4">
                    <button class="text-blue-600 hover:text-blue-800 font-medium" 
                            onclick="viewBookDetails(${(currentPage - 1) * itemsPerPage + index})">
                        <i class="fas fa-eye mr-1"></i> View
                    </button>
                </td>
            `;
            
            fragment.appendChild(tr);
        });
    }

    // Clear existing content and append new results
    tbody.innerHTML = '';
    tbody.appendChild(fragment);

    // Update pagination info
    updatePaginationInfo();
}

// Update pagination info
function updatePaginationInfo() {
    const startRecord = document.getElementById('startRecord');
    const endRecord = document.getElementById('endRecord');
    const totalRecords = document.getElementById('totalRecords');
    
    if (startRecord && endRecord && totalRecords) {
        const total = currentBooks.length;
        const start = total === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, total);
        
        startRecord.textContent = start;
        endRecord.textContent = end;
        totalRecords.textContent = total;
    }

    // Update pagination buttons
    const prevButton = document.getElementById('prevPage');
    const nextButton = document.getElementById('nextPage');
    
    if (prevButton) {
        prevButton.disabled = currentPage === 1;
    }
    if (nextButton) {
        nextButton.disabled = currentPage >= totalPages;
    }

    // Update page numbers
    const pageNumbers = document.getElementById('pageNumbers');
    if (pageNumbers) {
        pageNumbers.innerHTML = '';
        
        // Show max 5 page numbers
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        
        if (startPage > 1) {
            addPageButton(1, pageNumbers);
            if (startPage > 2) {
                addEllipsis(pageNumbers);
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            addPageButton(i, pageNumbers);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                addEllipsis(pageNumbers);
            }
            addPageButton(totalPages, pageNumbers);
        }
    }
}

// Add page button
function addPageButton(pageNum, container) {
    const button = document.createElement('button');
    button.textContent = pageNum;
    button.className = `relative inline-flex items-center px-4 py-2 text-sm font-semibold ${
        pageNum === currentPage
            ? 'z-10 bg-blue-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
            : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
    }`;
    button.onclick = () => goToPage(pageNum);
    container.appendChild(button);
}

// Add ellipsis
function addEllipsis(container) {
    const span = document.createElement('span');
    span.className = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0';
    span.textContent = '...';
    container.appendChild(span);
}

// Go to page
function goToPage(page) {
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        displaySearchResults(currentBooks); // Reuse existing data
    }
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Cache key generator
function generateCacheKey(searchType, searchQuery, language, category) {
    return `${searchType}-${searchQuery}-${language}-${category}`;
}

// Search books function
const searchBooks = async function() {
    const searchType = document.getElementById('searchType')?.value || 'title';
    const searchQuery = document.getElementById('searchQuery')?.value?.trim();
    const language = document.getElementById('language')?.value || 'all';
    
    if (!searchQuery) {
        displaySearchResults([], true);
        return;
    }

    try {
        showLoadingModal('Searching books...');
        
        let apiUrl = `https://openlibrary.org/search.json?${searchType}=${encodeURIComponent(searchQuery)}`;
        if (language !== 'all') {
            apiUrl += `&language=${language}`;
        }
        
        const response = await fetch(apiUrl);
        const data = await response.json();
        
        displaySearchResults(data.docs || [], true); // Pass true for new search
    } catch (error) {
        console.error('Search error:', error);
        displaySearchResults([], true);
        alert('Error searching books: ' + error.message);
    } finally {
        closeLoadingModal();
    }
}

// Fetch selected books with loading state
async function fetchSelectedBooks() {
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
            
            // Ensure all values are properly sanitized
            return {
                title: book.title || 'Unknown Title',
                author: book.author_name ? book.author_name[0] : 'Unknown Author',
                isbn: book.isbn && book.isbn[0] ? book.isbn[0] : 'N/A',
                published_year: book.first_publish_year || null,
                cover_id: book.cover_i ? book.cover_i.toString() : '',
                language: book.language && book.language[0] ? book.language[0] : 'eng'
            };
        });

        const response = await fetch('../api/books/save.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(books)  // Send books array directly
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.text();  // Get response as text first
        let data;
        
        try {
            data = JSON.parse(result);  // Try to parse as JSON
        } catch (e) {
            console.error('Server response:', result);
            throw new Error('Invalid JSON response from server');
        }

        if (data.success) {
            alert('Books saved successfully!');
            // Clear selections
            selected.forEach(cb => cb.checked = false);
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = false;
            }
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    } catch (error) {
        console.error('Error saving books:', error);
        alert('Error saving books: ' + error.message);
    } finally {
        closeLoadingModal();
    }
}

// View book details with loading state
function viewBookDetails(index) {
    const book = currentBooks[index];
    if (!book) return;
    
    const modalHtml = `
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" id="bookDetailModal">
            <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full mx-4 opacity-0 transform translate-y-4 transition-all duration-300">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-2xl font-semibold text-gray-800">Book Details</h3>
                    <button class="text-gray-500 hover:text-gray-700" id="closeDetailModal">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="flex gap-8">
                        <div class="w-1/3">
                            <img src="https://covers.openlibrary.org/b/id/${book.cover_i}-L.jpg" 
                                 class="w-full rounded-lg shadow-lg object-cover"
                                 onerror="this.src='../assets/images/no-cover.png'">
                        </div>
                        
                        <div class="w-2/3 space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Title</h4>
                                <p class="text-lg font-medium text-gray-900">${book.title}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Author</h4>
                                <p class="text-gray-800">${book.author_name?.[0] || 'Unknown'}</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Language</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        ${book.language || 'Unknown'}
                                    </span>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Published Year</h4>
                                    <p class="text-gray-800">${book.first_publish_year || 'N/A'}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">ISBN</h4>
                                    <p class="text-gray-800">${book.isbn?.[0] || 'N/A'}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Publisher</h4>
                                    <p class="text-gray-800">${book.publisher?.[0] || 'N/A'}</p>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Description</h4>
                                <p class="text-gray-800 mt-1">${book.description || 'No description available'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-4 p-6 border-t bg-gray-50 rounded-b-2xl">
                    <button class="px-4 py-2 text-gray-700 hover:text-gray-900" id="cancelDetailModal">
                        Close
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" id="addToLibraryButton">
                        Add to Library
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Add event listeners for modal buttons
    const modal = document.getElementById('bookDetailModal');
    if (modal) {
        // Trigger animation after a frame
        requestAnimationFrame(() => {
            const dialog = modal.querySelector('.transform');
            if (dialog) {
                dialog.classList.remove('opacity-0', 'translate-y-4');
            }
        });
        
        const closeButtons = modal.querySelectorAll('#closeDetailModal, #cancelDetailModal');
        closeButtons.forEach(button => {
            button.addEventListener('click', closeBookDetails);
        });
        
        const addButton = modal.querySelector('#addToLibraryButton');
        if (addButton) {
            addButton.addEventListener('click', () => {
                closeBookDetails();
                fetchSelectedBooks([index]);
            });
        }
    }
}

// Close book details modal with animation
function closeBookDetails() {
    const modal = document.getElementById('bookDetailModal');
    if (modal) {
        const dialog = modal.querySelector('.transform');
        if (dialog) {
            dialog.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.remove();
            }, 300);
        } else {
            modal.remove();
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize search button
    const searchButton = document.getElementById('searchButton');
    if (searchButton) {
        searchButton.addEventListener('click', searchBooks);
    }
    
    // Initialize fetch selected button
    const fetchButton = document.getElementById('fetchSelectedButton');
    if (fetchButton) {
        fetchButton.addEventListener('click', fetchSelectedBooks);
    }
    
    // Initialize select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            document.querySelectorAll('.book-select').forEach(cb => {
                cb.checked = e.target.checked;
            });
        });
    }
    
    // Initialize search on enter key only
    const searchInput = document.getElementById('searchQuery');
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchBooks();
            }
        });
    }
    
    // Initialize pagination buttons
    const prevPageButton = document.getElementById('prevPage');
    if (prevPageButton) {
        prevPageButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displaySearchResults(currentBooks);
            }
        });
    }
    
    const nextPageButton = document.getElementById('nextPage');
    if (nextPageButton) {
        nextPageButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                displaySearchResults(currentBooks);
            }
        });
    }
    
    // Initialize items per page selector
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
    if (itemsPerPageSelect) {
        itemsPerPageSelect.value = itemsPerPage; // Set initial value
        itemsPerPageSelect.addEventListener('change', () => {
            itemsPerPage = parseInt(itemsPerPageSelect.value);
            currentPage = 1; // Reset to first page when changing items per page
            displaySearchResults(currentBooks);
        });
    }
});