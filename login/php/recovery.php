<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['securityQuestion']) || !isset($data['securityAnswer'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => 'ข้อมูลไม่ครบถ้วน'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users 
                        WHERE username = :username 
                        AND security_question = :security_question");

    $username = trim($data['username']);
    $securityQuestion = trim($data['securityQuestion']);
    $securityAnswer = trim($data['securityAnswer']);

    if (empty($username) || empty($securityQuestion) || empty($securityAnswer)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => 'ข้อมูลว่างเปล่า กรุณากรอกข้อมูลให้ครบ'
        ]);
        exit;
    }

    $stmt->execute([
        ':username' => $username,
        ':security_question' => $securityQuestion
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($securityAnswer, $user['security_answer'])) {
            error_log("Successful recovery attempt for user: " . $username);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'ยืนยันตัวตนสำเร็จ'
            ]);
        } else {
            error_log("Failed recovery attempt for user: " . $username);
            
            http_response_code(401);
            echo json_encode([
                'status' => 'error', 
                'message' => 'คำตอบไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง'
            ]);
        }
    } else {
        error_log("Failed recovery attempt for user: " . $username);
        
        http_response_code(401);
        echo json_encode([
            'status' => 'error', 
            'message' => 'ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง'
        ]);
    }
} catch(PDOException $e) {
    error_log("Database error in recovery: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล'
    ]);
}
?>
