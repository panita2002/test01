<?php
require_once 'db.php';

$pdo = getPDO();
$headingId = $_GET['id'];

$query = "SELECT content FROM editor_content WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $headingId, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo htmlspecialchars($result['content']);
} else {
    echo "Content not found.";
}
?>
