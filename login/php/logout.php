<?php
session_start();
session_destroy();
header("Location: /test01/category/php/index.php");
exit();
