<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test02";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่า ID จาก URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$breadcrumbs = [];

// เพิ่ม Home เป็นรายการแรก - เพียงรายการเดียวที่คลิกได้
$breadcrumbs[] = ["title" => "Home", "url" => "../php/index.php"];

// ดึงข้อมูลหัวข้อสำหรับ ID ปัจจุบัน
if ($id > 0) {
    $sql = "SELECT primary_topic, secondary_topic, tertiary_topic, quaternary_topic 
            FROM editor_content WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // สร้างอาร์เรย์ breadcrumb ตามหัวข้อที่มี
        if (!empty($row['primary_topic'])) {
            $breadcrumbs[] = ["title" => $row['primary_topic']];
        }
        if (!empty($row['secondary_topic'])) {
            $breadcrumbs[] = ["title" => $row['secondary_topic']];
        }
        if (!empty($row['tertiary_topic'])) {
            $breadcrumbs[] = ["title" => $row['tertiary_topic']];
        }
        if (!empty($row['quaternary_topic'])) {
            $breadcrumbs[] = ["title" => $row['quaternary_topic']];
        }
    }
    $stmt->close();
}

$conn->close();

// ตรวจสอบว่ามีการเรียกผ่าน AJAX หรือไม่
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
         strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax) {
    // ส่งเป็น JSON ถ้าเป็นการเรียกผ่าน AJAX
    header('Content-Type: application/json');
    echo json_encode($breadcrumbs);
    exit;
}

?>
