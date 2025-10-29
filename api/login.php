<?php
// api/login.php

// Запускаємо сесію НА САМОМУ ПОЧАТКУ файлу
// Це необхідно для роботи з $_SESSION
session_start();

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

// 1. Підключаємо наш файл конфігурації БД
require_once '../db.php'; // $conn тепер доступний

// 2. Отримуємо дані з тіла запиту (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// 3. Валідація введених даних
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Email and password are required']);
    $conn->close();
    exit;
}

$email = $data['email'];
$password_from_user = $data['password'];

// 4. Шукаємо користувача в БД за email
$stmt = $conn->prepare('SELECT id, username, email, password_hash, role FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Користувача не знайдено
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'User with this email not found']);
    $stmt->close();
    $conn->close();
    exit;
}

// 5. Отримуємо дані користувача
$user = $result->fetch_assoc();

// 6. Перевіряємо пароль
if (password_verify($password_from_user, $user['password_hash'])) {
    // Пароль вірний!

    // 7. Ініціалізуємо сесію
    // Зберігаємо дані користувача в сесію
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // 8. Повертаємо успішну JSON-відповідь
    http_response_code(200); // OK
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);

} else {
    // Пароль невірний
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Invalid password']);
}

// 9. Закриваємо з'єднання
$stmt->close();
$conn->close();

?>