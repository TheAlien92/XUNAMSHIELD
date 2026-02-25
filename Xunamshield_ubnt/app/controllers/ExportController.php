<?php
session_start();
// Seguridad: Solo Admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    http_response_code(403);
    die("Acceso denegado");
}

$fecha = date('Y-m-d');
$logFile = __DIR__ . "/../../logs/auth_$fecha.log";

if (!file_exists($logFile)) {
    die("No hay logs registrados para el día de hoy ($fecha).");
}

// Nombre del archivo de descarga
$filename = "Audit_Logs_$fecha.csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Añadir BOM para que Excel reconozca tildes y caracteres especiales
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabecera del CSV
fputcsv($output, ['Reporte de Actividad XunamShield - ' . $fecha]);
fputcsv($output, ['Registro Completo del Evento']); 

// Leer el archivo de log línea por línea
$lineas = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lineas as $linea) {
    // Si la línea contiene los separadores "!!!!", la limpiamos un poco para el CSV
    if (strpos($linea, '!!!!') === false && strpos($linea, '[ALERTA') === false) {
        fputcsv($output, [$linea]);
    } else {
        // Mantenemos las alertas pero sin tantas exclamaciones para que sea legible
        $limpia = str_replace('!', '', $linea);
        if(!empty(trim($limpia))) fputcsv($output, [$limpia]);
    }
}

fclose($output);
exit;