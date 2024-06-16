<?php
require 'db.php';
session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin']; // Set based on the value retrieved from the database
        $_SESSION['username'] = $user['username'];
        setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); // 86400 = 1 day
        header('Location: ../index.php');
        exit();
    } else {
        $error_message = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoTrip Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Login Mototrip</h2>
            <form action="login.php" method="post">
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
                <div class="input-box">
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
                
            </form>
        </div>
    </div>
</body>
</html>

