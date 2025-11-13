<?php
// db.php

// Зчитуємо змінні середовища, які ми встановимо на Render
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT');

if (!$host) {
    // Якщо ми локально (XAMPP) - змінні порожні
    $host = "localhost";
    $user = "root";
    $pass = ""; // Ваш пароль від XAMPP (якщо є)
    $dbname = "clothstore";
    $port = 3306; // Стандартний порт XAMPP
}

// Створюємо підключення
$conn = new mysqli($host, $user, $pass, $dbname, (int)$port); // (int) для порту

// Якщо це TiDB (вимагає SSL), вмикаємо SSL
// Ми знаємо, що це TiDB, якщо $host не порожній
if (getenv('DB_HOST')) {
    $conn->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
    $conn->real_connect($host, $user, $pass, $dbname, (int)$port, NULL, MYSQLI_CLIENT_SSL);
}

// Перевірка підключення
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>