<?php
// เปิด Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = ""; // เปลี่ยนรหัสผ่านให้ตรงกับ MySQL
$database = "test01"; // ชื่อฐานข้อมูล

$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// รับข้อมูลจาก request
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    die(json_encode(['success' => false, 'message' => 'Invalid JSON input']));
}

$content = $data['content'] ?? '';

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Content is empty']);
    exit;
}

// บันทึกข้อมูลลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO editor_content (content, created_at) VALUES (?, NOW())");
$stmt->bind_param("s", $content);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Content saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save content: ' . $stmt->error]);
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
