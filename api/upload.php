<?php
require __DIR__ . '/../vendor/autoload.php'; // путь к autoload
use Cloudinary\Cloudinary;

header('Content-Type: application/json');

// Включаем логирование ошибок в файл (не выводим их в браузер)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/upload_errors.log');
error_reporting(E_ALL);

try {
    // Получаем данные Cloudinary из переменных окружения
    $cloudName = getenv('CLOUDINARY_CLOUD');
    $apiKey    = getenv('CLOUDINARY_KEY');
    $apiSecret = getenv('CLOUDINARY_SECRET');

    if (!$cloudName || !$apiKey || !$apiSecret) {
        throw new Exception('Cloudinary credentials not set in environment');
    }

    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => $cloudName,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret
        ]
    ]);

    // Проверяем, что файл пришёл
    if(empty($_FILES['image_file']['tmp_name'])){
        throw new Exception('No file uploaded');
    }

    // Загружаем файл в Cloudinary
    $upload = $cloudinary->uploadApi()->upload($_FILES['image_file']['tmp_name'], [
        'folder'=>'clothstore'
    ]);

    // Возвращаем URL изображения
    echo json_encode(['url' => $upload['secure_url']]);

} catch (Exception $e) {
    // Логируем полную ошибку для дебага
    error_log($e->__toString());

    // Отправляем клиенту JSON с сообщением ошибки
    echo json_encode(['error' => $e->getMessage()]);
}
