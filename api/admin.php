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
require_once '../db.php';

// 3. "Міні-роутер": визначаємо, яку дію виконувати
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // ДІЯ: ОТРИМАТИ ВСІ ТОВАРИ (READ)
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        $result = $conn->query($sql);
        $products = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($products);
        break;

    case 'POST':
        // ДІЯ: СТВОРИТИ НОВИЙ ТОВАР (CREATE)
        $data = json_decode(file_get_contents('php://input'), true);

        // Валідація
        if (empty($data['name']) || empty($data['price']) || empty($data['category_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, price, and category_id are required']);
            $conn->close();
            exit;
        }

        // Якщо image_url не передано — ставимо null
        $image_url = $data['image_url'] ?? null;

        $stmt = $conn->prepare("
        INSERT INTO products (name, description, price, image_url, category_id)
        VALUES (?, ?, ?, ?, ?)
    ");
        $stmt->bind_param(
            'ssdsi',
            $data['name'],
            $data['description'],
            $data['price'],
            $image_url,
            $data['category_id']
        );

        if ($stmt->execute()) {
            http_response_code(201); // Created
            echo json_encode(['success' => true, 'product_id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create product']);
        }
        $stmt->close();
        break;


    case 'PUT':
        // ДІЯ: ОНОВИТИ ТОВАР (UPDATE)
        // Ми очікуємо ID товару в URL, наприклад: .../admin.php?id=3
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID is required in URL parameter (e.g., ?id=1)']);
            $conn->close();
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category_id = ? WHERE id = ?");
        $stmt->bind_param(
            'ssdsii', // 'i' в кінці для id
            $data['name'],
            $data['description'],
            $data['price'],
            $data['image_url'],
            $data['category_id'],
            $id
        );

        if ($stmt->execute()) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Product updated']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update product']);
        }
        $stmt->close();
        break;

    case 'DELETE':
        // ДІЯ: ВИДАЛИТИ ТОВАР (DELETE)
        // Ми очікуємо ID товару в URL, наприклад: .../admin.php?id=3
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID is required in URL parameter (e.g., ?id=1)']);
            $conn->close();
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                http_response_code(200); // OK
                echo json_encode(['success' => true, 'message' => 'Product deleted']);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'Product not found or already deleted']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete product']);
        }
        $stmt->close();
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

// Закриваємо з'єднання
$conn->close();

?>