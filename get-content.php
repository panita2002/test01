<?php
header('Content-Type: application/json; charset=utf-8');

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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

// ดึงข้อมูลจากฐานข้อมูลตาม ID
$sql = "SELECT * FROM editor_content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// ตรวจสอบว่าพบข้อมูลหรือไม่
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'id' => $row['id'],
        'name' => $row['name'],
        'title' => $row['title'],
        'content' => $row['content'], // เนื้อหา HTML ที่จะโหลดลงใน Unlayer
        'design' => json_decode($row['design']) // การแปลงค่า JSON ของ design ที่เก็บไว้
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Content not found']);
}

$stmt->close();
$conn->close();
?>
