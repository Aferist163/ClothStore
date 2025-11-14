<?php
require __DIR__ . '/../vendor/autoload.php'; // путь к автолоадеру
use Cloudinary\Cloudinary;

// Получаем данные Cloudinary из переменных окружения
$cloudName = getenv('CLOUDINARY_CLOUD');
$apiKey    = getenv('CLOUDINARY_KEY');
$apiSecret = getenv('CLOUDINARY_SECRET');

if (!$cloudName || !$apiKey || !$apiSecret) {
    error_log('Cloudinary credentials not set in environment');
    exit;
}

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key'    => $apiKey,
        'api_secret' => $apiSecret
    ]
]);

if(empty($_FILES['image_file']['tmp_name'])){
    error_log('No file uploaded');
    exit;
}

try {
    $cloudinary->uploadApi()->upload($_FILES['image_file']['tmp_name'], ['folder'=>'clothstore']);
    // Файл загружен, ничего не возвращаем
} catch (Exception $e) {
    error_log('Cloudinary upload error: ' . $e->getMessage());
    exit;
}
