<?php
header('Content-Type: application/json; charset=utf-8');


// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$database = "test01";  // ใช้ฐานข้อมูลที่คุณต้องการ

$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// ดึงข้อมูลจากตารางใหม่
$sql = "SELECT id, title, created_at FROM editor_content ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $contentList = [];
    while ($row = $result->fetch_assoc()) {
        $contentList[] = $row;
    }
    echo json_encode($contentList);
} else {
    echo json_encode([]);
}

$conn->close();
?>
