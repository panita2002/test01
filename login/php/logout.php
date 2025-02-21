<?php
$session_lifetime = 10800;
session_set_cookie_params($session_lifetime);
ini_set('session.gc_maxlifetime', $session_lifetime);

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../login/html/login.html");
    exit();
}

session_unset();
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: /test01/category/php/index.php");
exit();
?>
