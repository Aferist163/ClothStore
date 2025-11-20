<?php
// Відображати всі помилки для дебагу (тільки для dev)
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';
use Cloudinary\Cloudinary;

// --- 1. Встановлюємо змінні Cloudinary залежно від середовища ---
$env = getenv('ENV') ?: 'local'; // ENV = 'render' або 'local'
if ($env === 'local') {
    // Локальні значення (можна додати у файл .env)
    $key = 'YOUR_LOCAL_KEY';
    $secret = 'YOUR_LOCAL_SECRET';
    $cloud = 'YOUR_LOCAL_CLOUD';
} else {
    // Render / production
    $key = getenv('CLOUDINARY_KEY');
    $secret = getenv('CLOUDINARY_SECRET');
    $cloud = getenv('CLOUDINARY_CLOUD');
}

// Перевіряємо наявність ключів
if (!$key || !$secret || !$cloud) {
    http_response_code(500);
    echo json_encode(['error' => 'Cloudinary credentials are missing']);
    exit;
}

// --- 2. Перевірка файлу ---
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
}

try {
    // --- 3. Створюємо Cloudinary об’єкт ---
    $cloudinaryUrl = "cloudinary://{$key}:{$secret}@{$cloud}";
    $cloudinary = new Cloudinary($cloudinaryUrl);

    // --- 4. Завантаження файлу ---
    $upload = $cloudinary->uploadApi()->upload(
        $_FILES['image']['tmp_name'],
        ["folder" => "products"]
    );

    echo json_encode([
        'success' => true,
        'url' => $upload['secure_url']
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
