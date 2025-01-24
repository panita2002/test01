<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../db/db-connection.php';

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
$id = isset($data['id']) && is_numeric($data['id']) ? intval($data['id']) : null;
$name = isset($data['name']) ? trim($data['name']) : ''; // ตัดช่องว่าง
$title = isset($data['title']) ? trim($data['title']) : '';
$content = isset($data['content']) ? trim($data['content']) : '';
$design = isset($data['design']) ? trim($data['design']) : '';
$project_id = isset($data['project_id']) && is_numeric($data['project_id']) ? intval($data['project_id']) : 0;
$category_id = isset($data['category_id']) && is_numeric($data['category_id']) ? intval($data['category_id']) : 0;
$main_topic = isset($data['main_topic']) && !empty($data['main_topic']) ? trim($data['main_topic']) : null;
$sub_topic = isset($data['sub_topic']) && !empty($data['sub_topic']) ? trim($data['sub_topic']) : null;
$sub_sub_topic = isset($data['sub_sub_topic']) && !empty($data['sub_sub_topic']) ? trim($data['sub_sub_topic']) : null;
$order_number = isset($data['order_number']) && is_numeric($data['order_number']) ? intval($data['order_number']) : 0;


// ตรวจสอบข้อมูลที่จำเป็น
if (empty($name) || empty($title) || empty($content) || empty($design) || $project_id <= 0 || $category_id <= 0 || $order_number <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

if ($id) {
    // อัปเดตข้อมูล และอัปเดตวันที่กับเวลาที่สร้าง
    $sql = "UPDATE editor_content 
            SET name = ?, title = ?, content = ?, design = ?, project_id = ?, category_id = ?, 
                main_topic = ?, sub_topic = ?, sub_sub_topic = ?, order_number = ?, 
                updated_at = CURDATE(), update_time = CURTIME() 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiiissii", $name, $title, $content, $design, $project_id, $category_id, $main_topic, $sub_topic, $sub_sub_topic, $order_number, $id);
} else {
    // สร้างข้อมูลใหม่
    $sql = "INSERT INTO editor_content (name, title, content, design, project_id, category_id, main_topic, sub_topic, sub_sub_topic, order_number, created_at, created_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiiissi", $name, $title, $content, $design, $project_id, $category_id, $main_topic, $sub_topic, $sub_sub_topic, $order_number);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Content saved successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save content.']);
}

$stmt->close();
$conn->close();
?>
