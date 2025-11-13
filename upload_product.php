<?php
require 'vendor/autoload.php'; // AWS SDK

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Данные для R2
$accountId = '<YOUR_ACCOUNT_ID>';
$accessKey = '<YOUR_R2_ACCESS_KEY>';
$secretKey = '<YOUR_R2_SECRET_KEY>';
$bucket = '<YOUR_BUCKET_NAME>';

// Создаём клиент S3 (R2 совместим с S3 API)
$s3 = new S3Client([
    'version' => 'latest',
    'region' => 'auto',
    'endpoint' => "https://$accountId.r2.cloudflarestorage.com",
    'credentials' => [
        'key' => $accessKey,
        'secret' => $secretKey,
    ],
]);

// Получаем данные из формы
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$category_id = $_POST['category_id'];

if(isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0){
    $fileTmp  = $_FILES['image_file']['tmp_name'];
    $fileName = basename($_FILES['image_file']['name']);

    // Проверка расширения
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if(!in_array($ext, $allowed)){
        die("Неподдерживаемый формат файла");
    }

    try {
        // Загружаем файл в R2
        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key'    => $fileName,
            'SourceFile' => $fileTmp,
            'ACL'    => 'public-read' // чтобы можно было использовать URL на сайте
        ]);

        $imageUrl = $result['ObjectURL']; // публичный URL файла

        // Сохраняем в базу
        $pdo = new PDO("mysql:host=localhost;dbname=clothstore;charset=utf8", "DB_USER", "DB_PASS");
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $imageUrl, $category_id]);

        echo "Товар успешно добавлен! <a href='admin.php'>Вернуться к списку товаров</a>";

    } catch (AwsException $e) {
        echo "Ошибка загрузки файла: " . $e->getMessage();
    }

} else {
    echo "Файл не был загружен.";
}
