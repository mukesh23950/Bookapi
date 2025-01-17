<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirectTo('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'login') {
            $login = sanitizeInput($_POST['login']);
            $password = md5($_POST['password']);
            
            // Validate inputs
            if (empty($login)) {
                $errors[] = "Username or Email is required";
            }
            if (empty($password)) {
                $errors[] = "Password is required";
            }

            if (empty($errors)) {
                try {
                    // Check if user exists with either username or email
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$login, $login]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && $user['password'] === $password) {
                        // Login successful
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_role'] = $user['role'];

                        // Handle remember me
                        if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                            $token = bin2hex(random_bytes(32));
                            setcookie('remember_token', $token, time() + (86400 * 30), "/");
                            
                            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                            $stmt->execute([$token, $user['id']]);
                        }

                        // Redirect based on role
                        if ($user['role'] === 'admin') {
                            redirectTo('admin/dashboard.php');
                        } else {
                            redirectTo('index.php');
                        }
                    } else {
                        $errors[] = "Invalid username/email or password";
                    }
                } catch (PDOException $e) {
                    $errors[] = "Login failed. Please try again.";
                }
            }
        } elseif ($_POST['action'] == 'register') {
            $name = sanitizeInput($_POST['name']);
            $username = sanitizeInput($_POST['username']);
            $email = sanitizeInput($_POST['email']);
            $password = md5($_POST['password']);
            
            // Validate register inputs
            if (empty($name)) {
                $errors[] = "Name is required";
            }
            if (empty($username)) {
                $errors[] = "Username is required";
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
                $errors[] = "Username must be 3-20 characters and can only contain letters, numbers, and underscores";
            }
            if (!validateEmail($email)) {
                $errors[] = "Invalid email format";
            }
            if (!validatePassword($password)) {
                $errors[] = "Password must be at least 8 characters with uppercase, lowercase, number and special character";
            }

            // Check if username or email already exists
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    if ($stmt->fetchColumn() > 0) {
                        $errors[] = "Username or Email already exists";
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
                        if ($stmt->execute([$name, $username, $email, $password])) {
                            setFlashMessage('success', 'Registration successful! Please login.');
                            redirectTo('login.php');
                        }
                    }
                } catch (PDOException $e) {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        }
    }
}

$page_title = "Login - Online Library";
include 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-400 to-purple-600 p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-[1000px] p-8 flex">
        <!-- Left Side - Login Form -->
        <div class="w-full md:w-1/2 pr-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Login</h2>
                <p class="text-gray-600">
                    Doesn't have an account yet? 
                    <button onclick="switchTab('register')" class="text-purple-600 hover:text-purple-700 font-medium">
                        Sign Up
                    </button>
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-red-700"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="login">
                <div>
                    <label class="block text-gray-600 mb-2">Username or Email</label>
                    <input type="text" name="login" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter username or email">
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-gray-600">Password</label>
                        <a href="#" class="text-purple-600 hover:text-purple-700 text-sm">Forgot Password?</a>
                    </div>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter your password">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" 
                           class="rounded text-purple-600 focus:ring-purple-500">
                    <label for="remember" class="ml-2 text-gray-600">Remember me</label>
                </div>
                <button type="submit" 
                        class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-all duration-200 transform hover:-translate-y-0.5">
                    LOGIN
                </button>
            </form>

            <!-- Register Form -->
            <form id="registerForm" method="POST" class="hidden space-y-6">
                <input type="hidden" name="action" value="register">
                <div>
                    <label class="block text-gray-600 mb-2">Full Name</label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter your full name">
                </div>
                <div>
                    <label class="block text-gray-600 mb-2">Username</label>
                    <input type="text" name="username" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Choose a username">
                </div>
                <div>
                    <label class="block text-gray-600 mb-2">Email Address</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter your email">
                </div>
                <div>
                    <label class="block text-gray-600 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Create a strong password">
                </div>
                <button type="submit" 
                        class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-all duration-200 transform hover:-translate-y-0.5">
                    CREATE ACCOUNT
                </button>
            </form>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden md:block md:w-1/2">
            <img src="assets/images/login-illustration.svg" alt="Login" class="w-full">
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const title = document.querySelector('h2');
    const subtitle = document.querySelector('p');

    if (tab === 'login') {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        title.textContent = 'Login';
        subtitle.innerHTML = 'Doesn\'t have an account yet? <button onclick="switchTab(\'register\')" class="text-purple-600 hover:text-purple-700 font-medium">Sign Up</button>';
    } else {
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        title.textContent = 'Create Account';
        subtitle.innerHTML = 'Already have an account? <button onclick="switchTab(\'login\')" class="text-purple-600 hover:text-purple-700 font-medium">Login</button>';
    }
}
</script>

<?php include 'includes/footer.php'; ?> 