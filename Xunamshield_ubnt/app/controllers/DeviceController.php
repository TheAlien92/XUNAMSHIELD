<?php
require_once "../config/database.php";

try {

    $pdo = getDBConnection();

    /* =========================
       🔴 ELIMINAR DISPOSITIVO
    ==========================*/
    if (isset($_GET["delete"])) {

        $id = (int) $_GET["delete"];

        $stmt = $pdo->prepare("DELETE FROM DISPOSITIVOS WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /admin/dashboard.php");
        exit;
    }

    /* =========================
       🟢 CREAR DISPOSITIVO
    ==========================*/
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $api_key = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("
            INSERT INTO DISPOSITIVOS
            (empresas_id, dispositivo_uid, nombre, api_key, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $_POST["empresas_id"],
            $_POST["dispositivo_uid"],
            $_POST["nombre"],
            $api_key
        ]);

        header("Location: /admin/dashboard.php");
        exit;
    }

} catch (Exception $e) {

    // Manejo limpio del error
    echo "<div style='font-family:sans-serif;padding:40px;text-align:center'>
            <h2>Error en Dispositivos 🐝</h2>
            <p>No se pudo completar la operación.</p>
            <a href='/admin/dashboard.php'>Volver</a>
          </div>";
    exit;
}
