<?php
// api/products.php

// Встановлюємо заголовки
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Дозволяємо запити з будь-якого джерела
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обробка OPTIONS-запиту (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Дозволяємо лише GET-запити
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only GET method is allowed']);
    exit;
}

// 1. Підключаємо наш файл конфігурації БД
require_once '../db.php'; // $conn тепер доступний

// 2. Виконуємо SELECT-запит
// Ми беремо той самий запит, що й у index.php, щоб отримати назви категорій
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";

$result = $conn->query($sql);

$products = [];

// 3. Збираємо всі товари в один масив
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// 4. Відповідаємо у JSON-форматі
http_response_code(200); // OK
echo json_encode($products);

// 5. Закриваємо з'єднання
$conn->close();

?>