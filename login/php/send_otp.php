<?php
require 'db_connect.php';
require 'functions.php';

header('Content-Type: application/json');

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ["status" => "error", "message" => "รูปแบบอีเมลไม่ถูกต้อง"];
    } else {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token) 
                                       ON DUPLICATE KEY UPDATE token=:token, created_at=NOW()");
                $stmt->execute(['email' => $email, 'token' => $token]);

                $resetLink = "http://localhost/test01/login/php/reset_password.php?token=$token";
                if (sendEmail($email, "รีเซ็ตรหัสผ่านของคุณ", "คลิกลิงก์เพื่อตั้งรหัสผ่านใหม่: <a href='$resetLink'>$resetLink</a>")) {
                    $response = ["status" => "success", "message" => "ลิงก์รีเซ็ตรหัสผ่านถูกส่งไปยังอีเมลของคุณ"];
                } else {
                    $response = ["status" => "error", "message" => "เกิดข้อผิดพลาดในการส่งอีเมล"];
                }
            } else {
                $response = ["status" => "error", "message" => "ไม่พบอีเมลนี้ในระบบ"];
            }
        } catch (PDOException $e) {
            $response = ["status" => "error", "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()];
        }
    }
}

echo json_encode($response);
exit;


?>
