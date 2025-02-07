<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once "../db/db-connection.php"; // เชื่อมต่อฐานข้อมูล

$sql = "SELECT * FROM topics";
$result = $conn->query($sql);

$topics = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $topics]);
} else {
    echo json_encode(["status" => "error", "message" => "No records found"]);
}

$conn->close();
?>
