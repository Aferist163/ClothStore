<?php
// Параметри підключення
$host = "localhost";       // сервер бази даних
$user = "root";            // користувач MySQL (за замовчуванням root)
$pass = "";                // пароль (за замовчуванням порожній)
$dbname = "clothstore";    // назва твоєї бази даних у phpMyAdmin

// Створюємо підключення
$conn = new mysqli($host, $user, $pass, $dbname);

// Перевірка підключення
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

?>
