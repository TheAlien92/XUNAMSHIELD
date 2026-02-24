<?php
require_once "../config/database.php";

try {

    $pdo = getDBConnection();

    /* CREAR EMPRESA */
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $stmt = $pdo->prepare("
            INSERT INTO EMPRESAS (nombre, ubicacion, created_at)
            VALUES (?, ?, NOW())
        ");

        $stmt->execute([
            $_POST["nombre"],
            $_POST["ubicacion"]
        ]);
    }

    /* ELIMINAR EMPRESA */
    if (isset($_GET["delete"])) {

        $stmt = $pdo->prepare("DELETE FROM EMPRESAS WHERE id = ?");
        $stmt->execute([(int)$_GET["delete"]]);
    }

    // ✅ REDIRECCIÓN FINAL
    header("Location: /admin/dashboard.php");
    exit;

} catch (Exception $e) {

    header("Location: /admin/dashboard.php?error=1");
    exit;
}
