<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "test02"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); 

// รับค่าคำถามกู้คืนรหัสผ่าน
$security_question = $_POST['security_question'];
$security_answer = password_hash($_POST['security_answer'], PASSWORD_BCRYPT);

// ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลมีอยู่แล้วหรือไม่
$check_query = "SELECT * FROM users WHERE email = ? OR username = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้แล้ว!'); window.location.href='../html/signup.html';</script>";
    exit();
}

// เพิ่มข้อมูลลงฐานข้อมูล
$sql = "INSERT INTO users (username, email, password, security_question, security_answer) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $username, $email, $password, $security_question, $security_answer);

if ($stmt->execute()) {
    echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location.href='../html/login.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
