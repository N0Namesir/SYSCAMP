<?php
// Esta inclusión de header.php es la primera y podría contener la apertura <html>, <head>, <body>
include_once('header.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

    <link rel="stylesheet" href="estilos.css">

    <style>
        /* Este padding-top es para evitar que el contenido quede oculto bajo un header fijo */
        body {
            padding-top: 80px !important; /* Ajusta este valor según la altura de tu header fijo */
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
            margin-bottom: 20px; /* Added some bottom margin to ensure space above footer */
        }

        h2, h1 {
            padding-top: 15px;
        }

        /* Add some basic table spacing if needed, but Bootstrap classes handle most of it */
        .table-responsive {
            margin-top: 20px; /* Add space above the table */
        }

        /* Asegúrate de que el footer no tenga estilos que lo hagan flotar */
        /* Por ejemplo, evita position: fixed o position: absolute a menos que sea intencional */
         /* Si tu footer.php contiene un <footer class="fixed-bottom">, ese es el que lo fija abajo de la ventana */
    </style>
</head>
<body>

<?php
// La inclusión del header aquí nuevamente era parte de la estructura de ejemplo previa,
// pero es redundante y potencialmente problemática si header.php ya abrió el body.
// La eliminamos para una estructura HTML más estándar.
// include_once('header.php');
?>

<?php
include("configuracion/conexion.php");

// Consulta SQL
$query = "SELECT * FROM public.usuarios ORDER BY id ASC";
$resultado = pg_query($conexion, $query);

// Verificar si hubo error en la consulta
if (!$resultado) {
    // Es mejor practice usar die() solo después de verificar la conexión
    // Para errores de consulta, podrías mostrar un mensaje amigable y loguear el error real
    echo "<div class='alert alert-danger'>Error al cargar usuarios: " . htmlspecialchars(pg_last_error($conexion)) . "</div>";
    $users = []; // Initialize an empty array so the rest of the code doesn't fail
} else {
    // Fetch all rows
    $users = pg_fetch_all($resultado);

    // Free result memory (good practice)
    pg_free_result($resultado);
}

// Close the connection here if it's not managed by footer.php
// if (isset($conexion)) {
//     pg_close($conexion);
// }
?>

    <div class="container mt-5">
        </br>
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
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $fila): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($fila['username'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($fila['email'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombres'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($fila['apellidos'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($fila['password_hash'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($fila['rol_id'] ?? ''); ?></td>
                                </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <tr>
                            <td colspan="7" class="text-center">No se encontraron usuarios.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


</body>
</html>

