<?php
function registrarLog($mensaje, $nivel = 'INFO', $empresa_id = null, $bytes = 0) {
    $directorioLogs = __DIR__ . '/../../logs/';
    $archivoLog = $directorioLogs . 'auth_' . date('Y-m-d') . '.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $empresaStr = ($empresa_id !== null) ? " [Empresa ID: $empresa_id]" : " [Empresa ID: N/A]";
    $bytesStr = " [Size: {$bytes}b]";
    
    // --- LÓGICA DE ALERTA VISUAL ---
    $alerta = "";
    $esCritico = ($nivel === 'AUTH_ERROR' || $nivel === 'SECURITY' || $bytes > 100);

    if ($esCritico) {
        $borde = str_repeat("!", 50);
        $alerta = "\n$borde\n[ALERTA VISUAL - POSIBLE ANOMALÍA O FALLO]\n";
    }

    $contenido = $alerta . "[$timestamp] [$nivel]{$empresaStr}{$bytesStr} [IP: $ip] $mensaje" . ($esCritico ? "\n" . str_repeat("!", 50) . "\n\n" : PHP_EOL);
    
    if (!is_dir($directorioLogs)) {
        mkdir($directorioLogs, 0777, true);
    }
    
    file_put_contents($archivoLog, $contenido, FILE_APPEND);
}