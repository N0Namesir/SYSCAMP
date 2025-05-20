<?php
include("configuracion/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_institucion = $_POST['nombre_institucion'];
    $nombre_persona = $_POST['nombre_persona'];
    $grado = $_POST['grado'];
    $seccion = $_POST['seccion'];
    $turno = $_POST['turno'];
    $cantidad_estudiantes = $_POST['cantidad_estudiantes'];
    $fecha = $_POST['fecha'];

    $query_institucion = "SELECT id_institucion FROM institucion WHERE nombre_institucion = $1";
    $resultado_institucion = pg_query_params($conexion, $query_institucion, array($nombre_institucion));
    $institucion = pg_fetch_assoc($resultado_institucion);

    $query_persona = "SELECT id_persona FROM persona WHERE nombre_persona = $1";
    $resultado_persona = pg_query_params($conexion, $query_persona, array($nombre_persona));
    $persona = pg_fetch_assoc($resultado_persona);

    if ($institucion && $persona) {
        $id_institucion = $institucion['id_institucion'];
        $id_persona = $persona['id_persona'];

        $insert_query = "INSERT INTO respuestas (id_institucion, id_persona, grado, seccion, turno, cantidad_estudiantes, fecha)
                         VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING cod_respuesta";
        $params = array($id_institucion, $id_persona, $grado, $seccion, $turno, $cantidad_estudiantes, $fecha);
        $resultado = pg_query_params($conexion, $insert_query, $params);
        $respuesta = pg_fetch_assoc($resultado);

        if ($respuesta) {
            $cod_respuesta = $respuesta['cod_respuesta'];
            header("Location: visitas.php?cod_respuesta=" . $cod_respuesta);
            exit;
        } else {
            echo "❌ Error al obtener el código de respuesta.";
        }
    } else {
        echo "❌ Institución o persona no encontrada.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario</title>

    <!-- Estilos -->
    <style>
        body {
            background-color: #1e293b;
            color: #f1f5f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        form {
            background-color: #334155;
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 12px;
            margin-bottom: 6px;
            font-weight: bold;
            color: #f8fafc;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background-color: #1e293b;
            color: #f1f5f9;
            margin-bottom: 15px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #2563eb;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }

        button[type="submit"]:hover {
            background-color: #1d4ed8;
        }

        /* Select2 estilos personalizados */
        .select2-container--default .select2-selection--single {
            background-color: #1e293b;
            color: black;
            border: 1px solid #475569;
            border-radius: 6px;
            height: 42px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: black;
            line-height: 42px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }

        /* Estilo del menú desplegable (opciones) */
        .select2-container--default .select2-results > .select2-results__options {
            background-color: #f1f5f9; /* Fondo claro */
            color: black;
        }

        .select2-container--default .select2-results__option {
            color: black;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #cbd5e1;
            color: black;
        }
    </style>

    <!-- jQuery y Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>

<form method="POST" action="preguntas_form1.php">
    <h2>Formulario de Respuestas</h2>

    <label for="distrito">Selecciona el distrito:</label>
    <select name="distrito" id="distrito" required>
        <option value="">-- Selecciona un distrito --</option>
        <?php
        $query = "SELECT id_distrito, nombre_distrito FROM distrito ORDER BY nombre_distrito";
        $resultado = pg_query($conexion, $query);
        while ($fila = pg_fetch_assoc($resultado)) {
            echo "<option value='" . $fila['id_distrito'] . "'>" . htmlspecialchars($fila['nombre_distrito']) . "</option>";
        }
        ?>
    </select>

    <label for="nombre_institucion">Nombre de la institución:</label>
    <select name="nombre_institucion" id="nombre_institucion" required>
        <option value="">-- Selecciona una institución --</option>
    </select>

    <label>Nombre del gestor:</label>
    <input type="text" name="nombre_persona" required><br>

    <label for="grado">Grado:</label>
    <input type="text" name="grado" id="grado" required>

    <label for="seccion">Sección:</label>
    <input type="text" name="seccion" id="seccion" required>

    <label for="turno">Turno:</label>
    <input type="text" name="turno" id="turno" required>

    <label for="cantidad_estudiantes">Cantidad de estudiantes:</label>
    <input type="number" name="cantidad_estudiantes" id="cantidad_estudiantes" required>

    <label for="fecha">Fecha:</label>
    <input type="date" name="fecha" id="fecha" required>

    <button type="submit">Guardar</button>
</form>

<!-- Script dinámico para instituciones -->
</body>
</html>

<!-- SCRIPT PARA SELECT2 Y CARGA DINÁMICA -->
<script>
$(document).ready(function() {
    // Activar Select2 en ambos selects
    $('#distrito').select2({
        placeholder: "-- Selecciona un distrito --",
        allowClear: true
    });

    $('#nombre_institucion').select2({
        placeholder: "-- Selecciona una institución --",
        allowClear: true
    });

    // Cargar instituciones según distrito
    $('#distrito').on('change', function () {
        var idDistrito = $(this).val();

        fetch('obtener_instituciones.php?id_distrito=' + idDistrito)
            .then(response => response.json())
            .then(data => {
                let select = $('#nombre_institucion');
                select.empty().append('<option value="">-- Selecciona una institución --</option>');

                data.forEach(inst => {
                    select.append(new Option(inst.nombre_institucion, inst.nombre_institucion));
                });

                select.trigger('change'); // Actualiza Select2
            });
    });
});
</script>