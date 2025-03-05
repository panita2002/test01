<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "test02"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
$security_question = $_POST['security_question'];
$security_answer = password_hash($_POST['security_answer'], PASSWORD_BCRYPT); 

$check_query = "SELECT * FROM users WHERE email = ? OR username = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Username or Email already exists!'); window.location.href='../html/register.html';</script>";
    exit();
}

$sql = "INSERT INTO users (username, email, password, security_question, security_answer) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $username, $email, $password, $security_question, $security_answer);

if ($stmt->execute()) {
    echo "<script>alert('Registration successful!'); window.location.href='../html/login.html';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();
$conn->close();
?>
