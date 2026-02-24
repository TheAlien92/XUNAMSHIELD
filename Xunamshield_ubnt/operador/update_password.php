<?php
require_once "../app/middleware/auth_operador.php";
require_once "../app/config/database.php";

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newPassword = password_hash($_POST["new_password"], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET password_hash = :password
        WHERE id = :user_id
    ");

    $stmt->execute([
        'password' => $newPassword,
        'user_id' => $user_id
    ]);

    header("Location: dashboard.php?updated=1");
    exit;
}
