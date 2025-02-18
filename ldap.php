<?php
$ldap_server = "ldap://128.1.0.1:389"; // URL ของเซิร์ฟเวอร์ LDAP
$ldap_port = 389; // พอร์ตเริ่มต้นของ LDAP (636 สำหรับ LDAPS)
$ldap_user = "CN=moodleBind,OU=WMSL,OU=System,OU=WMSL_User,DC=wmsl,DC=local"; // ชื่อผู้ใช้ที่มีสิทธิ์เข้าถึง LDAP
$ldap_password = ""; // รหัสผ่าน

// เชื่อมต่อเซิร์ฟเวอร์ LDAP
$ldap_conn = ldap_connect($ldap_server, $ldap_port);

if (!$ldap_conn) {
    die("❌ ไม่สามารถเชื่อมต่อ LDAP ได้");
}

// ตั้งค่า LDAP ให้รองรับการใช้ Protocol 3
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

// พยายาม Bind (ล็อกอิน) ด้วย Admin Account
if (@ldap_bind($ldap_conn, $ldap_user, $ldap_password)) {
    echo "✅ เชื่อมต่อ LDAP สำเร็จ!";
} else {
    echo "❌ เชื่อมต่อ LDAP ไม่สำเร็จ: " . ldap_error($ldap_conn);
}

ldap_close($ldap_conn);
?>
