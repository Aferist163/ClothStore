<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
use Cloudinary\Cloudinary;

// Показываем ошибки PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    $cloudName = getenv('CLOUDINARY_CLOUD');
    $apiKey    = getenv('CLOUDINARY_KEY');
    $apiSecret = getenv('CLOUDINARY_SECRET');

    if (!$cloudName || !$apiKey || !$apiSecret) {
        throw new Exception('Cloudinary credentials missing');
    }

    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => $cloudName,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret
        ]
    ]);

    if (empty($_FILES['image_file']['tmp_name'])) {
        throw new Exception('No file uploaded');
    }

    $upload = $cloudinary->uploadApi()->upload($_FILES['image_file']['tmp_name'], ['folder'=>'clothstore']);

    // Просто логируем в консоль, ничего не возвращаем
    echo json_encode(['success' => true, 'message' => 'File uploaded']);

} catch (Exception $e) {
    // Любую ошибку сразу выводим в JSON, чтобы JS видел её в console.log
    echo json_encode(['error' => $e->getMessage()]);
}
