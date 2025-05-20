<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos/estilo-registro.css">
</head>
<body>

<!-- NAVBAR MEJORADA -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SYSCAMP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="preguntas_form1.php">Visitas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestore2.php">Gestores</a></li>
                <li class="nav-item"><a class="nav-link" href="escuelas.php">Escuelas</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Configuración
                    </a>
                    <ul class="dropdown-menu custom-dropdown">
                        <li><a class="dropdown-item" href="ver_usuarios.php">Perfil de usuarios</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                        <li><a class="dropdown-item" href="cerrar.php">Cerrar sección</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
                    <ul class="dropdown-menu custom-dropdown">
                        <li><a class="dropdown-item" href="ver_usuarios.php">Perfil de usuarios</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                        <li><a class="dropdown-item" href="cerrar.php">Cerrar sección</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
