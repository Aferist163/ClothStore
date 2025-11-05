<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - ClothStore</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/profile.css">
    <link rel="stylesheet" href="./css/navbar.css">
    <script src="js/auth.js" defer></script>
</head>
<body class="profile-page">
    <?php include 'header.php'; ?>

    <div class="profile-container">
        <h2>Your Profile</h2>

        <div class="profile-info">
            <p><strong>Username:</strong> <span id="profile-username">Loading...</span></p>
            <p><strong>Email:</strong> <span id="profile-email">Loading...</span></p>
            <p><strong>Role:</strong> <span id="profile-role">Loading...</span></p>
        </div>

        <div class="profile-actions">
            <a href="admin.php" id="admin-button" class="profile-button">Admin Panel</a>

            <button id="logout-button" class="profile-button">Logout</button>
        </div>

        <div class="profile-links">
            <a href="index.php">Back to shop</a>
        </div>
    </div>

    </body>
    <?php include 'footer.php'; ?>
</html>