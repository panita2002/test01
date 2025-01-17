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
$id = isset($data['id']) ? intval($data['id']) : null;
$name = isset($data['name']) ? $data['name'] : '';
$title = isset($data['title']) ? $data['title'] : '';
$content = isset($data['content']) ? $data['content'] : '';
$design = isset($data['design']) ? $data['design'] : '';
$project_id = isset($data['project_id']) ? intval($data['project_id']) : 0;
$category_id = isset($data['category_id']) ? intval($data['category_id']) : 0;

// ตรวจสอบข้อมูลที่จำเป็น
if (empty($name) || empty($title) || empty($content) || empty($design) || $project_id <= 0 || $category_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

if ($id) {
    // อัปเดตข้อมูล และอัปเดตวันที่กับเวลาที่สร้าง
    $sql = "UPDATE editor_content 
            SET name = ?, title = ?, content = ?, design = ?, project_id = ?, category_id = ?, updated_at = CURDATE(), update_time = CURTIME() 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $name, $title, $content, $design, $project_id, $category_id, $id);
} else {
    // สร้างข้อมูลใหม่
    $sql = "INSERT INTO editor_content (name, title, content, design, project_id, category_id, created_at, created_time) 
            VALUES (?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $name, $title, $content, $design, $project_id, $category_id);
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
