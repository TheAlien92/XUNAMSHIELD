<?php
session_start();
require_once "../config/database.php";
require_once "../config/logger.php"; 

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $respuesta = json_encode(["success" => false]);
    registrarLog("Acceso no permitido por método " . $_SERVER["REQUEST_METHOD"], "SECURITY", null, strlen($respuesta));
    echo $respuesta;
    exit;
}

$email = $_POST["email"] ?? '';
$password = $_POST["password_hash"] ?? '';

$pdo = getDBConnection();

$stmt = $pdo->prepare("
    SELECT u.*, r.rol
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
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

    // Preparamos la respuesta para medirla
    $datosRespuesta = [
        "success" => true,
        "rol_id" => $user["rol_id"]
    ];
    $jsonRespuesta = json_encode($datosRespuesta);
    $bytes = strlen($jsonRespuesta);

    // Registro con Bytes y Empresa
    registrarLog("Login EXITOSO: Usuario $email", "SUCCESS", $user["empresas_id"], $bytes);

    echo $jsonRespuesta;

} else {
    $datosRespuesta = ["success" => false];
    $jsonRespuesta = json_encode($datosRespuesta);
    $bytes = strlen($jsonRespuesta);

    // Registro de fallo con Bytes
    registrarLog("Login FALLIDO: Credenciales incorrectas para [$email]", "AUTH_ERROR", null, $bytes);
    
    echo $jsonRespuesta;
}