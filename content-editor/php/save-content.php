<?php

header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$username = "root";
$password = "";
$database = "test02";

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
$title = isset($data['title']) && $data['title'] !== '' ? trim($data['title']) : null;
$content = isset($data['content']) ? trim($data['content']) : '';
$design = isset($data['design']) ? trim($data['design']) : '';
$project_id = isset($data['project_id']) && is_numeric($data['project_id']) ? intval($data['project_id']) : 0;
$category_id = isset($data['category_id']) && is_numeric($data['category_id']) ? intval($data['category_id']) : 0;
$primary_topic = isset($data['primary_topic']) && $data['primary_topic'] !== '' ? trim($data['primary_topic']) : '';
$secondary_topic = isset($data['secondary_topic']) && $data['secondary_topic'] !== '' ? trim($data['secondary_topic']) : null;
$tertiary_topic = isset($data['tertiary_topic']) && $data['tertiary_topic'] !== '' ? trim($data['tertiary_topic']) : null;
$quaternary_topic = isset($data['quaternary_topic']) && $data['quaternary_topic'] !== '' ? trim($data['quaternary_topic']) : null;
// ตรวจสอบข้อมูลที่จำเป็น
if (empty($name) || empty($primary_topic) ||empty($content) || empty($design) || $project_id <= 0 || $category_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input. Please check the required fields.']);
    exit;
}

try {
    if ($id) {
        $sql = "UPDATE editor_content 
                SET name = ?, title = ?, content = ?, design = ?, project_id = ?, category_id = ?, 
                    primary_topic = ?, secondary_topic = ?, tertiary_topic = ?, quaternary_topic = ?, 
                    updated_at = CURDATE(), update_time = CURTIME() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssiissssi", 
            $name,
            $title,
            $content,
            $design,
            $project_id,
            $category_id,
            $primary_topic,
            $secondary_topic,
            $tertiary_topic,
            $quaternary_topic,
            $id
        );
    } else {
        $sql = "INSERT INTO editor_content (name, title, content, design, project_id, category_id, primary_topic, secondary_topic, tertiary_topic, quaternary_topic, created_at, created_time) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssiissss", 
            $name,
            $title,
            $content,
            $design,
            $project_id,
            $category_id,
            $primary_topic,
            $secondary_topic,
            $tertiary_topic,
            $quaternary_topic
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