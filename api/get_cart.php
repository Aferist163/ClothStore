<?php
// api/get_cart.php

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 1. ПЕРЕВІРКА АВТОРИЗАЦІЇ
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User must be logged in to view the cart']);
    exit;
}

// 2. Підключаємо БД
require_once '../db.php'; // $conn

$user_id = $_SESSION['user_id'];

// 3. Складний запит:
// Вибираємо дані з кошика (кількість)
// і приєднуємо дані з товарів (назва, ціна, зображення)
$sql = "SELECT 
            c.id as cart_item_id,
            p.id as product_id,
            p.name,
            p.price,
            p.image_url,
            c.quantity
        FROM 
            cart c
        JOIN 
            products p ON c.product_id = p.id
        WHERE 
            c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

// 4. Збираємо всі товари в масив і рахуємо суму
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

// 5. Повертаємо JSON-відповідь
http_response_code(200); // OK
echo json_encode([
    'items' => $cart_items,
    'totalPrice' => $total_price
]);

// 6. Закриваємо з'єднання
$stmt->close();
$conn->close();

?>