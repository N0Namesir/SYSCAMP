<?php
include_once('header.php');
include("configuracion/conexion.php");

// Definir categorías y tablas
$categorias_tablas = [
    '2.1 Ambiente de aula' => 'categoria1',
    '2.2 Organización de aula' => 'categoria2',
    '2.3 Mediación pedagógica' => 'categoria3'
];

$mensaje = $error = "";
$preguntas = [];
$selected_category = $_GET['categoria'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['respuesta'])) {
    pg_query($conexion, "BEGIN");
    $ok = true;
    foreach ($_POST['respuesta'] as $cod_pregunta => $respuesta) {
        $cod_pregunta = filter_var($cod_pregunta, FILTER_VALIDATE_INT);
        $respuesta_valor = pg_escape_string($conexion, $respuesta);
        $comentario = pg_escape_string($conexion, $_POST['comentario'][$cod_pregunta] ?? '');
        $categoria_nombre = $_POST['categoria_pregunta'][$cod_pregunta] ?? '';
        if ($cod_pregunta === false || !isset($categorias_tablas[$categoria_nombre])) {
            $ok = false;
            continue;
        }
        $tabla_origen = $categorias_tablas[$categoria_nombre];
        $query = "INSERT INTO respuesta(cod_pregunta, respuesta, comentario, categoria, tabla_origen)
                  VALUES ($1, $2, $3, $4, $5)";
        $res = pg_query_params($conexion, $query, [
            $cod_pregunta, $respuesta_valor, $comentario, $categoria_nombre, $tabla_origen
        ]);
        if (!$res) $ok = false;
    }
    if ($ok) {
        pg_query($conexion, "COMMIT");
        $mensaje = "✅ Respuestas guardadas correctamente.";
    } else {
        pg_query($conexion, "ROLLBACK");
        $error = "❌ Error al guardar respuestas.";
    }
}

if ($selected_category && isset($categorias_tablas[$selected_category])) {
    $tabla = pg_escape_identifier($conexion, $categorias_tablas[$selected_category]);
    $res = pg_query($conexion, "SELECT cod_pregunta, pregunta FROM $tabla ORDER BY cod_pregunta");
    if ($res) {
        $preguntas = pg_fetch_all($res) ?: [];
        pg_free_result($res);
    } else {
        $error = "❌ Error al cargar preguntas: " . htmlspecialchars(pg_last_error($conexion));
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Evaluación</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
        .main-card { max-width: 700px; margin: 40px auto; }
        .pregunta-card { margin-bottom: 20px; }
        .pregunta { font-weight: 500; }
        .opciones-respuesta label { margin-right: 20px; }
        .comentarios textarea { resize: vertical; }
    </style>
</head>
<body>
<div class="container main-card">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center">Formulario de Evaluación</h2>
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="get" class="mb-4">
            <div class="mb-3">
                <label for="categoria_select" class="form-label">Seleccione una categoría:</label>
                <select name="categoria" id="categoria_select" class="form-select" required onchange="this.form.submit()">
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

        <?php if ($preguntas): ?>
            <form method="post">
                <?php foreach ($preguntas as $pregunta): ?>
                    <div class="card pregunta-card p-3">
                        <div class="pregunta mb-2">
                            <?php echo htmlspecialchars($pregunta['pregunta'] ?? 'N/A'); ?>
                        </div>
                        <div class="opciones-respuesta mb-2">
                            <?php foreach (['Sí', 'No', 'No aplica'] as $op): ?>
                                <label>
                                    <input type="radio" name="respuesta[<?php echo $pregunta['cod_pregunta']; ?>]" value="<?php echo $op; ?>" required> <?php echo $op; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="comentarios">
                            <textarea class="form-control" name="comentario[<?php echo $pregunta['cod_pregunta']; ?>]" rows="2" placeholder="Comentarios (opcional)"></textarea>
                            <input type="hidden" name="categoria_pregunta[<?php echo $pregunta['cod_pregunta']; ?>]" value="<?php echo htmlspecialchars($selected_category); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Enviar Respuestas</button>
                </div>
            </form>
        <?php elseif ($selected_category): ?>
            <div class="alert alert-info text-center">No hay preguntas disponibles para esta categoría.</div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>