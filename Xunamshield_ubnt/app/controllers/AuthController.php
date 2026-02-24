<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false]);
    exit;
}

$email = $_POST["email"] ?? '';
$password = $_POST["password_hash"] ?? '';

$pdo = getDBConnection();

$stmt = $pdo->prepare("
    SELECT u.*, r.rol
    FROM USUARIOS u
    JOIN ROLES r ON u.rol_id = r.id
    WHERE u.email = ?
");

$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $password === $user["password_hash"]) {

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["nombre"] = $user["nombre"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["rol_id"] = $user["rol_id"];
    $_SESSION["rol"] = $user["rol"]; 
    $_SESSION["empresa_id"] = $user["empresas_id"];
    


    echo json_encode([
        "success" => true,
        "rol_id" => $user["rol_id"]
    ]);

} else {
    echo json_encode(["success" => false]);
}
