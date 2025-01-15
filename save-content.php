<?php
header('Content-Type: application/json; charset=utf-8');

// ข้อมูลการเชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$database = "test01";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// รับข้อมูล JSON จากคำขอ
$data = json_decode(file_get_contents('php://input'), true);

// รับข้อมูลจาก request
$name = isset($data['name']) ? $data['name'] : '';
$title = isset($data['title']) ? $data['title'] : '';
$content = isset($data['content']) ? $data['content'] : '';
$design = isset($data['design']) ? $data['design'] : '';

// ตรวจสอบข้อมูลที่จำเป็น
if (empty($name) || empty($title) || empty($content) || empty($design)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

// สร้างคำสั่ง SQL สำหรับการบันทึกข้อมูลใหม่
$sql = "INSERT INTO editor_content (name, title, content, design, created_at, created_time) VALUES (?, ?, ?, ?, CURDATE(), CURTIME())";

// เตรียมคำสั่ง SQL
$stmt = $conn->prepare($sql);

// ตรวจสอบการเตรียมคำสั่ง SQL
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'SQL preparation failed: ' . $conn->error]);
    exit;
}

// ทำการผูกค่าตัวแปรกับคำสั่ง SQL
$stmt->bind_param("ssss", $name, $title, $content, $design);

// ตรวจสอบว่าการบันทึกทำได้สำเร็จหรือไม่
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Content saved successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save content.']);
}

// ปิดคำสั่ง SQL และการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
