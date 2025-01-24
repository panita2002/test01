<?php
// Database connection
function getPDO() {
    // ข้อมูลการเชื่อมต่อฐานข้อมูล
    $config = [
        'host' => 'localhost',
        'dbname' => 'test01',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ];

    try {
        // ใช้การเชื่อมต่อ PDO
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // แสดงข้อความเมื่อเชื่อมต่อไม่สำเร็จ
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
