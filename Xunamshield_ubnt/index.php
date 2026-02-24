<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XunamShield | Protección y Monitoreo Apícola</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f59e0b; /* Ámbar Abeja */
            --primary-dark: #d97706;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-light: #f1f5f9;
            --accent: #38bdf8; /* Azul tecnológico */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(245, 158, 11, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(56, 189, 248, 0.05) 0%, transparent 20%);
            color: var(--text-light);
            line-height: 1.6;
        }

        /* --- Header --- */
        header {
            padding: 1.5rem 10%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(245, 158, 11, 0.3);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span { color: #fff; }

        header .login-btn {
            text-decoration: none;
            padding: 0.6rem 1.5rem;
            border: 1px solid var(--primary);
            color: var(--primary);
            border-radius: 50px;
            font-weight: 600;
            transition: 0.3s;
        }

        header .login-btn:hover {
            background: var(--primary);
            color: var(--bg-dark);
        }

        /* --- Hero Section --- */
        .hero {
            padding: 100px 10%;
            text-align: center;
            background: url('https://www.transparenttextures.com/patterns/hexellence.png'); /* Sutil patrón de panal */
        }

        .hero h2 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            max-width: 700px;
            margin: 0 auto 2.5rem;
            font-size: 1.2rem;
            color: #94a3b8;
        }

        .cta-main {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: var(--primary);
            color: var(--bg-dark);
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
            transition: 0.3s transform;
        }

        .cta-main:hover {
            transform: translateY(-3px);
            background: var(--primary-dark);
        }

        /* --- Grid de Contenido --- */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 50px 10%;
            max-width: 1400px;
            margin: auto;
        }

        .card {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
        }

        .card h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card p {
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        /* --- Lista de Servicios --- */
        .services-list {
            list-style: none;
            margin-top: 1.5rem;
        }

        .services-list li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .services-list li::before {
            content: "🐝";
            font-size: 0.8rem;
        }

        /* --- Footer --- */
        footer {
            margin-top: 50px;
            padding: 4rem 10%;
            background: #020617;
            text-align: center;
            border-top: 1px solid rgba(245, 158, 11, 0.2);
        }

        .footer-logo {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1rem;
            display: block;
        }

        .copyright {
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header { padding: 1rem 5%; }
            .hero { padding: 60px 5%; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 .587l3.668 2.117v4.234L12 9.055 8.332 6.938V2.704L12 .587zM21.668 8.117v4.234L18 14.468l-3.668-2.117v-4.234L18 5.999l3.668 2.118zM6 8.117v4.234L2.332 14.468 0 13.117v-4.234L3.668 6.766 6 8.117zm12 8.234v4.234l-3.668 2.117-3.668-2.117v-4.234l3.668-2.117 3.668 2.117zM6 16.351v4.234l-3.668 2.117-2.332-1.351v-4.234l3.668-2.117 2.332 1.351z"/>
        </svg>
        Xunam<span>Shield</span>
    </div>
    <a href="public/login.php" class="login-btn">Iniciar Sesión</a>
</header>

<section class="hero">
    <h2>Monitoreo Inteligente <br> para Abejas Apis</h2>
    <p>
        Protegemos el futuro de la biodiversidad mediante sensores IoT y análisis de datos avanzado para el manejo de <strong>Apis mellifera</strong>.
    </p>
    <a href="public/login.php" class="cta-main">Acceder al Sistema v2.0</a>
</section>

<main class="content-grid">
    <div class="card">
        <h3><span>🏢</span> ¿Quiénes Somos?</h3>
        <p>
            XunamShield es un ecosistema tecnológico diseñado para el apicultor moderno. Integramos seguridad digital y biológica para transformar apiarios tradicionales en unidades de producción inteligentes.
        </p>
    </div>

    <div class="card">
        <h3><span>🐝</span> La Apis Mellifera</h3>
        <p>
            Más que productoras de miel, son el motor de nuestros ecosistemas. Nuestra tecnología respeta su comportamiento natural mientras optimizamos su salud y productividad mediante monitoreo no invasivo.
        </p>
    </div>

    <div class="card">
        <h3><span>🛠️</span> Servicios Tech</h3>
        <ul class="services-list">
            <li>Telemetría de temperatura y humedad</li>
            <li>Alertas de intrusión y movimiento</li>
            <li>Análisis predictivo de floración</li>
            <li>Reportes de salud de la colmena</li>
            <li>Gestión multi-apiario centralizada</li>
        </ul>
    </div>
</main>

<footer>
    <span class="footer-logo">XUNAMSHIELD</span>
    <p>Ingeniería aplicada a la preservación ambiental.</p>
    <p class="copyright">© 2026 Todos los derechos reservados.</p>
</footer>

</body>
</html>