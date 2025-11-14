<?php
require __DIR__ . '/../vendor/autoload.php'; // поправил путь
use Cloudinary\Cloudinary;

header('Content-Type: application/json');

// Получаем данные Cloudinary из переменных окружения
$cloudName = getenv('CLOUDINARY_CLOUD');
$apiKey    = getenv('CLOUDINARY_KEY');
$apiSecret = getenv('CLOUDINARY_SECRET');

if (!$cloudName || !$apiKey || !$apiSecret) {
    echo json_encode(['error' => 'Cloudinary credentials not set in environment']);
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
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

try {
    $upload = $cloudinary->uploadApi()->upload($_FILES['image_file']['tmp_name'], ['folder'=>'clothstore']);
    echo json_encode(['url' => $upload['secure_url']]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
