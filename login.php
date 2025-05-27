<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Prepare and execute query
        $stmt = $pdo->prepare("SELECT * FROM login_users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <?php if ($error): ?>
      <p class="text-red-500 mb-4 text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php" class="space-y-4">
      <div>
        <label for="username" class="block font-semibold mb-1">Username</label>
        <input type="text" name="username" id="username" class="w-full border px-3 py-2 rounded" required>
      </div>
      <div>
        <label for="password" class="block font-semibold mb-1">Password</label>
        <input type="password" name="password" id="password" class="w-full border px-3 py-2 rounded" required>
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Login</button>
    </form>

    <p class="mt-4 text-center">
      Don't have an account?
      <a href="signup.php" class="text-blue-600 hover:underline">Sign Up</a>
    </p>
  </div>
</body>
</html>


