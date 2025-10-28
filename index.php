<?php
include 'db.php';

$date = date("d.m.Y");

$name = "Користувачу";

$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>ClothStore</title>
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>
    <h2>Our products</h2>
    <div class="products">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <h3><?= $row['name'] ?> (<?= $row['category_name'] ?>)</h3>
                <img src="<?= $row['image_url'] ?>" alt="<?= $row['name'] ?>">
                <p><?= $row['description'] ?></p>
                <p><strong>Price:</strong><?= $row['price'] ?>€</p>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>