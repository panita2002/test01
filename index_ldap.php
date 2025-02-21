<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION["username"])) {
    header("Location: ../../login/html/login.html"); // ถ้ายังไม่ได้ล็อกอินให้ไปที่หน้า Login
    exit();
}

// เชื่อมต่อ LDAP เพื่อดึงข้อมูลผู้ใช้
$ldap_server = "ldap://128.1.0.1:389";
$ldap_dn = "CN=moodleBind,OU=WMSL,OU=System,OU=WMSL_User,DC=wmsl,DC=local";
$ldap_password = "";

// เชื่อมต่อกับเซิร์ฟเวอร์ LDAP
$ldap_conn = ldap_connect($ldap_server);
if (!$ldap_conn) {
    die("❌ ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ LDAP");
}

// ตั้งค่าให้ใช้ LDAP Protocol Version 3
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Bind ด้วยบัญชีแอดมิน
if (!ldap_bind($ldap_conn, $ldap_dn, $ldap_password)) {
    die("❌ ไม่สามารถเชื่อมต่อ LDAP (Bind failed)");
}

// ค้นหาข้อมูลของผู้ใช้จาก LDAP
$username = $_SESSION["username"];
$search_dn = "ou=users,dc=example,dc=com"; // ตำแหน่งของบัญชีผู้ใช้ใน LDAP
$filter = "(cn=$username)"; // ค้นหาผู้ใช้ตาม cn
$search = ldap_search($ldap_conn, $search_dn, $filter);
$entries = ldap_get_entries($ldap_conn, $search);

// ดึงข้อมูลผู้ใช้จาก LDAP
if ($entries["count"] > 0) {
    $user_data = $entries[0];
    $email = $user_data["mail"][0] ?? "N/A";
} else {
    $email = "ไม่พบข้อมูล";
}

// ปิดการเชื่อมต่อ LDAP
ldap_close($ldap_conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบแสดงเอกสาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('../php/header.php'); ?>
<?php include('../php/sidebar.php'); ?>

<div class="container mt-4">
    <h3>ยินดีต้อนรับ, <?php echo htmlspecialchars($username); ?> 👋</h3>
    <p>อีเมลของคุณ: <?php echo htmlspecialchars($email); ?></p>
    <a href="../../login/php/logout.php" class="btn btn-danger">Logout</a>
</div>

</body>
</html>
