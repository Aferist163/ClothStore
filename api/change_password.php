<?php


session_start();


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


if (!isset($_SESSION['user_id'])) {
    http_response_code(401); 
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}


require_once '../db.php'; 

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);


if (empty($data['old_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    $conn->close();
    exit;
}

if ($data['new_password'] !== $data['confirm_password']) {
    http_response_code(400);
    echo json_encode(['error' => 'New passwords do not match']);
    $conn->close();
    exit;
}

if (strlen($data['new_password']) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'New password must be at least 8 characters long']);
    $conn->close();
    exit;
}

$old_password = $data['old_password'];
$new_password = $data['new_password'];

try {
    
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404); 
        echo json_encode(['error' => 'User not found']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

   
    if (!password_verify($old_password, $user['password_hash'])) {
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Invalid old password']);
        $conn->close();
        exit;
    }


    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $update_stmt->bind_param('si', $new_password_hash, $user_id);
    
    if ($update_stmt->execute()) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } else {
        throw new Exception('Failed to update password');
    }
    
    $update_stmt->close();

} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>