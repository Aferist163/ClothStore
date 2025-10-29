<?php
// api/register.php

// Встановлюємо заголовки для JSON-відповіді та CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Дозвіл для всіх доменів (для розробки)
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обробка OPTIONS-запиту (preflight)
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

// 1. Підключаємо наш файл конфігурації (він лежить на рівень вище)
require_once '../db.php'; // $conn тепер доступний

// 2. Отримуємо дані з тіла запиту (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// 3. Валідація введених даних
if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'All fields are required (username, email, password)']);
    $conn->close();
    exit;
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    $conn->close();
    exit;
}

$username = $data['username'];
$email = $data['email'];

// 4. Хешуємо пароль (!!!)
$password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

// 5. Перевіряємо, чи не існує користувач з таким email (використовуємо prepared statements)
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email); // 's' означає string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(['error' => 'User with this email already exists']);
    $stmt->close();
    $conn->close();
    exit;
}

// 6. Записуємо дані в таблицю users (використовуємо prepared statements)
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $email, $password_hash);

if ($stmt->execute()) {
    // 7. Повертаємо успішну JSON-відповідь
    http_response_code(201); // Created
    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully',
        'user_id' => $conn->insert_id // Повертаємо ID створеного користувача
    ]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}

// 8. Закриваємо з'єднання
$stmt->close();
$conn->close();

?>