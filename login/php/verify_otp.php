<?php
require 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = :token AND created_at >= NOW() - INTERVAL 15 MINUTE");
        $stmt->execute(['token' => $token]);
        $resetData = $stmt->fetch();

        if ($resetData) {
            session_start();
            $_SESSION['reset_email'] = $resetData['email'];

            echo json_encode(["status" => "success", "message" => "Token ถูกต้อง"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Token ไม่ถูกต้องหรือหมดอายุ"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
}
?>
