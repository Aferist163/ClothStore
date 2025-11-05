<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ClothStore</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/auth.css">
    <script src="js/auth.js" defer></script>
</head>
<body class="auth-page"> <form id="register-form" class="auth-form">
        <h2>Create Account</h2>
        
        <div id="error-message"></div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="form-button">Register</button>
        
        <div class="form-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <p><a href="index.php">Back to shop</a></p>
        </div>
    </form>

</body>
</html>