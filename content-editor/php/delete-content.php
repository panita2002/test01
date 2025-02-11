<?php
header('Content-Type: application/json; charset=utf-8');


$servername = "localhost";
$username = "root";
$password = "";
$database = "test02";


$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}


$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    exit;
}


$sql = "DELETE FROM editor_content WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Content deleted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete content.']);
}

$stmt->close();
$conn->close();
?>
