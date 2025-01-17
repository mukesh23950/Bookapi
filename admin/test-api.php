<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirectTo('../login.php');
}

$page_title = "Test API";
include '../includes/header.php';
?>

<div class="p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">OpenLibrary API Test</h1>
        
        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex gap-4 mb-4">
                <select id="searchType" class="border rounded px-3 py-2">
                    <option value="title">Title</option>
                    <option value="author">Author</option>
                    <option value="isbn">ISBN</option>
                </select>
                <input type="text" id="searchQuery" 
                       class="flex-1 border rounded px-3 py-2" 
                       placeholder="Enter search term">
                <button onclick="testSearch()" 
                        class="bg-blue-500 text-white px-4 py-2 rounded">
                    Search
                </button>
            </div>
        </div>

        <!-- Results Display -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">API Response</h2>
            <pre id="apiResponse" class="bg-gray-100 p-4 rounded overflow-auto max-h-[500px]">
                Search results will appear here...
            </pre>
        </div>
    </div>
</div>

<script>
async function testSearch() {
    const searchType = document.getElementById('searchType').value;
    const query = document.getElementById('searchQuery').value.trim();
    const responseDiv = document.getElementById('apiResponse');
    
    if (!query) {
        alert('Please enter a search term');
        return;
    }

    try {
        responseDiv.innerHTML = 'Loading...';
        
        const response = await fetch(
            `https://openlibrary.org/search.json?${searchType}=${encodeURIComponent(query)}&limit=1`
        );
        
        const data = await response.json();
        
        // Format the response for better readability
        const formattedResponse = JSON.stringify(data, null, 2);
        
        // Display full API response
        responseDiv.innerHTML = `
            <div class="mb-4">
                <strong>Total Results:</strong> ${data.numFound}
            </div>
            <div class="mb-4">
                <strong>First Book Details:</strong>
                <ul class="list-disc pl-5 mt-2">
                    <li>Title: ${data.docs[0]?.title || 'N/A'}</li>
                    <li>Author: ${data.docs[0]?.author_name?.[0] || 'N/A'}</li>
                    <li>ISBN: ${data.docs[0]?.isbn?.[0] || 'N/A'}</li>
                    <li>Published: ${data.docs[0]?.first_publish_year || 'N/A'}</li>
                    <li>Rating Average: ${data.docs[0]?.ratings_average || 'N/A'}</li>
                    <li>Rating Count: ${data.docs[0]?.ratings_count || 'N/A'}</li>
                    <li>Pages: ${data.docs[0]?.number_of_pages_median || data.docs[0]?.number_of_pages || 'N/A'}</li>
                </ul>
            </div>
            <div>
                <strong>Raw API Response:</strong>
                <pre class="mt-2 text-sm">${formattedResponse}</pre>
            </div>
        `;
        
    } catch (error) {
        responseDiv.innerHTML = `Error: ${error.message}`;
    }
}

// Add enter key support for search
document.getElementById('searchQuery').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        testSearch();
    }
});
</script>

<?php include '../includes/footer.php'; ?> 