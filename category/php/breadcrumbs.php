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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$breadcrumbs = [];

$breadcrumbs[] = ["title" => "Home", "url" => "../php/index.php"];

if ($id > 0) {
    $sql = "SELECT primary_topic, secondary_topic, tertiary_topic, quaternary_topic 
            FROM editor_content WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
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

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
         strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode($breadcrumbs);
    exit;
}

?>
