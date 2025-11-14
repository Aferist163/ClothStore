<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error'=>'Access denied']); exit;
}

require_once '../db.php';
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])){
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
            $stmt->bind_param("i",$id); $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            echo json_encode($product); exit;
        }
        $sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id=c.id";
        $res = $conn->query($sql);
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if(empty($data['name'])||empty($data['price'])||empty($data['category_id'])){
            http_response_code(400); echo json_encode(['error'=>'Name, price, category_id required']); exit;
        }
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_url, category_id) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssdsi",$data['name'],$data['description'],$data['price'],$data['image_url'],$data['category_id']);
        if($stmt->execute()){ echo json_encode(['success'=>true,'product_id'=>$conn->insert_id]); }
        else{ http_response_code(500); echo json_encode(['error'=>'Failed']); }
        $stmt->close(); break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        if(!$id){ http_response_code(400); echo json_encode(['error'=>'ID required']); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image_url=?, category_id=? WHERE id=?");
        $stmt->bind_param("ssdsii",$data['name'],$data['description'],$data['price'],$data['image_url'],$data['category_id'],$id);
        if($stmt->execute()){ echo json_encode(['success'=>true]); }
        else{ http_response_code(500); echo json_encode(['error'=>'Failed']); }
        $stmt->close(); break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if(!$id){ http_response_code(400); echo json_encode(['error'=>'ID required']); exit; }
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i",$id);
        if($stmt->execute() && $stmt->affected_rows>0){ echo json_encode(['success'=>true]); }
        else{ http_response_code(404); echo json_encode(['error'=>'Not found']); }
        $stmt->close(); break;

    default: http_response_code(405); echo json_encode(['error'=>'Method not allowed']); break;
}
$conn->close();
