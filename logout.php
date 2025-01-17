<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Clear all session data
session_start();
session_destroy();

// Clear remember me cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login page
redirectTo('login.php');
?>
