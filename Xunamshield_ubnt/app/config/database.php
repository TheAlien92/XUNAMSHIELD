<?php

function getDBConnection() {
    $host = "192.168.158.10";
    $port = "5432";
    $dbname = "xunam_db";
    $user = "xunam_alien";
    $password = "Xunam3322";

    try {
        return new PDO(
            "pgsql:host=$host;port=$port;dbname=$dbname",
            $user,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        die("Error DB: " . $e->getMessage());
    }
}
