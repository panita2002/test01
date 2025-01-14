<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test01";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$title = $data['title'];
$body = $data['body'];

$sql = "UPDATE content SET title = ?, body = ? WHERE id = 1"; // อัพเดตเนื้อหาที่ id=1
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $title, $body);
$stmt->execute();

$stmt->close();
$conn->close();
?>
