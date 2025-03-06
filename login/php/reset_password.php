<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['newPassword']) || empty($data['username']) || empty($data['newPassword'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'ข้อมูลไม่ครบถ้วน กรุณากรอกข้อมูลให้ครบ'
    ]);
    exit;
}

if (strlen($data['newPassword']) < 6) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร'
    ]);
    exit;
}

if (!preg_match('/[A-Za-z]/', $data['newPassword']) || !preg_match('/\d/', $data['newPassword'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'รหัสผ่านต้องมีทั้งตัวอักษรและตัวเลข'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่พบผู้ใช้ในระบบ'
        ]);
        exit;
    }
    
    if (password_verify($data['newPassword'], $user['password'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'รหัสผ่านนี้ถูกใช้ไปแล้ว กรุณาตั้งรหัสผ่านใหม่ที่แตกต่างจากเดิม'
        ]);
        exit;
    }

    $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
    $stmt->execute([
        ':password' => $hashedPassword,
        ':username' => $data['username']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'เปลี่ยนรหัสผ่านสำเร็จ']);
} catch(PDOException $e) {
    error_log("Database error in reset_password: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'ไม่สามารถเปลี่ยนรหัสผ่านได้ เนื่องจากข้อผิดพลาดของระบบ'
    ]);
}

?>
