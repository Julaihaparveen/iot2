<?php
session_start();
include 'config.php';
 
// Handle Registration
if (isset($_POST['signup'])) {
    $name = $_POST['signup_name'];
    $email = $_POST['signup_email'];
    $password = password_hash($_POST['signup_password'], PASSWORD_DEFAULT);
 
    $query = "INSERT INTO users (name, email, password) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, [$name, $email, $password]);
 
    if ($result) {
        $signup_msg = "✅ Registration successful! Please log in.";
    } else {
        $signup_msg = "❌ Registration failed. Email may already exist.";
    }
}
 
// Handle Login
if (isset($_POST['login'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];
 
    $query = "SELECT * FROM users WHERE email = $1";
    $result = pg_query_params($conn, $query, [$email]);
    $user = pg_fetch_assoc($result);
 
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $login_msg = "❌ Invalid email or password.";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login / Signup - OpenSenseMap Viewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
 
<div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">
    <!-- Login Form -->
    <div class="p-8">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Login</h2>
 
        <?php if (!empty($login_msg)) echo "<p class='text-red-600 mb-2'>$login_msg</p>"; ?>
 
        <form method="post" class="space-y-4">
            <input type="email" name="login_email" placeholder="Email"
                   required class="w-full px-4 py-2 border rounded-md">
            <input type="password" name="login_password" placeholder="Password"
                   required class="w-full px-4 py-2 border rounded-md">
            <button type="submit" name="login"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
                Login
            </button>
        </form>
    </div>
 
    <!-- Signup Form -->
    <div class="p-8 bg-gray-50">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Sign Up</h2>
 
        <?php if (!empty($signup_msg)) echo "<p class='text-green-600 mb-2'>$signup_msg</p>"; ?>
 
        <form method="post" class="space-y-4">
            <input type="text" name="signup_name" placeholder="Full Name"
                   required class="w-full px-4 py-2 border rounded-md">
            <input type="email" name="signup_email" placeholder="Email"
                   required class="w-full px-4 py-2 border rounded-md">
            <input type="password" name="signup_password" placeholder="Password"
                   required class="w-full px-4 py-2 border rounded-md">
            <button type="submit" name="signup"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded">
                Sign Up
            </button>
        </form>
    </div>
</div>
 
</body>
</html>