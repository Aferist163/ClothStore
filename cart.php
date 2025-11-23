<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - ClothStore</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/cart.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="main-container cart">
        <div class="cart-container glass">
            <h2>Your Cart</h2>
            <div id="cart-items-container">
                <p id="cart-empty-msg">Your cart is currently empty.</p>
            </div>
            <div class="cart-summary">
                <h3 id="cart-total">Total: 0.00€</h3>
                <button id="checkout-button">Proceed to Checkout</button>
            </div>
            <div class="cart-links">
                <a href="index.php">Back to shop</a>
            </div>
        </div>
    </div>


    <script src="js/cart.js" defer></script>

    <?php include 'footer.php'; ?>