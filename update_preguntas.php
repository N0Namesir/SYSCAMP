<?php
include("configuracion/conexion.php");
$mensaje = "Actualizar pregunta";

// Obtener datos de la pregunta a editar primero
$id_pregunta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$datos_pregunta = [];

if ($id_pregunta > 0) {
    $consulta = "SELECT * FROM categoria1 WHERE cod_pregunta = $id_pregunta";
    $resultado = pg_query($conexion, $consulta);
    if ($resultado && pg_num_rows($resultado) > 0) {
        $datos_pregunta = pg_fetch_assoc($resultado);
    }
}

// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que tenemos el ID de la pregunta (del formulario enviado)
    if (isset($_POST['cod_pregunta']) && !empty($_POST['cod_pregunta'])) {
        $id_pregunta = intval($_POST['cod_pregunta']);
        $pregunta = $_POST['pregunta'];
        $categoria_pregunta = $_POST['categoria'];

        // Actualizar en la base de datos - Sintaxis corregida y con verificación de ID
        $query = "UPDATE preguntas SET pregunta = '$pregunta', categoria = '$categoria_pregunta' WHERE cod_pregunta = $id_pregunta";
        
        $resultado = pg_query($conexion, $query);

        if ($resultado) {
            $mensaje = "✅ Pregunta actualizada correctamente.";
        } else {
            $mensaje = "❌ Error al actualizar pregunta: " . pg_last_error($conexion);
        }
    } else {
        $mensaje = "❌ Error: ID de pregunta no proporcionado.";
    }
}

include_once("header.php");
?>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Update pregunta</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if ($id_pregunta > 0 && !empty($datos_pregunta)): ?>
            <form method="post" class="card p-4 shadow-sm">
                <!-- Campo oculto para el ID de la pregunta -->
                <input type="hidden" name="cod_pregunta" value="<?php echo $datos_pregunta['cod_pregunta']; ?>">
                
                  <div class="mb-3">
                <label for="categoria" class="form-label">Categoria de la pregunta:</label>
                <select class="form-select" id="categoria" name="categoria" required>
                <option value="">--Selecciona una categoria--</option>
                <?php
                $query = "SELECT * FROM categoria";
                $resultado = pg_query($conexion, $query);
                while ($fila = pg_fetch_assoc($resultado)){
                echo "<option value='".$fila['nombre_categoria']."'>".$fila['nombre_categoria']."</option>";
                }
                ?>
               </select>
         </div>
                <div class="mb-3">
                    <label for="pregunta" class="form-label">Pregunta:</label>
                    <textarea class="form-control" id="pregunta" name="pregunta" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Actualizar</button> </br>
                <a href="ver_preguntas.php" class="btn btn-success w-100 mt-2">Regresar</a>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                No se encontró la pregunta solicitada o no se proporcionó un ID válido.
                <div class="mt-3">
                    <a href="ver_preguntas.php" class="btn btn-success">Volver al listado</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php include_once("footer.php") ?>