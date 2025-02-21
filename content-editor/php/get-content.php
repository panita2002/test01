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

if ($id) {
    $sql = "SELECT * FROM editor_content WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'id' => $row['id'],
            'name' => $row['name'],
            'project_id' => $row['project_id'],
            'category_id' => $row['category_id'],
            'content' => $row['content'],
            'design' => json_decode($row['design']),
            'primary_topic' => $row['primary_topic'],
            'secondary_topic' => $row['secondary_topic'],
            'tertiary_topic' => $row['tertiary_topic'],
            'quaternary_topic' => $row['quaternary_topic']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Content not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => true, 'message' => 'Create new content']);
}

$conn->close();
?>