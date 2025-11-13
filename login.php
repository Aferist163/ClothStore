<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ClothStore</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/navbar.css"> <link rel="stylesheet" href="./css/auth.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <form id="login-form" class="auth-form">
        <h2>Login</h2>
        <div id="error-message"></div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="form-button">Login</button>
        <div class="form-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><a href="index.php">Back to shop</a></p>
        </div>
    </form>

<?php include 'footer.php'; ?>