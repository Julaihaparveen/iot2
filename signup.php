<?php
session_start();
require_once 'config.php'; // Ensure config.php defines $pdo

$signup_error = '';
$signup_success = '';

// Handle signup submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT * FROM login WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $signup_error = "Username already taken.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO login (username, password_hash, role) VALUES (?, ?, 'user')");
            if ($insert->execute([$username, $hashed_password])) {
                $signup_success = "Account created! You can now log in.";
            } else {
                $signup_error = "Signup failed. Please try again.";
            }
        }
    } else {
        $signup_error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Signup</h2>

        <?php if ($signup_error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($signup_error) ?></div>
        <?php elseif ($signup_success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= htmlspecialchars($signup_success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block font-medium mb-1">Username</label>
                <input type="text" name="username" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div class="mb-6">
                <label class="block font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <button type="submit" name="signup" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">Signup</button>
        </form>

        <div class="mt-4 text-center">
            <a href="login.php" class="text-blue-600 hover:underline">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>
