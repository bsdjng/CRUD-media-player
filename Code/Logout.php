<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

session_destroy();
Header('Location: main.php');
exit();
