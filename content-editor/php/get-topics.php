<?php
header('Content-Type: application/json; charset=utf-8');

session_start();
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    http_response_code(401);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "test02";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : null;
$primary = isset($_GET['primary']) ? trim($_GET['primary']) : null;
$secondary = isset($_GET['secondary']) ? trim($_GET['secondary']) : null;
$tertiary = isset($_GET['tertiary']) ? trim($_GET['tertiary']) : null;

if (!$type) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing type parameter']);
    exit();
}

$columnMap = [
    'primary' => 'primary_topic',
    'secondary' => 'secondary_topic',
    'tertiary' => 'tertiary_topic',
    'quaternary' => 'quaternary_topic'
];

if (!isset($columnMap[$type])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid type parameter']);
    exit();
}

$column = $columnMap[$type];
$query = "SELECT DISTINCT $column FROM editor_content WHERE $column IS NOT NULL AND $column != ''";

if ($type === 'secondary' && $primary) {
    $query .= " AND primary_topic = ?";
} elseif ($type === 'tertiary' && $primary && $secondary) {
    $query .= " AND primary_topic = ? AND secondary_topic = ?";
} elseif ($type === 'quaternary' && $primary && $secondary && $tertiary) {
    $query .= " AND primary_topic = ? AND secondary_topic = ? AND tertiary_topic = ?";
}

$query .= " ORDER BY $column";
$stmt = $conn->prepare($query);

if ($type === 'secondary' && $primary) {
    $stmt->bind_param("s", $primary);
} elseif ($type === 'tertiary' && $primary && $secondary) {
    $stmt->bind_param("ss", $primary, $secondary);
} elseif ($type === 'quaternary' && $primary && $secondary && $tertiary) {
    $stmt->bind_param("sss", $primary, $secondary, $tertiary);
}

$stmt->execute();
$result = $stmt->get_result();

$topics = [];
while ($row = $result->fetch_assoc()) {
    $topics[] = $row[$column];
}

echo json_encode($topics);
$conn->close();
?>
