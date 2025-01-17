    <footer class="bg-gray-900 text-gray-300 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Column 1 -->
                <div>
                    <h3 class="text-xl font-semibold text-white mb-4">Online Library</h3>
                    <p class="text-gray-400 text-sm">
                        Your gateway to endless knowledge. Discover millions of books and expand your horizons.
                    </p>
                    <div class="mt-4 flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Column 2 -->
                <div>
                    <h3 class="text-xl font-semibold text-white mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="/books" class="text-gray-400 hover:text-white transition duration-300">Books</a></li>
                        <li><a href="/categories" class="text-gray-400 hover:text-white transition duration-300">Categories</a></li>
                        <li><a href="/about" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                    </ul>
                </div>

                <!-- Column 3 -->
                <div>
                    <h3 class="text-xl font-semibold text-white mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="/privacy" class="text-gray-400 hover:text-white transition duration-300">Privacy Policy</a></li>
                        <li><a href="/terms" class="text-gray-400 hover:text-white transition duration-300">Terms of Service</a></li>
                        <li><a href="/disclaimer" class="text-gray-400 hover:text-white transition duration-300">Disclaimer</a></li>
                        <li><a href="/contact" class="text-gray-400 hover:text-white transition duration-300">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Column 4 -->
                <div>
                    <h3 class="text-xl font-semibold text-white mb-4">Newsletter</h3>
                    <p class="text-gray-400 text-sm mb-4">Subscribe to our newsletter for updates and new releases.</p>
                    <form class="flex flex-col space-y-3">
                        <input type="email" placeholder="Your email address" 
                            class="px-4 py-2 bg-gray-800 text-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-sm text-gray-400">
                        © <?php echo date('Y'); ?> Online Library. All rights reserved.
                    </p>
                    <div class="mt-4 md:mt-0">
                        <a href="/privacy" class="text-sm text-gray-400 hover:text-white mx-3">Privacy</a>
                        <a href="/terms" class="text-sm text-gray-400 hover:text-white mx-3">Terms</a>
                        <a href="/cookies" class="text-sm text-gray-400 hover:text-white mx-3">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
    <script>
        const btn = document.querySelector("button.mobile-menu-button");
        const menu = document.querySelector(".mobile-menu");

        btn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });
    </script>
</body>
</html> 