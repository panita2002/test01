<?php
session_start();
require './db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $_SESSION["error"] = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน!";
        header("Location: ../../login/html/login.html");
        exit();
    }

    try {
        $sql = "SELECT id, email, password FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user["password"])) {
                session_regenerate_id(true);

                $_SESSION["id"] = $user["id"];
                $_SESSION["username"] = $username;

                header("Location: /test01/category/php/index.php");
                exit();
            }
        }
        
        $_SESSION["error"] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
        header("Location: ../../login/html/login.html");
        exit();

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION["error"] = "เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง!";
        header("Location: ../../login/html/login.html");
        exit();
    }
}

?>
