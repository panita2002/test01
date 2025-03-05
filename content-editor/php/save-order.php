<?php
require_once("../db/db-connection.php");

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['order'])) {
    echo json_encode(["success" => false, "message" => "ข้อมูลไม่ถูกต้อง"]);
    exit();
}

try {
    $db->beginTransaction();
    foreach ($data['order'] as $item) {
        $stmt = $db->prepare("UPDATE editor_content SET position = ? WHERE id = ?");
        $stmt->execute([$item['position'], $item['id']]);
    }
    $db->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
