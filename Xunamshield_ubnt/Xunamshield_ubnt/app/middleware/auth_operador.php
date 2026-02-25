<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /public/login.php");
    exit;
}

if ($_SESSION["rol_id"] != 2) { // 2 = OPERADOR
    header("Location: /public/login.php");
    exit;
}
