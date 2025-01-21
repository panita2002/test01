<?php
// การเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$database = "test01";

$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert into headings
if (isset($_POST['insert_heading'])) {
    $title = $_POST['heading_title'];
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $order = $_POST['order'];

    $sql = "INSERT INTO headings (parent_id, title, `order`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $parent_id, $title, $order);

    if ($stmt->execute()) {
        echo "Heading added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Insert into tables
if (isset($_POST['insert_table'])) {
    $heading_id = $_POST['heading_id'];
    $title = $_POST['table_title'];

    $sql = "INSERT INTO tables (heading_id, title) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $heading_id, $title);

    if ($stmt->execute()) {
        echo "Table added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Insert into table_rows
if (isset($_POST['insert_row'])) {
    $table_id = $_POST['table_id'];
    $variable = $_POST['variable'];
    $value = $_POST['value'];
    $description = !empty($_POST['description']) ? $_POST['description'] : null;

    $sql = "INSERT INTO table_rows (table_id, variable, value, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $table_id, $variable, $value, $description);

    if ($stmt->execute()) {
        echo "Row added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
