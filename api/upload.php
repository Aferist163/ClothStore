<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';
use Cloudinary\Cloudinary;

// Перевіряємо файл
if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] !== 0) {
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

try {
    // Створюємо CLOUDINARY_URL вручну
    $cloudinaryUrl = "cloudinary://" .
        getenv('CLOUDINARY_KEY') . ":" .
        getenv('CLOUDINARY_SECRET') . "@" .
        getenv('CLOUDINARY_CLOUD');

    $cloudinary = new Cloudinary($cloudinaryUrl);

    // Завантаження файлу
    $upload = $cloudinary->uploadApi()->upload(
        $_FILES['image_file']['tmp_name'],
        ["folder" => "products"]
    );

    echo json_encode([
        'success' => true,
        'url' => $upload['secure_url']
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>