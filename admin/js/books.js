let currentBooks = [];

// Show loading modal
function showModal(message) {
    const modal = document.createElement('div');
    modal.id = 'loadingModal';
    modal.innerHTML = `
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600">${message}</p>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Close loading modal
function closeModal() {
    const modal = document.getElementById('loadingModal');
    if (modal) {
        modal.remove();
    }
}

// Search books function
async function searchBooks() {
    const searchType = document.getElementById('searchType').value;
    const searchQuery = document.getElementById('searchQuery').value;
    const language = document.getElementById('language').value;
    
    if (!searchQuery) {
        alert('Please enter a search term');
        return;
    }

    try {
        showModal('Searching books...');
        
        let apiUrl = `https://openlibrary.org/search.json?${searchType}=${encodeURIComponent(searchQuery)}`;
        if (language !== 'all') {
            apiUrl += `&language=${language}`;
        }
        
        const response = await fetch(apiUrl);
        const data = await response.json();
        
        currentBooks = data.docs;
        displaySearchResults(currentBooks);
        closeModal();
    } catch (error) {
        closeModal();
        console.error('Search error:', error);
        alert('Error searching books');
    }
}

// Display search results
function displaySearchResults(books) {
    const tbody = document.getElementById('searchResults');
    tbody.innerHTML = '';

    if (!books || books.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No books found</td></tr>';
        return;
    }

    books.forEach((book, index) => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';
        
        tr.innerHTML = `
            <td class="px-6 py-4">
                <input type="checkbox" class="book-select w-4 h-4 rounded border-gray-300" data-index="${index}">
            </td>
            <td class="px-6 py-4">
                <img src="https://covers.openlibrary.org/b/id/${book.cover_i}-S.jpg" 
                     class="w-16 h-20 object-cover rounded-lg shadow-sm"
                     onerror="this.src='../assets/images/no-cover.png'">
            </td>
            <td class="px-6 py-4 text-gray-900 font-medium">${book.title}</td>
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
                <button onclick="viewBookDetails(${index})" 
                        class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-eye mr-1"></i> View
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Fetch selected books
async function fetchSelectedBooks() {
    const selected = document.querySelectorAll('.book-select:checked');
    
    if (selected.length === 0) {
        alert('Please select at least one book');
        return;
    }

    try {
        showModal('Fetching selected books...');
        
        const books = Array.from(selected).map(checkbox => {
            const index = parseInt(checkbox.dataset.index);
            const book = currentBooks[index];
            
            return {
                title: book.title || '',
                author: book.author_name ? book.author_name[0] : 'Unknown',
                cover_image: book.cover_i ? `https://covers.openlibrary.org/b/id/${book.cover_i}-L.jpg` : '',
                description: book.description || '',
                isbn: book.isbn ? book.isbn[0] : '',
                publish_year: book.first_publish_year || null,
                publisher: book.publisher ? book.publisher[0] : '',
                language: book.language ? book.language[0] : '',
                page_count: book.number_of_pages || 0,
                book_key: book.key || '',
                slug: book.title ? book.title.toLowerCase().replace(/[^a-z0-9]+/g, '-') : '',
                meta_title: book.title || '',
                meta_description: book.description ? book.description.substring(0, 160) : '',
                status: 'published'
            };
        });

        const response = await fetch('../api/save-books.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(books)
        });

        const result = await response.json();
        
        if (result.success) {
            closeModal();
            alert('Books saved successfully!');
            document.querySelectorAll('.book-select').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        closeModal();
        console.error('Error:', error);
        alert('Error saving books: ' + error.message);
    }
}

// Initialize select all functionality
document.getElementById('selectAll')?.addEventListener('change', (e) => {
    document.querySelectorAll('.book-select').forEach(cb => cb.checked = e.target.checked);
}); 