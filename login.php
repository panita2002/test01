<?php
session_start();

$ldap_server = "ldap://128.1.0.1:389";
$ldap_dn = "CN=moodleBind,OU=WMSL,OU=System,OU=WMSL_User,DC=wmsl,DC=local"; 

$username = $_POST["username"];
$password = $_POST["password"];

$ldap_conn = ldap_connect($ldap_server);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ldap_conn) {
    // ใช้ username และ password ที่ผู้ใช้กรอกมา bind กับ LDAP
    $user_dn = "uid=$username,$ldap_dn";

    if (@ldap_bind($ldap_conn, $user_dn, $password)) {
        $_SESSION["user_id"] = $username;
        echo "✅ ล็อกอินสำเร็จ! ยินดีต้อนรับ $username";
    } else {
        echo "❌ ล็อกอินไม่สำเร็จ: " . ldap_error($ldap_conn);
    }
} else {
    echo "❌ ไม่สามารถเชื่อมต่อ LDAP ได้";
}

ldap_close($ldap_conn);
?>
