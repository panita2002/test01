<?php
// db.php - สำหรับการเชื่อมต่อฐานข้อมูล

function getPDO() {
    // ปรับการเชื่อมต่อฐานข้อมูลตามรายละเอียดของคุณ
    $dsn = 'mysql:host=localhost;dbname=test01';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }
}
?>
