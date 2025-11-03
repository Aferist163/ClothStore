<?php
// api/checkout.php

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 1. ПЕРЕВІРКА АВТОРИЗАЦІЇ
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User must be logged in to checkout']);
    exit;
}

// Дозволяємо лише POST-запити
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

// 2. Підключаємо БД
require_once '../db.php'; // $conn

$user_id = $_SESSION['user_id'];

// 3. Починаємо ТРАНЗАКЦІЮ
// Це гарантує, що всі дії виконаються "атомарно"
$conn->begin_transaction();

try {
    // --- КРОК 1: Отримати вміст кошика і порахувати суму ----

    $sql_get_cart = "SELECT 
                        p.id as product_id,
                        p.price,
                        c.quantity
                    FROM 
                        cart c
                    JOIN 
                        products p ON c.product_id = p.id
                    WHERE 
                        c.user_id = ?";

    $stmt_get_cart = $conn->prepare($sql_get_cart);
    $stmt_get_cart->bind_param('i', $user_id);
    $stmt_get_cart->execute();
    $result_cart = $stmt_get_cart->get_result();

    $cart_items = [];
    $total_amount = 0;

    if ($result_cart->num_rows === 0) {
        // Кошик порожній, нічого оформлювати
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Cart is empty']);
        $conn->rollback(); // Відкочуємо транзакцію (хоча ми нічого і не зробили)
        $conn->close();
        exit;
    }

    while ($row = $result_cart->fetch_assoc()) {
        $cart_items[] = $row;
        $total_amount += $row['price'] * $row['quantity'];
    }
    $stmt_get_cart->close();

    // --- КРОК 2: Створити запис у таблиці `orders` ----

    $sql_create_order = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')";
    $stmt_create_order = $conn->prepare($sql_create_order);
    $stmt_create_order->bind_param('id', $user_id, $total_amount); // 'i' - integer, 'd' - double
    $stmt_create_order->execute();

    $new_order_id = $conn->insert_id; // Отримуємо ID щойно створеного замовлення
    $stmt_create_order->close();

    // --- КРОК 3: Скопіювати товари з кошика в `order_items` ----

    $sql_insert_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_insert_items = $conn->prepare($sql_insert_items);

    foreach ($cart_items as $item) {
        $stmt_insert_items->bind_param(
            'iiid', 
            $new_order_id, 
            $item['product_id'], 
            $item['quantity'], 
            $item['price'] // Зберігаємо ціну на момент покупки!
        );
        $stmt_insert_items->execute();
    }
    $stmt_insert_items->close();

    // --- КРОК 4: Очистити кошик користувача ----

    $sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->bind_param('i', $user_id);
    $stmt_clear_cart->execute();
    $stmt_clear_cart->close();

    // --- КРОК 5: ПІДТВЕРДЖЕННЯ ТРАНЗАКЦІЇ ----
    // Якщо ми дійшли сюди, всі кроки успішні
    $conn->commit();

    http_response_code(201); // Created
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $new_order_id,
        'total' => $total_amount
    ]);

} catch (Exception $e) {
    // --- ОБРОБКА ПОМИЛОК: ВІДКОТИТИ ВСЕ ----
    $conn->rollback();

    http_response_code(500); // Internal Server Error
    echo json_encode([
        'error' => 'Failed to place order',
        'details' => $e->getMessage()
    ]);
}

$conn->close();

?>