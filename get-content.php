<?php
header('Content-Type: text/html; charset=utf-8');

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$database = "test01";

$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed: " . $conn->connect_error);
}

// รับ ID ที่ต้องการดึงข้อมูล
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ตรวจสอบ ID ว่าถูกต้อง
if ($id <= 0) {
    http_response_code(400);
    die("Invalid ID.");
}

// ดึงข้อมูล HTML จากฐานข้อมูล
$sql = "SELECT content FROM editor_content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // ส่งคืน HTML ที่ดึงจากฐานข้อมูล
    echo $row['content'];
} else {
    http_response_code(404);
    echo "No content found.";
}

$stmt->close();
$conn->close();
?>
