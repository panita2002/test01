<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$database = "test01";  // ตั้งชื่อฐานข้อมูลที่คุณใช้

$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $title = $_POST['title'];
    $main_topic = $_POST['main_topic'];
    $sub_topic = $_POST['sub_topic'];
    $sub_sub_topic = $_POST['sub_sub_topic'];
    $order_number = $_POST['order_number'];
    $content = $_POST['content'];
    $design = $_POST['design'];
    $project_id = $_POST['project_id'];
    $category_id = $_POST['category_id'];

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($name) || empty($content) || empty($project_id) || empty($category_id)) {
        echo "Please fill all required fields.";
        exit;
    }

    // SQL สำหรับการเพิ่มข้อมูล
    $sql = "INSERT INTO editor_content (name, title, main_topic, sub_topic, sub_sub_topic, order_number, content, design, project_id, category_id, created_at, created_time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssissii", $name, $title, $main_topic, $sub_topic, $sub_sub_topic, $order_number, $content, $design, $project_id, $category_id);

    // ตรวจสอบว่าการเพิ่มข้อมูลสำเร็จหรือไม่
    if ($stmt->execute()) {
        echo "Content inserted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    $conn->close();
}
?>
