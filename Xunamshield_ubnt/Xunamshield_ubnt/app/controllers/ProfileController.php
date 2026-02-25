<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $pdo = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $new_password = $_POST['new_password']; // Recibimos la contraseña
    $confirm_password = $_POST['confirm_password'];

    // 1. Validar que coincidan
    if ($new_password !== $confirm_password) {
        echo "<script>alert('Las contraseñas no coinciden'); window.history.back();</script>";
        exit;
    }

    try {
        // 2. Actualizar directamente con la variable $new_password (SIN HASH)
        // Nota: El campo en tu DB se llama password_hash, pero guardaremos texto plano.
        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        $stmt->execute([$new_password, $user_id]);

        echo "<script>
                alert('Contraseña actualizada');
                window.location.href = '../../operador/dashboard.php';
              </script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: /public/login.php");
    exit;
}