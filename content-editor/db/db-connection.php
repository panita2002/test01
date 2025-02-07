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

$conn->set_charset("utf8");

function closeConnection($conn) {
    $conn->close();
}


?>
