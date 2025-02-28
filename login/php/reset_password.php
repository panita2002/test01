<?php
require 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        echo json_encode(["status" => "error", "message" => "รหัสผ่านต้องมีอย่างน้อย 8 ตัวและมีตัวอักษรและตัวเลข"]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    session_start();
    if (!isset($_SESSION['reset_email'])) {
        echo json_encode(["status" => "error", "message" => "Session หมดอายุ"]);
        exit;
    }

    $email = $_SESSION['reset_email'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        $stmt->execute(['password' => $hashedPassword, 'email' => $email]);

        // ลบ token ออกจาก database
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
        $stmt->execute(['email' => $email]);

        session_destroy();
        echo json_encode(["status" => "success", "message" => "เปลี่ยนรหัสผ่านสำเร็จ"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
}
?>
