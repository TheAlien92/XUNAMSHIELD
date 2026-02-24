<?php
session_start();
require_once '../app/config/database.php';

$error = "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XunamShield Login</title>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Inter', sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: url('https://images.unsplash.com/photo-1603386329225-868f9b1ee6c6?q=80&w=1974') no-repeat center center/cover;
}

/* Overlay oscuro */
body::before{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.75);
    z-index:0;
}

.login-container{
    position:relative;
    z-index:1;
    width:420px;
    background:#f4f4f4;
    border-radius:20px;
    padding:40px 35px;
    border:3px solid #d4a017;
    box-shadow:0 15px 40px rgba(0,0,0,0.4);
}

.logo{
    text-align:center;
    margin-bottom:15px;
    font-size:14px;
    color:#d4a017;
}

h1{
    text-align:center;
    font-size:32px;
    color:#111827;
    margin-bottom:10px;
}

.subtitle{
    text-align:center;
    font-size:14px;
    color:#6b7280;
    margin-bottom:30px;
}

.form-group{
    margin-bottom:20px;
}

label{
    font-size:14px;
    font-weight:500;
    color:#111827;
    display:block;
    margin-bottom:8px;
}

input{
    width:100%;
    padding:12px 14px;
    border-radius:12px;
    border:1px solid #cbd5e1;
    font-size:14px;
    outline:none;
    transition:0.3s;
}

input:focus{
    border-color:#1d4ed8;
    box-shadow:0 0 0 2px rgba(29,78,216,0.2);
}

.remember{
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:25px;
    font-size:14px;
    color:#374151;
}

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:25px;
    background:#1d6fd8;
    color:white;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#155ab6;
}

.divider{
    margin:25px 0;
    text-align:center;
    font-size:12px;
    color:#6b7280;
    position:relative;
}

.divider::before,
.divider::after{
    content:"";
    position:absolute;
    top:50%;
    width:40%;
    height:1px;
    background:#cbd5e1;
}

.divider::before{
    left:0;
}

.divider::after{
    right:0;
}

.error{
    color:red;
    font-size:13px;
    text-align:center;
    margin-top:10px;
    display:none;
}

.success{
    color:green;
    font-size:13px;
    text-align:center;
    margin-top:10px;
    display:none;
}
</style>
</head>
<body>

<div class="login-container">
    <div class="logo">🛡</div>
    <h1>XunamShield</h1>
    <p class="subtitle">Protegiendo tu colmena digital con seguridad de grado militar.</p>

   <form id="loginForm" method="POST" action="">
        <div class="form-group">
            <label>Email</label>
            <input type="text" id="email" name="email" placeholder="tu@colmena.com" required>

        </div>

       <div class="form-group">
    <label>Contraseña</label>
    <input type="password" id="password" name="password" placeholder="••••••••" required>
    </div>


        <button type="submit">Ingresar a la Colmena</button>

        <?php if(!empty($error)): ?>
            <div class="error" style="display:block;">
        <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <div class="success" id="successMsg">Acceso concedido</div>
    </form>
</div>

<script>

const form = document.getElementById("loginForm");

form.addEventListener("submit", async function(e){
    e.preventDefault();

    const response = await fetch("/app/controllers/AuthController.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            email: document.getElementById("email").value,
            password_hash: document.getElementById("password").value
        })
    });

    const data = await response.json();

    if(data.success){

        if(data.rol_id == 1){
            window.location.href = "/admin/dashboard.php";
        } 
        else if(data.rol_id == 2){
            window.location.href = "/operador/dashboard.php";
        }

    } else {
        alert("Credenciales incorrectas");
    }

});

</script>


</body>
</html>
