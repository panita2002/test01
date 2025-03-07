<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => 'ข้อมูลไม่ครบถ้วน'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT security_question FROM users WHERE username = :username");
    $username = trim($data['username']);

    if (empty($username)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => 'กรุณากรอกชื่อผู้ใช้'
        ]);
        exit;
    }

    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'status' => 'success',
            'security_question' => $user['security_question']
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'ชื่อผู้ใช้ไม่ถูกต้อง'
        ]);
    }
} catch(PDOException $e) {
    error_log("Database error in checking username: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล'
    ]);
}
?>
