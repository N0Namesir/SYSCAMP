<?php
include("configuracion/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rol = $_POST['id_rol'];
    $codigo_persona = $_POST['codigo_persona'];
    $correo_persona = $_POST['correo_persona'];
    $clave_persona = $_POST['clave_persona'];
    $nombre_persona = $_POST['nombre_persona'];
    $apellido_persona = $_POST['apellido_persona'];
    $documento_de_identificacion = $_POST['documento_de_identificacion'];
    $id_distrito_reside = $_POST['id_distrito_reside'];
    $id_departamento_labora = $_POST['id_departamento_labora'];

    $query = "INSERT INTO persona (
        id_rol, codigo_persona, correo_persona, clave_persona, nombre_persona, apellido_persona, 
        documento_de_identificacion, id_distrito_reside, id_departamento_labora
    ) VALUES (
        '$id_rol', '$codigo_persona', '$correo_persona', '$clave_persona', '$nombre_persona', '$apellido_persona', 
        '$documento_de_identificacion', '$id_distrito_reside', '$id_departamento_labora'
    )";

    $resultado = pg_query($conexion, $query);

    if ($resultado) {
        $mensaje = "✅ Datos insertados correctamente.";
    } else {
        $mensaje = "❌ Error al insertar: " . pg_last_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Persona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos/estilo-registro.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- ESTILOS PERSONALIZADOS PARA SELECT2 EN MODO OSCURO -->
    <style>
        .select2-container--default .select2-selection--single {
            background-color: #212529;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            height: calc(2.25rem + 2px);
            color: #fff;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
            line-height: 2.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }

        .select2-container--default .select2-results > .select2-results__options {
            background-color: #212529;
            color: #fff;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #495057;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>

<!-- NAVBAR -->
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
                        <li><a class="dropdown-item" href="cerrar.php">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- FORMULARIO DE REGISTRO -->
<div class="container-form">
    <div class="logo-container">
        <img src="Logo-mined.png" alt="Logo" class="logo">
    </div>

    <h2 class="titulo-principal">Registrar Persona</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert text-center">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-card">
        <div class="mb-3">
            <label for="id_rol" class="form-label">Rol</label>
            <select class="form-select" id="id_rol" name="id_rol" required>
                <option value="">Selecciona un rol</option>
                <option value="1">Administrador</option>
                <option value="2">Editor</option>
                <option value="3">Visor</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="codigo_persona" class="form-label">Código de Persona</label>
            <input type="text" class="form-control" id="codigo_persona" name="codigo_persona" required>
        </div>

        <div class="mb-3">
            <label for="correo_persona" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo_persona" name="correo_persona" required>
        </div>

        <div class="mb-3">
            <label for="clave_persona" class="form-label">Clave</label>
            <input type="password" class="form-control" id="clave_persona" name="clave_persona" required>
        </div>

        <div class="mb-3">
            <label for="nombre_persona" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre_persona" name="nombre_persona" required>
        </div>

        <div class="mb-3">
            <label for="apellido_persona" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido_persona" name="apellido_persona" required>
        </div>

        <div class="mb-3">
            <label for="documento_de_identificacion" class="form-label">Documento de Identificación</label>
            <input type="text" class="form-control" id="documento_de_identificacion" name="documento_de_identificacion" required>
        </div>

        <div class="mb-3">
            <label for="id_departamento_labora" class="form-label">Departamento en que labora</label>
            <select class="form-select" id="id_departamento_labora" name="id_departamento_labora" required>
                <option value="">-- Selecciona un departamento --</option>
                <?php
                $query_departamento = "SELECT * FROM departamento";
                $resultado_departamento = pg_query($conexion, $query_departamento);
                while ($fila = pg_fetch_assoc($resultado_departamento)) {
                    echo "<option value='" . $fila['id_departamento'] . "'>" . $fila['nombre_departamento'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_distrito_reside" class="form-label">Distrito en que reside</label>
            <select class="form-select" id="id_distrito_reside" name="id_distrito_reside" required>
                <option value="">-- Selecciona un distrito --</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Guardar Persona</button>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#id_distrito_reside').select2({
        placeholder: "-- Selecciona un distrito --",
        allowClear: true,
        width: '100%'
    });

    $('#id_departamento_labora').on('change', function () {
        let departamentoId = $(this).val();

        if (departamentoId !== '') {
            fetch('obtener_distritos.php?id_departamento=' + departamentoId)
                .then(response => response.json())
                .then(data => {
                    const distritoSelect = $('#id_distrito_reside');
                    distritoSelect.empty();
                    distritoSelect.append('<option value="">-- Selecciona un distrito --</option>');

                    data.forEach(distrito => {
                        distritoSelect.append(`<option value="${distrito.id_distrito}">${distrito.nombre_distrito}</option>`);
                    });

                    distritoSelect.trigger('change');
                });
        } else {
            $('#id_distrito_reside').empty().append('<option value="">-- Selecciona un distrito --</option>').trigger('change');
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>