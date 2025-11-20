<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../vendor/autoload.php';
use Cloudinary\Cloudinary;

// Змінні Cloudinary
$key = getenv('CLOUDINARY_KEY');
$secret = getenv('CLOUDINARY_SECRET');
$cloud = getenv('CLOUDINARY_CLOUD');

if (!$key || !$secret || !$cloud) {
    http_response_code(500);
    echo json_encode(['error' => 'Cloudinary credentials missing']);
    exit;
}

// Перевірка файлу
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

try {
    $cloudinaryUrl = "cloudinary://$key:$secret@$cloud";
    $cloudinary = new Cloudinary($cloudinaryUrl);

    $upload = $cloudinary->uploadApi()->upload($_FILES['image']['tmp_name'], ["folder" => "products"]);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'url' => $upload['secure_url']
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
