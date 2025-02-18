<?php
header('Content-Type: application/json; charset=utf-8');


$servername = "localhost";
$username = "root";
$password = "";
$database = "test02";

$conn = new mysqli($servername, $username, $password, $database);
mysqli_set_charset($conn, "utf8");


if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

try {
    
    $sql = "SELECT id, name, primary_topic, secondary_topic, tertiary_topic, quaternary_topic, created_at 
            FROM editor_content 
            ORDER BY name ASC";
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception($conn->error);
    }

    $contentList = [];
    while ($row = $result->fetch_assoc()) {
        $contentList[] = $row;
    }
    echo json_encode($contentList, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>