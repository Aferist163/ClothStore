<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';
use Cloudinary\Cloudinary;

try {
    // 1. Перевіряємо файл
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        echo json_encode([
            'success' => false,
            'step' => 'file_check',
            'error' => 'No file uploaded or upload error',
            'file_data' => $_FILES
        ]);
        exit;
    }

    // 2. Отримуємо ключі з ENV
    $key = getenv('CLOUDINARY_KEY');
    $secret = getenv('CLOUDINARY_SECRET');
    $cloud = getenv('CLOUDINARY_CLOUD');

    if (!$key || !$secret || !$cloud) {
        echo json_encode([
            'success' => false,
            'step' => 'env_check',
            'error' => 'Cloudinary credentials missing'
        ]);
        exit;
    }

    // 3. Ініціалізація Cloudinary
    $cloudinaryUrl = "cloudinary://$key:$secret@$cloud";
    $cloudinary = new Cloudinary($cloudinaryUrl);

    // 4. Завантаження файлу
    $upload = $cloudinary->uploadApi()->upload(
        $_FILES['image']['tmp_name'],
        ["folder" => "products"]
    );

    echo json_encode([
        'success' => true,
        'step' => 'upload',
        'url' => $upload['secure_url'],
        'file_name' => $_FILES['image']['name']
    ]);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'step' => 'exception',
        'error' => $e->getMessage()
    ]);
}
?>
