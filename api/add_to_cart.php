<?php
// api/add_to_cart.php

// Запускаємо сесію, щоб отримати user_id
session_start();

// Встановлюємо заголовки
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обробка OPTIONS-запиту
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Дозволяємо лише POST-запити
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

// 1. ПЕРЕВІРКА АВТОРИЗАЦІЇ (НАШ "ОХОРОНЕЦЬ")
// Це захищений ендпоінт. Тільки залогінені користувачі можуть додавати в кошик.
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User must be logged in to add to cart']);
    exit;
}

// 2. Підключаємо БД і отримуємо дані
require_once '../db.php'; // $conn
$data = json_decode(file_get_contents('php://input'), true);

// 3. Отримуємо дані
$user_id = $_SESSION['user_id']; // Беремо ID з сесії (безпечно)
$product_id = $data['product_id'] ?? 0;
$quantity = $data['quantity'] ?? 1; // За замовчуванням додаємо 1

// 4. Валідація
if (empty($product_id) || !is_numeric($product_id) || $quantity <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid product_id or quantity']);
    $conn->close();
    exit;
}

// 5. Логіка "UPSERT" (UPDATE або INSERT)
// Спочатку перевіряємо, чи цей товар ВЖЕ є в кошику цього юзера
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param('ii', $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ---- ТОВАР ВЖЕ В КОШИКУ: ОНОВЛЮЄМО КІЛЬКІСТЬ ----
    $row = $result->fetch_assoc();
    $cart_item_id = $row['id'];
    $new_quantity = $row['quantity'] + $quantity;

    $stmt_update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt_update->bind_param('ii', $new_quantity, $cart_item_id);

    if ($stmt_update->execute()) {
        http_response_code(200); // OK
        echo json_encode(['success' => true, 'message' => 'Item quantity updated in cart']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update cart']);
    }
    $stmt_update->close();

} else {
    // ---- НОВИЙ ТОВАР: ДОДАЄМО В КОШИК ----
    $stmt_insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt_insert->bind_param('iii', $user_id, $product_id, $quantity);

    if ($stmt_insert->execute()) {
        http_response_code(201); // Created
        echo json_encode([
            'success' => true, 
            'message' => 'Item added to cart',
            'cart_item_id' => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add item to cart']);
    }
    $stmt_insert->close();
}

// 6. Закриваємо з'єднання
$stmt->close();
$conn->close();

?>