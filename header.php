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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Gestión Escolar El Salvador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
          rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos para la barra de inicio */
        .navbar-custom {
            background: linear-gradient(90deg, #007bff, #29abe2); /* Azul intenso con transición */
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
        }

        .navbar-nav .nav-link {
            color: #fff;
            padding: 0.75rem 1.25rem;
            transition: color 0.2s ease-in-out;
        }

        .navbar-nav .nav-link:hover {
            color: #fdd835; /* Amarillo dorado para un toque cálido */
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            color: #333;
            transition: background-color 0.15s ease-in-out;
        }

        .dropdown-item:hover {
            background-color: #e0f7fa; /* Azul muy claro */
        }

        .navbar-toggler {
            border-color: #fff;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='rgba%28255,255,255,1%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="logog-removebg-preview.png" alt="Logo Gobierno El Salvador" width="100" height="50" class="d-inline-block align-top me-3">
            SYSCAMP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php"><i class="bi bi-house-fill me-1"></i> Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                        <i class="bi bi-gear-fill me-1"></i> Visitas
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="visitasFirst.php"><i class="bi bi-person-circle me-2"></i>Registrar Visitas</a></li>
                        <li><a class="dropdown-item" href="visitas_hechas.php"><i class="bi bi-pencil-square me-2"></i>Ver Visitas</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill me-1"></i> Preguntas
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="registrar_pregunta.php"><i class="bi bi-person-circle me-2"></i>Responder</a></li>
                        <li><a class="dropdown-item" href="ver_preguntas.php"><i class="bi bi-pencil-square me-2"></i>ver preguntas</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestore.php"><i class="bi bi-person-fill me-1"></i> Gestores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="escuelas.php"><i class="bi bi-building-fill me-1"></i> Escuelas</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill me-1"></i> Configuración
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="ver_usuarios.php"><i class="bi bi-person-circle me-2"></i> Perfil de usuario</a></li>
                        <li><a class="dropdown-item" href="cerrar.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
</html>