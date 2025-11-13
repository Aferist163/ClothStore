<?php
// header.php

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<nav class="main-nav">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php">ClothStore</a>
        </div>
        <div class="nav-links">
            <a href="index.php">Shop</a>
            <a href="cart.php">Cart</a>
            <a href="about.php">About</a>
            
            <?php if ($is_admin): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>

            <?php if ($is_logged_in): ?>
                <a href="profile.php">My Profile</a>
                <button id="logout-nav-btn" class="nav-button">Logout</button>
            <?php else: ?>
                <a href="login.php" class="nav-button">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>