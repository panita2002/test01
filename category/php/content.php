<?php
require_once 'db.php';

// รับค่า project_id (ซึ่งเป็นค่า sub_sub_topic ที่เลือกจาก sidebar)
if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
    
    // คำสั่ง SQL เพื่อดึงข้อมูลจากฐานข้อมูลตาม project_id
    $pdo = getPDO();
    $query = "SELECT * FROM editor_content WHERE CONCAT(main_topic, '\t', sub_topic, '\t', sub_sub_topic) = :project_id";

    // เตรียมการ query
    $stmt = $pdo->prepare($query);
    $stmt->execute([':project_id' => $projectId]);
    
    // ดึงข้อมูล
    $topic = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($topic) {
        // ถ้ามีข้อมูลให้แสดง content
        echo '<h2>' . htmlspecialchars($topic['main_topic']) . ' - ' . htmlspecialchars($topic['sub_topic']) . ' - ' . htmlspecialchars($topic['sub_sub_topic']) . '</h2>';
        echo '<div class="content-display">';
        echo nl2br(htmlspecialchars($topic['content']));  // แสดง content พร้อมกับแปลงการขึ้นบรรทัดใหม่
        echo '</div>';
    } else {
        // ถ้าไม่พบข้อมูล
        echo '<p>Content not found.</p>';
    }
} else {
    // ถ้าไม่มีการส่ง project_id
    echo '<p>No project selected.</p>';
}
?>
