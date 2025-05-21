<?php
include_once('header.php');
require_once 'configuracion/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Visita Escolar</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    
    <link rel="stylesheet" href="estilos.css">
    
    <style>
        body {
            padding-top: 80px !important; /* Valor ajustado para evitar superposición */
        }
        
        .navbar-custom {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1030 !important;
        }
        
        .container {
            margin-top: 20px;
        }
        
        h2, h1 {
            padding-top: 15px;
        }
        
        /* Style specific to the form card */
        .card.shadow {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
        }
        .card-header {
             background-color: #007bff !important; /* Assuming primary color from first example */
        }
        .rounded-top-4 {
            border-top-left-radius: .5rem !important;
            border-top-right-radius: .5rem !important;
        }
        .rounded-4 {
             border-radius: .5rem !important;
        }
         .rounded-pill {
            border-radius: 50rem !important;
        }
    </style>
</head>
<body>

<?php
// Note: Including header.php here again might be redundant or incorrect
// depending on the content of header.php and the desired page structure.
// This is included as per the user's example structure.
?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_persona = $_POST['id_persona'];
    $id_institucion = $_POST['id_institucion'];
    $turno_visita = strtolower(trim($_POST['turno_visita']));
    $grado_visita = $_POST['grado_visita'];
    $seccion_visita = $_POST['seccion_visita'];
    $cantidad_estudiantes_visita = $_POST['cantidad_estudiantes_visita'];
    $observacion_visita = $_POST['observacion_visita'];
    $fecha_visita = $_POST['fecha_visita'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];


    $query = "INSERT INTO visita (id_persona, id_institucion, turno_visita, grado_visita, seccion_visita, cantidad_estudiantes_visita, observacion_visita, fecha_visita, latitud, longitud)
              VALUES ('$id_persona', '$id_institucion', '$turno_visita', '$grado_visita', '$seccion_visita', '$cantidad_estudiantes_visita', '$observacion_visita', '$fecha_visita', '$latitud', '$longitud')";

    $resultado = pg_query($conexion, $query);

    if ($resultado) {
        echo "<div class='alert alert-success text-center'>✅ Visita registrada correctamente.</div>";
        //header("Location: registrar_pregunta.php");
    } else {
        // It's better practice to include the PostgreSQL error for debugging
        $error_message = pg_last_error($conexion);
        echo "<div class='alert alert-danger text-center'>❌ Error al registrar la visita: " . htmlspecialchars($error_message) . "</div>";
    }
}

// Fetch data for dropdowns
$personas = pg_fetch_all(pg_query($conexion, "SELECT id_persona, nombre_persona FROM persona"));
$instituciones = pg_fetch_all(pg_query($conexion, "SELECT id_institucion, nombre_institucion FROM institucion"));

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow rounded-4">
                <div class="card-header bg-primary text-white text-center rounded-top-4">
                    <h4 class="mb-0">Registrar Visita Escolar</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Responsable de la visita:</label>
                            <select name="id_persona" class="form-select" required>
                                <option value="">Seleccione una persona</option>
                                <?php if (!empty($personas)): ?>
                                    <?php foreach ($personas as $p): ?>
                                        <option value="<?= htmlspecialchars($p['id_persona']) ?>"><?= htmlspecialchars($p['nombre_persona']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, seleccione un responsable.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Centro escolar:</label>
                            <select name="id_institucion" class="form-select" required>
                                <option value="">Seleccione una institución</option>
                                <?php if (!empty($instituciones)): ?>
                                    <?php foreach ($instituciones as $i): ?>
                                        <option value="<?= htmlspecialchars($i['id_institucion']) ?>"><?= htmlspecialchars($i['nombre_institucion']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                             <div class="invalid-feedback">
                                Por favor, seleccione un centro escolar.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Turno:</label>
                            <select name="turno_visita" class="form-select" required>
                                <option value="matutino">Mañana</option>
                                <option value="vespertino">Tarde</option>
                            </select>
                             <div class="invalid-feedback">
                                Por favor, seleccione un turno.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Grado:</label>
                            <input type="text" name="grado_visita" class="form-control" required>
                             <div class="invalid-feedback">
                                Por favor, ingrese el grado.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sección:</label>
                            <input type="text" name="seccion_visita" class="form-control" required>
                             <div class="invalid-feedback">
                                Por favor, ingrese la sección.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cantidad de estudiantes:</label>
                            <input type="number" name="cantidad_estudiantes_visita" class="form-control" min="1" required>
                             <div class="invalid-feedback">
                                Por favor, ingrese la cantidad de estudiantes (mínimo 1).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones:</label>
                            <textarea name="observacion_visita" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha de visita:</label>
                            <input type="date" name="fecha_visita" class="form-control" required>
                             <div class="invalid-feedback">
                                Por favor, ingrese la fecha de visita.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Latitud:</label>
                            <input type="number" name="latitud" class="form-control" step="any" required>
                             <div class="invalid-feedback">
                                Por favor, ingrese la latitud.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Longitud:</label>
                            <input type="number" name="longitud" class="form-control" step="any" required>
                             <div class="invalid-feedback">
                                Por favor, ingrese la longitud.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success rounded-pill">Registrar Visita</button>
                        </div>
                    </form>

                </div>

            </div>

        </div>

    </div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    // Bootstrap validation script
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
</body>
</html>