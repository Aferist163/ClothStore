<?php
// api/logout.php

// Запускаємо сесію, щоб отримати до неї доступ
session_start();

// Знищуємо всі змінні сесії
$_SESSION = array();

// Руйнуємо саму сесію
session_destroy();

// Встановлюємо заголовки
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Повертаємо успішну відповідь
http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Logged out successfully']);

?>