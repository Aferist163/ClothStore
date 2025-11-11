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
<body>
    <?php include 'header.php'; ?>

    <div class="profile-container">
       <div class="info">
         <div class="profile-info">
             <h2>Your Profile</h2>
            <p><strong>Username:</strong> <span id="profile-username">Loading...</span></p>
            <p><strong>Email:</strong> <span id="profile-email">Loading...</span></p>
            <p><strong>Role:</strong> <span id="profile-role">Loading...</span></p>
        </div>
        <form id="change-password-form">
            <h3>Change Password</h3>

            <div id="password-message"></div>

            <div class="form-group">
                <label for="old_password">Old Password</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password (min. 8 characters)</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="form-button">Update Password</button>
        </form>
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