<?php
require_once "../app/middleware/auth.php";

// Seguridad: Solo administradores
if ($_SESSION["rol_id"] != 1) {
    header("Location: /public/login.php");
    exit;
}
require_once "../app/config/database.php";

try {
    $pdo = getDBConnection();
    $pdo->exec("SET NAMES 'utf8'");

    $empresas = $pdo->query("SELECT * FROM empresas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    $usuarios = $pdo->query("
        SELECT u.id, u.nombre, u.email, r.rol, e.nombre AS empresa
        FROM usuarios u
        JOIN roles r ON u.rol_id = r.id
        JOIN empresas e ON u.empresas_id = e.id
        ORDER BY u.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $dispositivos = $pdo->query("
        SELECT d.id, d.dispositivo_uid, d.nombre, e.nombre AS empresa
        FROM dispositivos d
        JOIN empresas e ON d.empresas_id = e.id
        ORDER d.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - XunamShield</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #F4D35E;
            --primary-dark: #E6B800;
            --bg-dark: #2C2C2C;
            --bg-light: #F0F2F5;
            --text-main: #333;
            --white: #ffffff;
            --success: #28a745;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: var(--bg-light); min-height: 100vh; }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px; background: var(--bg-dark); color: white;
            padding: 20px; position: fixed; height: 100vh;
            display: flex; flex-direction: column;
        }
        .sidebar h2 { color: var(--primary); font-size: 1.5rem; margin: 20px 0 30px; text-align: center; }
        
        /* Botones de Navegación */
        .sidebar button, .sidebar .btn-link {
            width: 100%; padding: 14px 20px; margin-bottom: 12px;
            background: rgba(255,255,255,0.05); border: 1px solid transparent;
            color: #ccc; border-radius: 12px; cursor: pointer; text-align: left; 
            transition: 0.3s; text-decoration: none; display: block; font-size: 0.9rem;
        }
        .sidebar button:hover, .sidebar button.active, .sidebar .btn-link:hover {
            background: var(--primary); color: var(--bg-dark); font-weight: 600;
        }

        /* Botón Especial de Reporte */
        .btn-csv {
            background: var(--success) !important;
            color: white !important;
            margin-top: auto; /* Lo empuja hacia abajo */
            margin-bottom: 20px;
            text-align: center !important;
        }

        /* --- MAIN CONTENT --- */
        .main { flex: 1; margin-left: 260px; padding: 40px; display: flex; justify-content: center; padding-bottom: 100px; }
        .section-container {
            display: flex; flex-direction: row; gap: 30px; width: 100%;
            max-width: 1200px; justify-content: center; align-items: flex-start;
            animation: fadeIn 0.4s ease;
        }

        .card, .table-container {
            background: var(--white); padding: 30px; border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .card { flex: 1; max-width: 400px; }
        .table-container { flex: 1.5; }

        h3 { margin-bottom: 20px; color: var(--bg-dark); }
        input, select { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 10px; border: 1.5px solid #eee; background: #f9f9f9; }
        .submit {
            width: 100%; background: var(--primary); padding: 15px; border: none;
            border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        .submit:hover { background: var(--primary-dark); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid var(--primary); color: #666; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }

        .btn-delete { border: none; background: none; cursor: pointer; font-size: 1.1rem; filter: grayscale(1); transition: 0.2s; }
        .btn-delete:hover { filter: grayscale(0); transform: scale(1.2); }

        .hidden { display: none; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        footer {
            position: fixed; bottom: 0; left: 260px; right: 0; padding: 15px;
            background: var(--white); border-top: 1px solid #eee; text-align: center;
            font-size: 0.85rem; color: #888; z-index: 100;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>XunamShield 🐝</h2>
    
    <a href="../logs/auth_<?= date('Y-m-d') ?>.log" target="_blank" class="btn-link">📄 Ver Logs Hoy</a>
    
    <hr style="opacity: 0.1; margin-bottom: 15px;">

    <button onclick="showSection('empresas', this)" class="active">🏢 Empresas</button>
    <button onclick="showSection('usuarios', this)">👤 Usuarios</button>
    <button onclick="showSection('dispositivos', this)">🛡️ Dispositivos</button>

    <a href="../app/controllers/ExportController.php" class="btn-link btn-csv">
        📥 Descargar Reporte CSV
    </a>
</div>

<div class="main">
    <div id="empresas" class="section-container">
        <div class="card">
            <h3>Nueva Empresa</h3>
            <form method="POST" action="../app/controllers/EmpresaController.php">
                <input type="text" name="nombre" placeholder="Nombre de la empresa" required>
                <input type="text" name="ubicacion" placeholder="Mérida, Yucatán" required>
                <button type="submit" class="submit">Crear Empresa</button>
            </form>
        </div>
        <div class="table-container">
            <h3>Empresas Registradas</h3>
            <table>
                <thead><tr><th>ID</th><th>Nombre</th><th>Ubicación</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach($empresas as $empresa): ?>
                    <tr>
                        <td><?= $empresa['id'] ?></td>
                        <td><?= htmlspecialchars($empresa['nombre']) ?></td>
                        <td><?= htmlspecialchars($empresa['ubicacion']) ?></td>
                        <td>
                            <button class="btn-delete" onclick="if(confirm('¿Borrar empresa?')) window.location.href='../app/controllers/EmpresaController.php?delete=<?= $empresa['id'] ?>'">🗑️</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="usuarios" class="section-container hidden">
        <div class="card">
            <h3>Nuevo Usuario</h3>
            <form method="POST" action="/app/controllers/UserController.php">
                <select name="empresas_id" required>
                    <option value="">Seleccionar Empresa</option>
                    <?php foreach($empresas as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="rol_id" required>
                    <option value="1">Administrador</option>
                    <option value="2">Usuario</option>
                </select>
                <input type="text" name="nombre" placeholder="Nombre completo" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password_hash" placeholder="Password" required>
                <button type="submit" class="submit">Crear Usuario</button>
            </form>
        </div>
        <div class="table-container">
            <h3>Usuarios Registrados</h3>
            <table>
                <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Empresa</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['rol'] ?></td>
                        <td><?= htmlspecialchars($u['empresa']) ?></td>
                        <td>
                            <button class="btn-delete" onclick="if(confirm('¿Borrar usuario?')) window.location.href='../app/controllers/UserController.php?delete=<?= $u['id'] ?>'">🗑️</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="dispositivos" class="section-container hidden">
        <div class="card">
            <h3>Nuevo Dispositivo</h3>
            <form method="POST" action="../app/controllers/DeviceController.php">
                <select name="empresas_id" required>
                    <option value="">Seleccionar Empresa</option>
                    <?php foreach($empresas as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="dispositivo_uid" placeholder="UID del equipo" required>
                <input type="text" name="nombre" placeholder="Ej. Cámara Norte" required>
                <button type="submit" class="submit">Registrar</button>
            </form>
        </div>
        <div class="table-container">
            <h3>Dispositivos Registrados</h3>
            <table>
                <thead><tr><th>UID</th><th>Nombre</th><th>Empresa</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach($dispositivos as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['dispositivo_uid']) ?></td>
                        <td><?= htmlspecialchars($d['nombre']) ?></td>
                        <td><?= htmlspecialchars($d['empresa']) ?></td>
                        <td>
                            <button class="btn-delete" onclick="if(confirm('¿Eliminar dispositivo?')) window.location.href='../app/controllers/DeviceController.php?delete=<?= $d['id'] ?>'">🗑️</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer>
    &copy; <?= date("Y"); ?> <strong>XunamShield</strong> - Panel de Control | Mérida, Yucatán.
</footer>

<script>
    function showSection(sectionId, btn) {
        document.querySelectorAll('.section-container').forEach(s => s.classList.add('hidden'));
        document.querySelectorAll('.sidebar button').forEach(b => b.classList.remove('active'));
        document.getElementById(sectionId).classList.remove("hidden");
        btn.classList.add('active');
    }
</script>

</body>
</html>