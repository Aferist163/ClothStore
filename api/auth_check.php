<?php
// api/auth_check.php

// Запускаємо сесію, щоб отримати доступ до $_SESSION
session_start();

// Встановлюємо заголовки
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Або ваш домен фронтенду
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Цей скрипт не вимагає підключення до БД, 
// оскільки всі потрібні дані вже лежать у сесії.

// 1. Перевіряємо наявність user_id у сесії
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {

    // 2. Якщо користувач авторизований, повертаємо його дані
    http_response_code(200); // OK
    echo json_encode([
        'isLoggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ]
    ]);

} else {

    // 3. Якщо користувач не авторизований
    http_response_code(401); // Unauthorized
    echo json_encode([
        'isLoggedIn' => false,
        'error' => 'User is not authenticated'
    ]);
}

?>