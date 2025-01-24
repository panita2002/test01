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

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id) {
    // ดึงข้อมูลจากฐานข้อมูลตาม ID
    $sql = "SELECT * FROM editor_content WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'id' => $row['id'],
            'name' => $row['name'],
            'title' => $row['title'],
            'project_id' => $row['project_id'],
            'category_id' => $row['category_id'],
            'content' => $row['content'],
            'design' => json_decode($row['design']),
            'main_topic' => $row['main_topic'],
            'sub_topic' => $row['sub_topic'],
            'sub_sub_topic' => $row['sub_sub_topic'],
            'order_number' => $row['order_number']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Content not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => true, 'message' => 'Create new content']);
}

$conn->close();
?>
