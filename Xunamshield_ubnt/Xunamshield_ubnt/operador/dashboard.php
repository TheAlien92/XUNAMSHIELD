<?php
require_once "../app/middleware/auth_operador.php";
require_once "../app/config/database.php";

if (!isset($_SESSION["rol_id"]) || $_SESSION["rol_id"] != 2) {
    header("Location: /public/login.php");
    exit;
}

$user_id       = $_SESSION["user_id"] ?? 0;
$empresa_id    = $_SESSION["empresas_id"] ?? $_SESSION["empresa_id"] ?? 0;
$email_display = $_SESSION["email"] ?? $_SESSION["user_email"] ?? "Correo no disponible";
$nombre_user   = $_SESSION["nombre"] ?? "Operador";

/* ================= METABASE CONFIG ================= */

$METABASE_SECRET_KEY = "5ccb66b4fac764354767bb9a3d8c576719acdea475daadadcfffdda48ff94ac0";
$METABASE_SITE_URL   = "http://192.168.158.10:3000";

$payload = [
    "resource" => ["dashboard" => 2],
    "params" => [
        "id_empresa" => [(int)$empresa_id] // Nombre del parámetro corregido
    ],
    "exp" => time() + (10 * 60)
];

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generateMetabaseToken($payload, $secret) {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $base64UrlHeader = base64url_encode(json_encode($header));
    $base64UrlPayload = base64url_encode(json_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = base64url_encode($signature);
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

$jwt_token = generateMetabaseToken($payload, $METABASE_SECRET_KEY);

/* ================= CONSULTAS ================= */

try {
    $pdo = getDBConnection();
    $pdo->exec("SET NAMES 'utf8'");

    // Corregido: dispositivos
    $stmt_dev = $pdo->prepare("SELECT id, dispositivo_uid, nombre FROM dispositivos WHERE empresas_id = ?");
    $stmt_dev->execute([$empresa_id]);
    $mis_dispositivos = $stmt_dev->fetchAll(PDO::FETCH_ASSOC);

    // Corregido: sensor_data y dispositivos
    $stmt_data = $pdo->prepare("
        SELECT d.nombre as dispositivo, s.temperatura, s.humedad, s.frecuencia, s.peso_total, s.created_at
        FROM sensor_data s
        JOIN dispositivos d ON s.dispositivos_id = d.id
        WHERE d.empresas_id = ?
        ORDER BY s.created_at DESC LIMIT 10
    ");
    $stmt_data->execute([$empresa_id]);
    $lecturas = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>
<script defer src="http://192.168.158.10:3000/app/embed.js"></script>
<script>
defineMetabaseConfig({
    theme: { preset: "light" },
    isGuest: true,
    instanceUrl: "http://192.168.158.10:3000"
});
</script>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Operador - XunamShield</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<script defer src="http://localhost:3000/app/embed.js"></script>
<script>
function defineMetabaseConfig(config) {
    window.metabaseConfig = config;
}
defineMetabaseConfig({
    theme: { preset: "light" },
    isGuest: true,
    instanceUrl: "http://localhost:3000"
});
</script>

<style>
:root { --primary:#F4D35E; --primary-dark:#E6B800; --bg-dark:#2C2C2C; --bg-light:#F0F2F5; --white:#fff; --success:#2ecc71; }
*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif;}
body{background:var(--bg-light);min-height:100vh;}

.sidebar{width:260px;background:var(--bg-dark);color:#fff;padding:40px 20px;position:fixed;height:100vh;display:flex;flex-direction:column;}
.sidebar h2{color:var(--primary);margin-bottom:40px;text-align:center;}
.sidebar button{width:100%;padding:14px;margin-bottom:10px;background:rgba(255,255,255,0.05);border:none;color:#ccc;border-radius:12px;text-align:left;cursor:pointer;transition:0.3s;}
.sidebar button.active,.sidebar button:hover{background:var(--primary);color:var(--bg-dark);font-weight:600;}

.main{margin-left:260px;padding:40px;padding-bottom:100px;}
.section-container{display:flex;flex-direction:column;gap:30px;max-width:1300px;margin:auto;}

.table-container,.card{background:#fff;padding:30px;border-radius:20px;box-shadow:0 10px 25px rgba(0,0,0,0.05);}
.table-container{overflow-x:auto;}

/* Estilos de Formulario para Perfil */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 8px; font-weight: 600; }
.form-group input { 
    width: 100%; 
    padding: 12px 15px; 
    border-radius: 10px; 
    border: 1.5px solid #eee; 
    font-size: 0.95rem; 
    transition: 0.3s;
    outline: none;
}
.form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(244, 211, 94, 0.2); }
.form-group input:disabled { background: #f5f5f5; color: #999; cursor: not-allowed; }

.btn-update { 
    width: 100%; 
    background: var(--primary); 
    color: var(--bg-dark); 
    padding: 15px; 
    border: none; 
    border-radius: 12px; 
    font-weight: 700; 
    cursor: pointer; 
    margin-top: 10px; 
    transition: 0.3s;
    font-size: 1rem;
}
.btn-update:hover { background: var(--primary-dark); transform: translateY(-2px); }

table{width:100%;border-collapse:collapse;}
th{padding:12px;text-align:left;border-bottom:2px solid var(--primary);font-size:0.85rem;color:#666;}
td{padding:12px;border-bottom:1px solid #eee;font-size:0.9rem;}

.status-badge{background:#e8f5e9;color:var(--success);padding:4px 8px;border-radius:6px;font-size:0.75rem;font-weight:600;}

.hidden{display:none;}

footer{position:fixed;bottom:0;left:260px;right:0;padding:15px;background:#fff;border-top:1px solid #eee;text-align:center;font-size:0.85rem;color:#888;}

.analytics-box{min-height:600px;}
metabase-dashboard{width:100%;height:800px;display:block;}
</style>
</head>
<body>

<div class="sidebar">
<h2>XunamShield 🐝</h2>
<button onclick="showSection('monitoreo', this)" class="active">📊 Monitoreo</button>
<button onclick="showSection('dispositivos', this)">🛡️ Mis Equipos</button>
<button onclick="showSection('perfil', this)">⚙️ Mi Perfil</button>
<button onclick="window.location.href='/public/login.php'" style="margin-top:auto;color:#ff4d4d;">🚪 Salir</button>
</div>

<div class="main">

<div id="monitoreo" class="section-container">
    <div class="table-container analytics-box">
    <h3>📊 Análisis Predictivo e Historial</h3>
    <metabase-dashboard 
        token="<?= $jwt_token ?>" 
        with-title="true" 
        with-downloads="true">
    </metabase-dashboard>
    </div>

    <div class="table-container">
    <h3>📈 Últimas Lecturas del Sensor</h3>
    <table>
    <thead>
    <tr>
    <th>Dispositivo</th><th>Temperatura</th><th>Humedad</th>
    <th>Sonido (Hz)</th><th>Peso</th><th>Fecha/Hora</th><th>Estado</th>
    </tr>
    </thead>
    <tbody>
    <?php if(empty($lecturas)): ?>
    <tr><td colspan="7" style="text-align:center;">No hay datos.</td></tr>
    <?php else: foreach($lecturas as $l): ?>
    <tr>
    <td><strong><?= htmlspecialchars($l['dispositivo']) ?></strong></td>
    <td><?= $l['temperatura'] ?>°C</td>
    <td><?= $l['humedad'] ?>%</td>
    <td><?= number_format($l['frecuencia'],1) ?> Hz</td>
    <td><?= number_format($l['peso_total'],2) ?> kg</td>
    <td><?= date("d/m/Y H:i", strtotime($l['created_at'])) ?></td>
    <td><span class="status-badge">Activo</span></td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody>
    </table>
    </div>
</div>

<div id="dispositivos" class="section-container hidden">
    <div class="table-container">
    <h3>🛡️ Equipos Vinculados</h3>
    <table>
    <thead><tr><th>ID</th><th>UID</th><th>Nombre</th></tr></thead>
    <tbody>
    <?php foreach($mis_dispositivos as $d): ?>
    <tr>
    <td><?= $d['id'] ?></td>
    <td><code><?= htmlspecialchars($d['dispositivo_uid']) ?></code></td>
    <td><?= htmlspecialchars($d['nombre']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    </div>
</div>

<div id="perfil" class="section-container hidden">
    <div class="card" style="max-width:500px;margin:40px auto;">
        <h3 style="border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 25px;">⚙️ Configuración de Perfil</h3>
        <p style="margin-bottom:25px; color: #444;">
            Hola, <strong style="color: var(--primary-dark);"><?= htmlspecialchars($nombre_user) ?></strong>. Aquí puedes actualizar tus credenciales de acceso.
        </p>

        <form method="POST" action="../app/controllers/ProfileController.php">
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="text" value="<?= htmlspecialchars($email_display) ?>" disabled title="El correo no se puede modificar">
            </div>

            <div class="form-group">
                <label>Nueva Contraseña</label>
                <input type="password" name="new_password" placeholder="Mínimo 8 caracteres" required minlength="8">
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirm_password" placeholder="Repite tu nueva contraseña" required>
            </div>

            <button type="submit" class="btn-update">
                Actualizar Credenciales
            </button>
        </form>
    </div>
</div>

</div>

<footer>
&copy; <?= date("Y"); ?> <strong>XunamShield</strong> | Protegiendo el futuro de las abejas
</footer>

<script>
function showSection(id, btn){
    document.querySelectorAll('.section-container').forEach(s=>s.classList.add('hidden'));
    document.querySelectorAll('.sidebar button').forEach(b=>b.classList.remove('active'));
    document.getElementById(id).classList.remove('hidden');
    btn.classList.add('active');
}
</script>

</body>
</html>