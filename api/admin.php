<?php
// api/admin.php

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- 1. АДМІН-ОХОРОНЕЦЬ ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Admin rights required.']);
    exit;
}

// 2. Підключаємо БД
require_once '../db.php'; // $conn

// 3. Підключаємо AWS SDK для Cloudflare R2
require_once '../vendor/autoload.php';
use Aws\S3\S3Client;

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'auto',
    'endpoint' => 'ID',
    'credentials' => [
        'key'    => 'KEY',
        'secret' => 'SECRET',
    ],
]);

$bucketName = 'BUCKETNAME';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        $result = $conn->query($sql);
        $products = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($products);
        break;

    case 'POST':
        // Створення нового або оновлення товару (якщо ?id= передано)
        $productId = $_GET['id'] ?? null;

        $name = $_POST['name'] ?? null;
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? null;
        $category_id = $_POST['category_id'] ?? null;

        if (empty($name) || empty($price) || empty($category_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, price, and category_id are required']);
            exit;
        }

        // --- Завантаження файлу на Cloudflare R2 ---
        $image_url = null;
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
            $filename = uniqid() . '-' . basename($_FILES['product_image']['name']);
            try {
                $upload = $s3->putObject([
                    'Bucket' => $bucketName,
                    'Key'    => $filename,
                    'SourceFile' => $_FILES['product_image']['tmp_name'],
                    'ACL'    => 'public-read',
                ]);
                $image_url = $upload['ObjectURL'];
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload image: ' . $e->getMessage()]);
                exit;
            }
        }

        if ($productId) {
            // --- UPDATE ---
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image_url = COALESCE(?, image_url) WHERE id = ?");
            $stmt->bind_param('ssdisi', $name, $description, $price, $category_id, $image_url, $productId);
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Product updated']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update product']);
            }
            $stmt->close();
        } else {
            // --- CREATE ---
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_url, category_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('ssdsi', $name, $description, $price, $image_url, $category_id);
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(['success' => true, 'product_id' => $conn->insert_id]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create product']);
            }
            $stmt->close();
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID is required']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Product deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete product']);
        }
        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

$conn->close();
?>
