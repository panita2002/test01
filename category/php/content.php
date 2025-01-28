<?php
if (isset($_GET['topic'])) {
    $topic = $_GET['topic'];

    // เชื่อมต่อฐานข้อมูล
    require './db.php';

    // ค้นหาข้อมูลเนื้อหาตาม topic
    $stmt = $conn->prepare("SELECT content FROM topics WHERE title = ?");
    $stmt->bind_param("s", $topic);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo nl2br(htmlspecialchars($row['content'])); // แสดงผลเนื้อหาแบบไม่ให้มีแท็ก HTML
    } else {
        echo "<p>ไม่พบเนื้อหา</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
