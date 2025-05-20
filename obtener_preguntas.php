<?php
include("configuracion/conexion.php");

if (isset($_POST['categoria'])) {
    $categoria = $_POST['categoria'];

    $query = "SELECT cod_pregunta, pregunta FROM preguntas WHERE categoria = $1 ORDER BY cod_pregunta";
    $resultado = pg_query_params($conexion, $query, array($categoria));

    if (pg_num_rows($resultado) > 0) {
        while ($fila = pg_fetch_assoc($resultado)) {
            echo '<div class="pregunta-box">';
            echo '<label>' . htmlspecialchars($fila['pregunta']) . '</label>';
            echo '<input type="hidden" name="preguntas[]" value="' . $fila['cod_pregunta'] . '">';

            // Selector de respuesta
            echo '<div class="mb-3 mt-2">';
            echo '<label class="form-label">Respuesta:</label>';
            echo '<select name="respuestas[]" class="form-select" required>';
            echo '<option value="">-- Selecciona --</option>';
            echo '<option value="SI">Sí</option>';
            echo '<option value="NO">No</option>';
            echo '</select>';
            echo '</div>';

            // Comentario opcional
            echo '<div class="mb-3">';
            echo '<label class="form-label">Comentario:</label>';
            echo '<input type="text" name="comentarios[]" class="form-control" placeholder="Comentario (opcional)">';
            echo '</div>';

            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-warning mt-3">No hay preguntas disponibles para esta categoría.</div>';
    }
}
?>
