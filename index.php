<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>ClothStore - Home</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/navbar.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <video autoplay muted loop id="bg-video">
        <source src="img/bg.mp4" type="video/mp4">
    </video>
    <div class="main-container">



        <aside class="sidebar glass">
            <h3>Filter</h3>
            <div class="filter-group">
                <label>
                    <input type="checkbox" class="filter" value="t-shirt"> t-shirt
                </label>
                <label>
                    <input type="checkbox" class="filter" value="hoodie"> hoodie
                </label>
                <label>
                    <input type="checkbox" class="filter" value="zip hoodie"> zip hoodie
                </label>
                <label>
                    <input type="checkbox" class="filter" value="sweatshirt"> sweatshirt
                </label>
                <label>
                    <input type="checkbox" class="filter" value="sweatpants"> sweatpants
                </label>
                <label>
                    <input type="checkbox" class="filter" value="jeans"> jeans
                </label>
                <label>
                    <input type="checkbox" class="filter" value="puffer jacket"> puffer jacket
                </label>
                <label>
                    <input type="checkbox" class="filter" value="hat"> hat
                </label>
                <label>
                    <input type="checkbox" class="filter" value="eyewear"> eyewear
                </label>
                <label>
                    <input type="checkbox" class="filter" value="shoes"> shoes
                </label>
            </div>

            <h3>Sort</h3>
            <select id="sort-select">
                <option value="default">Default</option>
                <option value="price-asc">Price ↑</option>
                <option value="price-desc">Price ↓</option>
                <option value="name-asc">Name A→Z</option>
                <option value="name-desc">Name Z→A</option>
            </select>





        </aside>

        <!-- Права колонка: товари -->
        <main class="products">
            <div class="imageSide">
                <div class="slider">
                    <div class="slides">
                        <img src="img/slide1.webp" alt="">
                        <img src="img/slide2.webp" alt="">
                        <img src="img/slide3.webp" alt="">
                    </div>

                    <button class="slide-btn prev">‹</button>
                    <button class="slide-btn next">›</button>
                </div>
            </div>

            <div class="products-list"></div>

            <!-- Тут JS вставляє картки товарів -->
        </main>

    </div>

    <script src="js/main.js" defer></script>

    <?php include 'footer.php'; ?>