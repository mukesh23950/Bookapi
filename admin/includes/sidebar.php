<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-lg">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b">
            <a href="../index.php" class="text-2xl font-bold text-blue-600 flex items-center gap-2">
                <i class="fas fa-book-reader"></i>
                <span>Online Library</span>
            </a>
        </div>
        
        <!-- Navigation -->
        <nav class="py-4">
            <div class="px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Main</div>
            <a href="dashboard.php" class="flex items-center px-6 py-3 text-gray-700 <?php echo $page_name == 'dashboard' ? 'bg-gray-100 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-600'; ?>">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="mx-3">Dashboard</span>
            </a>
            
            <div class="px-4 py-2 mt-4 text-xs font-semibold text-gray-600 uppercase">Books Management</div>
            <a href="books.php" class="flex items-center px-6 py-3 text-gray-700 <?php echo $page_name == 'books' ? 'bg-gray-100 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-600'; ?>">
                <i class="fas fa-book w-5"></i>
                <span class="mx-3">Books</span>
            </a>
            <a href="categories.php" class="flex items-center px-6 py-3 text-gray-700 <?php echo $page_name == 'categories' ? 'bg-gray-100 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-600'; ?>">
                <i class="fas fa-tags w-5"></i>
                <span class="mx-3">Categories</span>
            </a>
            <a href="authors.php" class="flex items-center px-6 py-3 text-gray-700 <?php echo $page_name == 'authors' ? 'bg-gray-100 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-600'; ?>">
                <i class="fas fa-user-edit w-5"></i>
                <span class="mx-3">Authors</span>
            </a>
            
            <div class="px-4 py-2 mt-4 text-xs font-semibold text-gray-600 uppercase">User Management</div>
            <a href="users.php" class="flex items-center px-6 py-3 text-gray-700 <?php echo $page_name == 'users' ? 'bg-gray-100 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-600'; ?>">
                <i class="fas fa-users w-5"></i>
                <span class="mx-3">Users</span>
            </a>
            
            <div class="px-4 py-2 mt-4 text-xs font-semibold text-gray-600 uppercase">Settings</div>
            <a href="profile.php" class="flex items-center px-6 py-3 text-gray-700 <?php echo $page_name == 'profile' ? 'bg-gray-100 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-600'; ?>">
                <i class="fas fa-user-circle w-5"></i>
                <span class="mx-3">Profile</span>
            </a>
            <a href="settings.php" class="flex items-center px-6 py-3 text-gray-700 <?php echo $page_name == 'settings' ? 'bg-gray-100 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-600'; ?>">
                <i class="fas fa-cog w-5"></i>
                <span class="mx-3">Settings</span>
            </a>
            <a href="../logout.php" class="flex items-center px-6 py-3 text-red-700 hover:bg-red-50 hover:border-l-4 hover:border-red-600">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="mx-3">Logout</span>
            </a>
        </nav>
    </div>
</div> 