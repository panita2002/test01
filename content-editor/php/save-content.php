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

$data = json_decode(file_get_contents('php://input'), true);

$id = isset($data['id']) && is_numeric($data['id']) ? intval($data['id']) : null;
$name = isset($data['name']) ? trim($data['name']) : '';
$content = isset($data['content']) ? trim($data['content']) : '';
$design = isset($data['design']) ? trim($data['design']) : '';
$project_id = isset($data['project_id']) && is_numeric($data['project_id']) ? intval($data['project_id']) : 0;
$category_id = isset($data['category_id']) && is_numeric($data['category_id']) ? intval($data['category_id']) : 0;

$primary_topic = !empty($data['primary_topic']) ? trim($data['primary_topic']) : null;
$secondary_topic = !empty($data['secondary_topic']) ? trim($data['secondary_topic']) : null;
$tertiary_topic = !empty($data['tertiary_topic']) ? trim($data['tertiary_topic']) : null;
$quaternary_topic = !empty($data['quaternary_topic']) ? trim($data['quaternary_topic']) : null;

if (empty($name) || empty($content) || empty($design) || $project_id <= 0 || $category_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input. Please check the required fields.']);
    exit;
}

try {
    if ($id) {
        $sql = "UPDATE editor_content 
                SET name = ?, content = ?, design = ?, project_id = ?, category_id = ?, 
                    primary_topic = ?, secondary_topic = ?, tertiary_topic = ?, quaternary_topic = ?, 
                    updated_at = NOW() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssiissssi", 
            $name,
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
        $sql = "INSERT INTO editor_content 
                (name, content, design, project_id, category_id, 
                 primary_topic, secondary_topic, tertiary_topic, quaternary_topic, 
                 created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssiissss", 
            $name,
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
        $responseId = $id ?? $conn->insert_id;
        http_response_code($id ? 200 : 201);  // **200 = UPDATE, 201 = INSERT**
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
