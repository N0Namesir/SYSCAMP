<?php
include_once('header.php');
//require("visitasFirst.php");
include("configuracion/conexion.php");
//require_once("visitasFirst.php");
// Verifica si se ha enviado el formulario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $respuesta = $_POST['respuesta'];
    $comentario = $_POST['comentario'];

    $query = "INSERT INTO respuestas_detalladas (respuesta, comentario) VALUES ($1, $2)";
    $resultado = pg_query_params($conexion, $query, array($respuesta, $comentario));

    if ($resultado) {
        $mensaje = "✅ Datos insertados correctamente.";
        header("Location: registrar_preguntas.php?cod_respuesta=");
        exit;
    } else {
        $mensaje = "❌ Error al insertar: " . pg_last_error($conexion);
    }
}
?>

<!-- Estilo personalizado basado en la imagen -->
<style>
    body {
        background-color: #1f2937;
        font-family: 'Segoe UI', sans-serif;
        color: #fff;
        display: flex;
        justify-content: center;
        padding-top: 80px;
    }

    .form-container {
        background-color: #2d3748;
        padding: 30px;
        border-radius: 20px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 25px;
        font-size: 28px;
    }

    label {
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        font-weight: bold;
        color: #cbd5e0;
    }

    select, input, textarea {
        width: 100%;
        padding: 12px;
        background-color: #1a202c;
        border: none;
        border-radius: 10px;
        color: #fff;
        margin-bottom: 15px;
    }

    select:focus, input:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 2px #805ad5;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #805ad5;
        border: none;
        border-radius: 12px;
        color: #fff;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }

    button:hover {
        background-color: #6b46c1;
    }

    #contenedor_preguntas {
        margin-top: 15px;
    }
</style>

<!-- Estructura HTML del formulario -->
<div class="form-container">
    <h2>Registrar Respuesta</h2>
    <form method="POST" action="guardar_respuesta.php">
        <input type="hidden" name="cod_respuesta" value="<?php echo htmlspecialchars($_GET['cod_respuesta'] ?? ''); ?>">

        <!-- Selector de categoría -->
        <label for="categoria">Selecciona una categoría:</label>
        <select name="categoria" id="categoria">
            <option value="">-- Selecciona --</option>
            <?php
            $query_categorias = "SELECT DISTINCT categoria FROM preguntas ORDER BY categoria";
            $resultado = pg_query($conexion, $query_categorias);
            while ($fila = pg_fetch_assoc($resultado)) {
                echo "<option value='" . htmlspecialchars($fila['categoria']) . "'>" . htmlspecialchars($fila['categoria']) . "</option>";
            }
            ?>
        </select>

        <div id="contenedor_preguntas">
            <!-- Aquí se cargarán dinámicamente las preguntas según la categoría -->
        </div>

        <button type="submit">Guardar respuestas y generar PDF</button>
    </form>
</div>

<!-- Script para cargar preguntas dinámicamente -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#categoria').on('change', function () {
        let categoria = $(this).val();
        if (categoria !== '') {
            $.post('obtener_preguntas.php', { categoria: categoria }, function (data) {
                $('#contenedor_preguntas').html(data);
            });
        } else {
            $('#contenedor_preguntas').html('');
        }
    });
</script>
