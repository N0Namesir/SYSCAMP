<?php

include("configuracion/conexion.php");

// Consulta SQL
$query = "SELECT * FROM public.usuarios ORDER BY id ASC";
$resultado = pg_query($conexion, $query);

// Verificar si hubo error en la consulta
if (!$resultado) {
    die("Error en la consulta: " . pg_last_error());
}
include_once("header.php");
?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Listado de Usuarios</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Password Hash</th>
                        <th>Rol ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['id']); ?></td>
                            <td><?php echo htmlspecialchars($fila['username']); ?></td>
                            <td><?php echo htmlspecialchars($fila['email']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombres']); ?></td>
                            <td><?php echo htmlspecialchars($fila['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($fila['password_hash']); ?></td>
                            <td><?php echo htmlspecialchars($fila['rol_id']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include_once("footer.php") ?>
