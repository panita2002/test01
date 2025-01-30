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
$name = isset($data['name']) ? trim($data['name']) : '';
$main_topic = isset($data['title']) && $data['title'] !== '' ? trim($data['title']) : null;
$content = isset($data['content']) ? trim($data['content']) : '';
$design = isset($data['design']) ? trim($data['design']) : '';
$project_id = isset($data['project_id']) && is_numeric($data['project_id']) ? intval($data['project_id']) : 0;
$category_id = isset($data['category_id']) && is_numeric($data['category_id']) ? intval($data['category_id']) : 0;
$main_topic = isset($data['main_topic']) && $data['main_topic'] !== '' ? trim($data['main_topic']) : '';
$sub_topic = isset($data['sub_topic']) && $data['sub_topic'] !== '' ? trim($data['sub_topic']) : null;
$sub_sub_topic = isset($data['sub_sub_topic']) && $data['sub_sub_topic'] !== '' ? trim($data['sub_sub_topic']) : null;
$order_number = isset($data['order_number']) && is_numeric($data['order_number']) ? intval($data['order_number']) : 0;

// ตรวจสอบข้อมูลที่จำเป็น
if (empty($name) || empty($main_topic) ||empty($content) || empty($design) || $project_id <= 0 || $category_id <= 0 || $order_number <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input. Please check the required fields.']);
    exit;
}

try {
    if ($id) {
        $sql = "UPDATE editor_content 
                SET name = ?, title = ?, content = ?, design = ?, project_id = ?, category_id = ?, 
                    main_topic = ?, sub_topic = ?, sub_sub_topic = ?, order_number = ?, 
                    updated_at = CURDATE(), update_time = CURTIME() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssiisssii", 
            $name, 
            $title, 
            $content, 
            $design, 
            $project_id, 
            $category_id, 
            $main_topic, 
            $sub_topic, 
            $sub_sub_topic, 
            $order_number, 
            $id
        );
    } else {
        $sql = "INSERT INTO editor_content (name, title, content, design, project_id, category_id, main_topic, sub_topic, sub_sub_topic, order_number, created_at, created_time) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssiisssi", 
            $name, 
            $title, 
            $content, 
            $design, 
            $project_id, 
            $category_id, 
            $main_topic, 
            $sub_topic, 
            $sub_sub_topic, 
            $order_number
        );
    }

    if ($stmt->execute()) {
        $responseId = $id ?? $conn->insert_id; // ใช้ ID ล่าสุดในกรณีเพิ่มข้อมูลใหม่
        echo json_encode(['success' => true, 'message' => 'Content saved successfully.', 'id' => $responseId]);
    } else {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
