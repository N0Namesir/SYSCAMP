<?php
session_start();
include("configuracion/conexion.php");
$mensaje = "iniciar sesión";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM usuarios WHERE username = '$username'";
    $resultado = pg_query($conexion, $query);
    if ($resultado && pg_num_rows($resultado) == 1) {
        $usuario = pg_fetch_assoc($resultado);
        if ($usuario['password_hash'] == $password) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['rol_id'] = $usuario['rol_id'];
            $_SESSION['nombres'] = $usuario['nombres'];
            header("Location: index.php");
            exit;
        } else {
            $mensaje = "❌ Contraseña incorrecta.";
        }
    } else {
        $mensaje = 'usuario no encontrado';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #303845;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background-color: #1f252f;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
            justify-content: center;
        }

        .navbar-brand {
            color: #ffffff !important;
            margin-left: auto;
            margin-right: auto;
            font-size: 1.6rem;
        }

        .login-container {
            margin-top: 120px;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }

        .login-card {
            background-color: #29303c;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        }

        .form-control, .form-select {
            background-color: #1f252f;
            border: none;
            color: #fff;
            transition: all 0.3s ease;
            transform: scale(1);
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 10px #007bff;
            transform: scale(1.03);
            background-color: #2a2f3a;
        }

        input[type="password"] {
            color:rgb(255, 255, 255); /* Color del texto (puntos) en el campo contraseña */
        }

        .form-label {
            color: #ccc;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .titulo-login {
            text-align: center;
            margin-bottom: 20px;
        }

        .alert {
            background-color: #1f252f;
            color: #f8d7da;
            border: 1px solidrgb(24, 100, 133);
        }
    </style>
</head>
<body>

<!-- Navbar con SYSCAMP centrado -->
<nav class="navbar navbar-dark fixed-top">
    <span class="navbar-brand mx-auto">SYSCAMP</span>
</nav>

<div class="login-container">
    <h2 class="titulo-login">Iniciar Sesión</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert text-center">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="login-card">
        <div class="mb-3">
            <label for="username" class="form-label">Nombre de usuario</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


