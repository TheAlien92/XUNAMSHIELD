<?php
require_once "../config/database.php";

try {

    $pdo = getDBConnection();

    /* ========================
       🟢 CREAR USUARIO
    ======================== */
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $stmt = $pdo->prepare("
            INSERT INTO USUARIOS 
            (empresas_id, rol_id, nombre, email, password_hash, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $_POST["empresas_id"],
            $_POST["rol_id"],
            $_POST["nombre"],
            $_POST["email"],
            $_POST["password_hash"]
        ]);
    }

    /* ========================
       🔴 ELIMINAR USUARIO
    ======================== */
    if (isset($_GET["delete"])) {

        $stmt = $pdo->prepare("DELETE FROM USUARIOS WHERE id = ?");
        $stmt->execute([(int)$_GET["delete"]]);
    }

    // ✅ REDIRECCIÓN SI TODO SALE BIEN
    header("Location: /admin/dashboard.php");
    exit;

} catch (Exception $e) {

    header("Location: /admin/dashboard.php?error=1");
    exit;
}
