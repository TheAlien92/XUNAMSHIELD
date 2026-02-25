<?php
function registrarLog($mensaje, $nivel = 'INFO', $empresa_id = null, $bytes = 0) {
    $directorioLogs = __DIR__ . '/../../logs/';
    $archivoLog = $directorioLogs . 'auth_' . date('Y-m-d') . '.log';
    
    $timestamp = date('Y-m-d H:i:s');

    // 1. DETECCIÓN DE IP REAL (Saltando el Gateway de Docker)
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    // 2. DETECCIÓN DE DISPOSITIVO (User Agent)
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Device';
    
    $empresaStr = ($empresa_id !== null) ? " [Empresa ID: $empresa_id]" : " [Empresa ID: N/A]";
    $bytesStr = " [Size: {$bytes}b]";
    
    // 3. LÓGICA DE ALERTA VISUAL
    $alerta = "";
    $esCritico = ($nivel === 'AUTH_ERROR' || $nivel === 'SECURITY' || $bytes > 100);

    if ($esCritico) {
        $borde = str_repeat("!", 50);
        $alerta = "\n$borde\n[ALERTA VISUAL - POSIBLE ANOMALÍA O FALLO]\n";
    }

    // Construcción del mensaje con IP Real y User Agent
    $contenido = $alerta . "[$timestamp] [$nivel]{$empresaStr}{$bytesStr} [IP: $ip] [Device: $userAgent] $mensaje" . ($esCritico ? "\n" . str_repeat("!", 50) . "\n\n" : PHP_EOL);
    
    if (!is_dir($directorioLogs)) {
        mkdir($directorioLogs, 0777, true);
    }
    
    // Escribir el log
    file_put_contents($archivoLog, $contenido, FILE_APPEND);

    // 4. LIMPIEZA AUTOMÁTICA (Borra logs de más de 30 días)
    // Solo se ejecuta una vez por cada 100 peticiones para no afectar el rendimiento
    if (rand(1, 100) === 1) {
        $archivos = glob($directorioLogs . "*.log");
        $ahora = time();
        foreach ($archivos as $archivo) {
            if ($ahora - filemtime($archivo) > (30 * 24 * 60 * 60)) { // 30 días en segundos
                unlink($archivo);
            }
        }
    }
}