<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid email or password";
    }
}

include 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <input name="email" type="email" required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="Email address">
                </div>
                <div>
                    <input name="password" type="password" required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="Password">
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Sign in
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Register here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 