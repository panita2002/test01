<?php
// การตั้งค่าข้อมูลสำหรับเชื่อมต่อฐานข้อมูล
$servername = "localhost"; // หรือ IP ของเซิร์ฟเวอร์
$username = "root"; // ชื่อผู้ใช้ MySQL
$password = ""; // รหัสผ่าน MySQL
$dbname = "test01"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// กำหนด character set เป็น UTF-8
$conn->set_charset("utf8");
?>
