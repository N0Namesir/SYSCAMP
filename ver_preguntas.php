<?php
include("configuracion/conexion.php");

// Consulta SQL
$query = "SELECT * FROM public.categoria1 ORDER BY cod_pregunta ASC";
$resultado = pg_query($conexion, $query);

// Verificar si hubo error en la consulta
if (!$resultado) {
    die("Error en la consulta: " . pg_last_error());
}
include_once("header.php");
?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Listado de preguntas</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Pregunta</th>
                        <th>Categoria</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['cod_pregunta']); ?></td>
                            <td><?php echo htmlspecialchars($fila['pregunta']); ?></td>
                            <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                            <td>
                                <a href="update_preguntas.php?id=<?php echo $fila['cod_pregunta']; ?>" class="btn btn-success btn-sm">Actualizar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="text-center mt-4">
    <a href="exportar_preguntas.php?formato=html" class="btn btn-primary">Exportar PDF</a>
    <a href="exportar_preguntas.php?formato=csv" class="btn btn-success">Exportar CSV</a>
    <a href="exportar_preguntas.php?formato=txt" class="btn btn-warning">Exportar TXT</a>
</div>
        </div>
    </div>