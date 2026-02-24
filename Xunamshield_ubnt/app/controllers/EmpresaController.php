<?php
require_once "../config/database.php";

try {
    $pdo = getDBConnection();

    /* CREAR EMPRESA */
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $stmt = $pdo->prepare("
            INSERT INTO empresas (nombre, ubicacion, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([
            $_POST["nombre"],
            $_POST["ubicacion"]
        ]);
    }

    /* ELIMINAR EMPRESA */
    if (isset($_GET["delete"])) {
        $stmt = $pdo->prepare("DELETE FROM empresas WHERE id = ?");
        $stmt->execute([(int)$_GET["delete"]]);
    }

    header("Location: /admin/dashboard.php");
    exit;

} catch (Exception $e) {
    // Para debug: descomenta la siguiente línea si sigue fallando
    // die("Error SQL: " . $e->getMessage()); 
    header("Location: /admin/dashboard.php?error=1");
    exit;
}