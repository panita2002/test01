<?php
function sendEmail($to, $subject, $message) {
    $headers = "From: no-reply@yourwebsite.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (!mail($to, $subject, $message, $headers)) {
        error_log("Email sending failed to: $to"); // บันทึก error ใน log แทนการ echo
        return false;
    }
    return true;
}

?>
