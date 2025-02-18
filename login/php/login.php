<?php
session_start();
require './db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT id, email, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $email, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            $_SESSION["id"] = $id;
            $_SESSION["username"] = $username;
            
            header("Location: /test01/category/php/index.php");
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
    $conn->close();
}
?>
