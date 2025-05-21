<?php
include_once('header.php');?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </br>
    <title>SYSCAMP - Sistema de Gestión</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    
    <!-- Tus estilos CSS -->
    <link rel="stylesheet" href="estilos.css">
    
    <!-- Solución para el header superpuesto -->
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
        
        #formulario-usuarios {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<?php
include_once('header.php');?>

<?php
include("configuracion/conexion.php");

// Consulta SQL
$query = "SELECT * FROM public.visita";
$resultado = pg_query($conexion, $query);

// Verificar si hubo error en la consulta
if (!$resultado) {
    die("Error en la consulta: " . pg_last_error());
}

?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Listado de Visitas Hechas</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID de la visita</th>
                        <th>ID del Visitor</th>
                        <th>ID Institucion</th>
                        <th>Turno de visita</th>
                        <th>Grado</th>
                        <th>Seccion</th>
                        <th>Cantidad de estudiantes</th>
                        <th>Observaciones de la visita</th>
                        <th>Fecha de la visita</th>
                        <th>latitud</th>
                        <th>longitud</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['id_visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['id_persona']); ?></td>
                            <td><?php echo htmlspecialchars($fila['id_institucion']); ?></td>
                            <td><?php echo htmlspecialchars($fila['turno_visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['grado_visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['seccion_visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['cantidad_estudiantes_visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['observacion_visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['fecha_visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['latitud']); ?></td>
                            <td><?php echo htmlspecialchars($fila['longitud']); ?></td>
                            
                            <td>
                                 <a href="visitas.php?id=<?php echo $fila['id_visita']; ?>" class="btn btn-success btn-sm">Actualizar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container mt-4 mb-5">
    <h2 class="text-center mb-4">Mapa de Ubicación de Visitas</h2>
    <div id="map" style="height: 500px; width: 100%; border: 1px solid #ccc; border-radius: 5px;"></div>
</div>

<!-- Incluir Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
    // Inicializar el mapa con una vista predeterminada de El Salvador
    const map = L.map('map').setView([13.794185, -88.89653], 8);
    
    // Añadir capa de mapa base de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Crear un array para almacenar las visitas
    const visitas = [
        <?php 
        // Resetear el puntero del resultado para volver a recorrerlo
        pg_result_seek($resultado, 0);
        while ($fila = pg_fetch_assoc($resultado)): 
            // Verificar que tanto latitud como longitud tengan valores válidos
            if (!empty($fila['latitud']) && !empty($fila['longitud'])):
        ?>
        {
            id: <?php echo json_encode($fila['id_visita']); ?>,
            idPersona: <?php echo json_encode($fila['id_persona']); ?>,
            idInstitucion: <?php echo json_encode($fila['id_institucion']); ?>,
            turno: <?php echo json_encode($fila['turno_visita']); ?>,
            grado: <?php echo json_encode($fila['grado_visita']); ?>,
            seccion: <?php echo json_encode($fila['seccion_visita']); ?>,
            estudiantes: <?php echo json_encode($fila['cantidad_estudiantes_visita']); ?>,
            observaciones: <?php echo json_encode($fila['observacion_visita']); ?>,
            fecha: <?php echo json_encode($fila['fecha_visita']); ?>,
            latitud: <?php echo (float)$fila['latitud']; ?>,
            longitud: <?php echo (float)$fila['longitud']; ?>
        },
        <?php 
            endif;
        endwhile; 
        ?>
    ];
    
    // Si hay visitas, centrar el mapa en la primera
    if (visitas.length > 0) {
        map.setView([visitas[0].latitud, visitas[0].longitud], 10);
    }
    
    // Recorrer las visitas y añadir marcadores al mapa
    visitas.forEach(visita => {
        // Crear marcador en la posición de la visita
        const marker = L.marker([visita.latitud, visita.longitud]).addTo(map);
        
        // Crear contenido del popup con información de la visita
        const popupContent = `
            <div style="min-width: 200px;">
                <h6 class="mb-2">Visita #${visita.id}</h6>
                <p class="mb-1"><strong>Institución:</strong> ${visita.idInstitucion}</p>
                <p class="mb-1"><strong>Visitante:</strong> ${visita.idPersona}</p>
                <p class="mb-1"><strong>Fecha:</strong> ${visita.fecha}</p>
                <p class="mb-1"><strong>Turno:</strong> ${visita.turno}</p>
                <p class="mb-1"><strong>Grado:</strong> ${visita.grado} ${visita.seccion}</p>
                <p class="mb-1"><strong>Estudiantes:</strong> ${visita.estudiantes}</p>
                <p class="mb-0"><strong>Observaciones:</strong> ${visita.observaciones}</p>
            </div>
        `;
        
        // Vincular popup al marcador
        marker.bindPopup(popupContent);
    });
    
    // Si no hay visitas con coordenadas válidas
    if (visitas.length === 0) {
        document.getElementById('map').innerHTML = '<div class="alert alert-info text-center p-4">No hay visitas con coordenadas disponibles para mostrar en el mapa.</div>';
    }
</script>

<!-- Bootstrap JS y Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
