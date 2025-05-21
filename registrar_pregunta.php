<?php
// Esta inclusión de header.php es la primera y podría contener la apertura <html>, <head>, <body>
include_once('header.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </br>
    <title>Formulario de Evaluación</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="estilos.css">

    <style>
        /* Styles from the previous example structure for layout */
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
            margin-bottom: 20px; /* Added some bottom margin for spacing */
        }

        h2, h1 {
            padding-top: 15px;
        }

        /* Styles from the current snippet for form elements */
        .card { margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; padding: 15px; }
        .pregunta { font-weight: bold; margin-bottom: 10px; }
        .opciones-respuesta { margin: 10px 0; }
        .opcion-respuesta { margin-right: 15px; display: inline-block; }
        .comentarios { margin-top: 10px; }
        textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; /* Include padding in element's total width */ }

         /* Alert styles from the current snippet */
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }

        /* Ensure form-control styles from Bootstrap apply */
        .form-control {
            display: block;
            width: 100%;
            padding: .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
         .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
         .btn-lg {
            padding: .5rem 1rem;
            font-size: 1.25rem;
            border-radius: .3rem;
        }
         .text-center {
            text-align: center !important;
        }
         .mb-4 {
             margin-bottom: 1.5rem !important;
         }
          .mt-4 {
             margin-top: 1.5rem !important;
         }
    </style>
</head>
<body class="bg-light">

<?php
// No incluimos header.php aquí nuevamente, siguiendo la estructura corregida
// de la respuesta anterior para evitar redundancia y posibles problemas de layout.
?>

<?php
include("configuracion/conexion.php");

// Definir las categorías y sus tablas correspondientes
$categorias_tablas = [
    '2.1 Ambiente de aula' => 'categoria1', // Make sure 'categoria1' table exists
    '2.2 Organización de aula' => 'categoria2', // Make sure 'categoria2' table exists
    '2.3 Mediación pedagógica' => 'categoria3' // Make sure 'categoria3' table exists
];

$mensaje = ""; // Initialize message variable
$error = ""; // Initialize error variable

// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if any responses were actually submitted
    if (isset($_POST['respuesta']) && is_array($_POST['respuesta']) && !empty($_POST['respuesta'])) {
        // Iniciar transacción
        $begin_result = pg_query($conexion, "BEGIN");
        if (!$begin_result) {
            $error = "❌ Error al iniciar la transacción: " . pg_last_error($conexion);
        } else {
            try {
                $all_queries_successful = true;
                // Recoger todas las respuestas
                foreach ($_POST['respuesta'] as $cod_pregunta => $respuesta) {
                    // Basic sanitization and validation
                    $cod_pregunta = filter_var($cod_pregunta, FILTER_VALIDATE_INT);
                    $respuesta_valor = pg_escape_string($conexion, $respuesta);
                    $comentario = pg_escape_string($conexion, $_POST['comentario'][$cod_pregunta] ?? '');
                    $categoria_nombre = $_POST['categoria_pregunta'][$cod_pregunta] ?? null; // Get category name from hidden field

                    if ($cod_pregunta === false || !array_key_exists($categoria_nombre, $categorias_tablas)) {
                         // Log or handle invalid input, but don't necessarily throw to avoid stopping the loop for other questions
                         error_log("Invalid cod_pregunta ($cod_pregunta) or category ($categoria_nombre) for submission.");
                         $all_queries_successful = false; // Mark transaction for rollback
                         continue; // Skip this response
                    }

                    $tabla_origen = $categorias_tablas[$categoria_nombre];

                    // MODIFICACIÓN OBLIGATORIA 1: Consulta INSERT actualizada con prepared statement placeholders
                    $query = "INSERT INTO respuesta(
                                cod_pregunta,
                                respuesta,
                                comentario,
                                categoria,
                                tabla_origen
                              ) VALUES ($1, $2, $3, $4, $5)";

                    // MODIFICACIÓN OBLIGATORIA 2: Parámetros actualizados para pg_query_params
                    $resultado = pg_query_params($conexion, $query, array(
                        $cod_pregunta, // $1
                        $respuesta_valor, // $2
                        $comentario, // $3
                        $categoria_nombre, // $4 - Storing category name
                        $tabla_origen  // $5 - Storing origin table name
                    ));

                    if (!$resultado) {
                         $all_queries_successful = false; // Mark transaction for rollback
                         error_log("Database error inserting response for question $cod_pregunta: " . pg_last_error($conexion));
                         // Optionally, build a list of errors for the user
                    }
                }

                if ($all_queries_successful) {
                    pg_query($conexion, "COMMIT");
                    $mensaje = "✅ Respuestas guardadas correctamente.";
                } else {
                    pg_query($conexion, "ROLLBACK");
                    $error = "❌ Ocurrió un error al guardar algunas respuestas. Por favor, revise los logs del servidor.";
                }

            } catch (Exception $e) {
                // This catch block might not be reached by pg_query_params errors directly
                // It's better to handle errors from pg_query_params result check within the loop
                pg_query($conexion, "ROLLBACK");
                $error = "❌ Error inesperado durante el proceso: " . htmlspecialchars($e->getMessage());
            }
        }
    } else {
        // Handle case where no responses were submitted (e.g., empty form submit)
         $error = "❌ No se recibieron respuestas válidas.";
    }
}

// Obtener preguntas si se ha seleccionado una categoría
$preguntas = array();
$selected_category = $_GET['categoria'] ?? ''; // Get selected category from GET

if (!empty($selected_category) && array_key_exists($selected_category, $categorias_tablas)) {
    // Sanitize the category name before using it to get the table name
    // The category name itself is used as a key in the hardcoded $categorias_tablas array,
    // so we just need to ensure it exists in the array.
    $tabla_preguntas = $categorias_tablas[$selected_category];

    // Escape the table name identifier
    $tabla_preguntas_escaped = pg_escape_identifier($conexion, $tabla_preguntas);

    // Query questions from the selected table
    $query_preguntas = "SELECT cod_pregunta, pregunta FROM $tabla_preguntas_escaped ORDER BY cod_pregunta";
    $result_preguntas = pg_query($conexion, $query_preguntas);

    if (!$result_preguntas) {
        $error = "❌ Error al cargar preguntas de la categoría seleccionada: " . htmlspecialchars(pg_last_error($conexion));
        $preguntas = []; // Ensure $preguntas is empty on error
    } else {
        $preguntas = pg_fetch_all($result_preguntas);
        pg_free_result($result_preguntas); // Free result
    }
}
?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Formulario de Evaluación</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <h4>Seleccione una categoría</h4>
            <form method="get" class="form-inline">
                <div class="mb-3"> <label for="categoria_select" class="form-label visually-hidden">Seleccione una categoría:</label>
                     <select name="categoria" id="categoria_select" class="form-control" required onchange="this.form.submit()">
                        <option value="">-- Seleccione una categoría --</option>
                        <?php foreach ($categorias_tablas as $nombre_categoria => $tabla): ?>
                            <option value="<?php echo htmlspecialchars($nombre_categoria); ?>"
                                <?php if ($selected_category == $nombre_categoria) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($nombre_categoria); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if (!empty($preguntas)): ?>
            <form method="post">
                <?php foreach ($preguntas as $pregunta): ?>
                    <div class="card">
                        <div class="pregunta">
                            <?php echo htmlspecialchars($pregunta['pregunta'] ?? 'N/A'); ?>
                        </div>
                        
                        <div class="opciones-respuesta">
                            <label class="opcion-respuesta">
                                <input type="radio" name="respuesta[<?php echo htmlspecialchars($pregunta['cod_pregunta'] ?? ''); ?>]" value="Sí" required> Sí
                            </label>
                            <label class="opcion-respuesta">
                                <input type="radio" name="respuesta[<?php echo htmlspecialchars($pregunta['cod_pregunta'] ?? ''); ?>]" value="No"> No
                            </label>
                            <label class="opcion-respuesta">
                                <input type="radio" name="respuesta[<?php echo htmlspecialchars($pregunta['cod_pregunta'] ?? ''); ?>]" value="No aplica"> No aplica
                            </label>
                        </div>
                        
                        <div class="comentarios">
                            <label>Comentarios (opcional):</label>
                            <textarea name="comentario[<?php echo htmlspecialchars($pregunta['cod_pregunta'] ?? ''); ?>]" rows="2"></textarea>
                            <input type="hidden" name="categoria_pregunta[<?php echo htmlspecialchars($pregunta['cod_pregunta'] ?? ''); ?>]"
                                   value="<?php echo htmlspecialchars($selected_category); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Enviar Respuestas</button>
                </div>
            </form>
        <?php elseif (!empty($selected_category)): // Only show message if a category was selected but no questions found ?>
            <div class="alert alert-info">
                No hay preguntas disponibles para esta categoría.
            </div>
        <?php endif; ?>
    </div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


</body>
</html>