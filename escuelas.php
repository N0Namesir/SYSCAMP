<?php
include_once('header.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centros Educativos con Filtros</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="estilos.css">
    
    <style>
        /* Styles from the second example */
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
        
        .container { /* Note: The current snippet uses .data-container instead of .container for the main wrapper */
            margin-top: 20px;
        }
        
        h2, h1 {
            padding-top: 15px;
        }
        
        /* Styles from the current snippet */
        .data-container {
            margin: 20px;
            overflow: auto; /* Important for horizontal scrolling */
        }
        table {
            border-collapse: collapse;
            width: 100%; /* Changed from 100% in snippet to ensure it fits container with overflow */
            margin-bottom: 20px;
            min-width: max-content; /* Allow table to be wider than container if needed */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            min-width: 120px;
            white-space: nowrap; /* Prevent text wrapping in cells */
        }
        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
            z-index: 5; /* Ensure header is above table body but below pagination-nav */
        }
        .pagination {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
            margin: 2px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        .pagination-info {
            text-align: center;
            margin: 10px 0;
            font-style: italic;
        }
        .page-selector {
            margin: 10px 0;
            text-align: center;
        }
        .page-selector select, .page-selector button {
            padding: 8px;
            margin: 0 5px;
        }
        .disabled {
            color: #999;
            cursor: not-allowed;
            pointer-events: none; /* Disable link clicks */
        }
        .pagination-nav {
            background: #f8f9fa;
            padding: 10px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px; /* Add space below the nav bar */
        }
        .form-selector {
            display: inline-block;
            margin: 0 10px;
        }
        .filtros-container {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .filtro-group {
            flex: 1;
            min-width: 250px;
        }
        .filtro-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        /* Adjust Select2 width to fit flex container */
        .filtro-group .select2-container {
             width: 100% !important;
             box-sizing: border-box; /* Include padding and border in element's total width */
        }
        .botones-filtro {
            display: flex;
            gap: 10px;
        }
        .botones-filtro button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        #aplicar-filtros {
            background-color: #4CAF50;
            color: white;
        }
        #reset-filtros {
            background-color: #f44336;
            color: white;
        }
        .centro {
            text-align: center;
        }
    </style>
</head>
<body>

<?php
// Note: Including header.php here again might be redundant or incorrect
// depending on the content of header.php and the desired page structure.
// This is included as per the user's example structure.
include_once('header.php');
?>

<?php
// Configuración de la conexión
include("configuracion/conexion.php");

// Configuración de paginación
$filas_por_pagina = 500;
$columnas_por_pagina = 8;

// Obtener parámetros de paginación
// Ensure page numbers are at least 1
$pagina_filas = isset($_GET['fila_page']) ? max(1, (int)$_GET['fila_page']) : 1;
$pagina_columnas = isset($_GET['col_page']) ? max(1, (int)$_GET['col_page']) : 1;

// Obtener todas las columnas de la tabla
$tabla = "institucion"; // Make sure this table exists and is accessible
$columnas_query = "SELECT column_name
                   FROM information_schema.columns
                   WHERE table_name = $1
                   AND table_schema = 'public' -- Adjust schema if needed
                   ORDER BY ordinal_position";
$result_columnas = pg_query_params($conexion, $columnas_query, array($tabla));

if (!$result_columnas) {
    die("Error al obtener nombres de columnas: " . pg_last_error($conexion));
}

$todas_columnas = pg_fetch_all_columns($result_columnas, 0);
pg_free_result($result_columnas); // Free result immediately

if (empty($todas_columnas)) {
     die("Error: No se encontraron columnas para la tabla '" . htmlspecialchars($tabla) . "' en el esquema 'public'.");
}


// Obtener total de filas
$total_filas_result = pg_query($conexion, "SELECT COUNT(*) FROM " . pg_escape_identifier($tabla));
if (!$total_filas_result) {
    die("Error al obtener el total de filas: " . pg_last_error($conexion));
}
$total_filas = pg_fetch_result($total_filas_result, 0, 0);
pg_free_result($total_filas_result); // Free result

$total_paginas_filas = ceil($total_filas / $filas_por_pagina);
$total_paginas_columnas = ceil(count($todas_columnas) / $columnas_por_pagina);

// Adjust current page if it exceeds total pages
$pagina_filas = min($pagina_filas, max(1, $total_paginas_filas));
$pagina_columnas = min($pagina_columnas, max(1, $total_paginas_columnas));

// Calcular offsets based on adjusted page numbers
$offset_filas = ($pagina_filas - 1) * $filas_por_pagina;
$offset_columnas = ($pagina_columnas - 1) * $columnas_por_pagina;


// Seleccionar columnas para esta página
$columnas_pagina = array_slice($todas_columnas, $offset_columnas, $columnas_por_pagina);

// Check if there are columns selected for this page
if (empty($columnas_pagina)) {
     // This case should be rare if total_paginas_columnas is calculated correctly,
     // but handle defensively.
     $columnas_sql = "*"; // Fallback to all columns if none selected for the slice
     $columnas_pagina = $todas_columnas; // Adjust the displayed columns
} else {
    // Build SQL for selected columns, properly escaped
    $columnas_sql = implode(', ', array_map(function($col) use ($conexion) {
        return pg_escape_identifier($conexion, $col); // Escape identifier
    }, $columnas_pagina));
}


// Construir consulta SQL para los datos paginados
$query = "SELECT $columnas_sql FROM " . pg_escape_identifier($conexion, $tabla) . " LIMIT $filas_por_pagina OFFSET $offset_filas";
$result = pg_query($conexion, $query);

if (!$result) {
    die("Error en la consulta de datos: " . pg_last_error($conexion));
}

// Obtener datos para JavaScript
$all_data = [];
while ($row = pg_fetch_assoc($result)) {
    $all_data[] = $row;
}
pg_free_result($result); // Free result

// Encode data for JavaScript, handle potential encoding issues
$json_data = json_encode($all_data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
if ($json_data === false) {
    echo "";
    $json_data = '[]'; // Provide empty array on error
}


// Obtener valores únicos para los filtros
$columna_distrito = 'id_distrito'; // Ajustar según BD
$columna_infraestructura = 'codigo_de_infraestructura'; // Ajustar según BD

// Consulta para códigos de distrito únicos (only if column exists)
$distritos = [];
if (in_array($columna_distrito, $todas_columnas)) {
    $query_distritos = "SELECT DISTINCT " . pg_escape_identifier($conexion, $columna_distrito) . " FROM " . pg_escape_identifier($conexion, $tabla) . " WHERE " . pg_escape_identifier($conexion, $columna_distrito) . " IS NOT NULL ORDER BY " . pg_escape_identifier($conexion, $columna_distrito);
    $result_distritos = pg_query($conexion, $query_distritos);
    if ($result_distritos) {
        $distritos = pg_fetch_all_columns($result_distritos, 0);
        pg_free_result($result_distritos);
    } else {
         echo "";
    }
} else {
     echo "";
}


// Consulta para códigos de infraestructura únicos (only if column exists)
$infraestructuras = [];
if (in_array($columna_infraestructura, $todas_columnas)) {
    $query_infraestructura = "SELECT DISTINCT " . pg_escape_identifier($conexion, $columna_infraestructura) . " FROM " . pg_escape_identifier($conexion, $tabla) . " WHERE " . pg_escape_identifier($conexion, $columna_infraestructura) . " IS NOT NULL ORDER BY " . pg_escape_identifier($conexion, $columna_infraestructura);
    $result_infraestructura = pg_query($conexion, $query_infraestructura);
     if ($result_infraestructura) {
        $infraestructuras = pg_fetch_all_columns($result_infraestructura, 0);
        pg_free_result($result_infraestructura);
    } else {
         echo "";
    }
} else {
    echo "";
}

?>
    <div class="data-container">
        <div class="pagination-nav">
            <div class="centro">
                <h1>Centros educativos</h1>
            </div>
            
            <div class="filtros-container">
                <div class="filtro-group">
                    <label for="filtro-distrito">Código de Distrito:</label>
                    <select id="filtro-distrito" class="filtro-select">
                        <option value="">Todos los distritos</option>
                        <?php foreach ($distritos as $distrito): ?>
                            <option value="<?php echo htmlspecialchars($distrito); ?>">
                                <?php echo htmlspecialchars($distrito); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="botones-filtro">
                    <button id="aplicar-filtros">Filtrar</button>
                    <button id="reset-filtros">Limpiar</button>
                </div>
            </div>
            
            <div class="page-selector">
                <form method="get" class="form-selector" id="filasForm">
                    <input type="hidden" name="col_page" value="<?php echo $pagina_columnas; ?>">
                    <label for="fila_page">Ir a la página: </label>
                    <select name="fila_page" id="fila_page" onchange="this.form.submit()">
                        <?php for($i = 1; $i <= $total_paginas_filas; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $i == $pagina_filas ? 'selected' : ''; ?>>
                                Página <?php echo $i; ?> (<?php echo ($i-1)*$filas_por_pagina+1; ?>-<?php echo min($i*$filas_por_pagina, $total_filas); ?>)
                            </option>
                        <?php endfor; ?>
                         <?php if ($total_paginas_filas == 0): // Handle case with no rows ?>
                             <option value="1" selected>Página 1 (0-0)</option>
                         <?php endif; ?>
                    </select>
                </form>
                
                <form method="get" class="form-selector" id="columnasForm">
                    <input type="hidden" name="fila_page" value="<?php echo $pagina_filas; ?>">
                    <label for="col_page">Ir a grupo de columnas: </label>
                    <select name="col_page" id="col_page" onchange="this.form.submit()">
                        <?php for($i = 1; $i <= $total_paginas_columnas; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $i == $pagina_columnas ? 'selected' : ''; ?>>
                                Grupo <?php echo $i; ?> (<?php echo ($i-1)*$columnas_por_pagina+1; ?>-<?php echo min($i*$columnas_por_pagina, count($todas_columnas)); ?>)
                            </option>
                        <?php endfor; ?>
                        <?php if (count($todas_columnas) == 0): // Handle case with no columns ?>
                             <option value="1" selected>Grupo 1 (0-0)</option>
                         <?php endif; ?>
                    </select>
                </form>
            </div>
            
            <div class="pagination">
                <?php if ($pagina_filas > 1): ?>
                    <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=1">« Primera</a>
                    <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=<?php echo $pagina_filas-1; ?>">‹ Anterior</a>
                <?php else: ?>
                    <span class="disabled">« Primera</span>
                    <span class="disabled">‹ Anterior</span>
                <?php endif; ?>

                <span>Página: <?php echo $pagina_filas; ?> de <?php echo max(1, $total_paginas_filas); ?></span>

                <?php if ($pagina_filas < $total_paginas_filas): ?>
                    <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=<?php echo $pagina_filas+1; ?>">Siguiente ›</a>
                    <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=<?php echo $total_paginas_filas; ?>">Última »</a>
                <?php else: ?>
                    <span class="disabled">Siguiente ›</span>
                    <span class="disabled">Última »</span>
                <?php endif; ?>
            </div>
        </div>

        <table id="tabla-datos">
            <thead>
                <tr>
                    <?php foreach ($columnas_pagina as $columna): ?>
                        <th><?php echo htmlspecialchars($columna); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody id="tabla-body">
                <?php 
                // Display the fetched data for the current page and columns
                if (!empty($all_data)):
                    foreach ($all_data as $row): ?>
                        <tr>
                            <?php foreach ($columnas_pagina as $columna): ?>
                                <td><?php echo htmlspecialchars($row[$columna] ?? ''); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; 
                else: // No data found for the current page/filters
                    ?>
                    <tr>
                         <td colspan="<?php echo count($columnas_pagina); ?>" style="text-align: center;">No se encontraron datos para la página y columnas seleccionadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="pagination">
            <?php if ($pagina_filas > 1): ?>
                <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=1">« Primera</a>
                <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=<?php echo $pagina_filas-1; ?>">‹ Anterior</a>
            <?php else: ?>
                <span class="disabled">« Primera</span>
                <span class="disabled">‹ Anterior</span>
            <?php endif; ?>

            <span>Página <?php echo $pagina_filas; ?> de <?php echo max(1, $total_paginas_filas); ?></span>

            <?php if ($pagina_filas < $total_paginas_filas): ?>
                <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=<?php echo $pagina_filas+1; ?>">Siguiente ›</a>
                <a href="?col_page=<?php echo $pagina_columnas; ?>&fila_page=<?php echo $total_paginas_filas; ?>">Última »</a>
            <?php else: ?>
                <span class="disabled">Siguiente ›</span>
                <span class="disabled">Última »</span>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Inicializar Select2 para los filtros
        $(document).ready(function() {
            $('.filtro-select').select2({
                placeholder: "Seleccione una opción",
                allowClear: true // Adds a clear button
            });

             // Set initial values for filters based on potentially applied filters in the URL (if you add that logic)
             // For now, they default to empty.
        });

        // Datos y configuración
        // Note: This assumes all data for the CURRENTLY DISPLAYED PAGE is loaded.
        // Filtering only works on the data already fetched via PHP for the active page/column set.
        // For filtering across ALL data, you would need AJAX or load the entire dataset initially (not recommended for large tables).
        const allData = <?php echo $json_data; ?>;
        const columnasPagina = <?php echo json_encode($columnas_pagina); ?>;
        // Ensure column names used for filtering match the ones in your $columnas_pagina array or the full dataset if loading all
        const columnaDistrito = '<?php echo $columna_distrito; ?>'; // Make sure this column name exists in your data
        const columnaInfraestructura = '<?php echo $columna_infraestructura; ?>'; // Make sure this column name exists in your data

        // Function to apply independent filters
        function aplicarFiltros() {
            const distritoSeleccionado = $('#filtro-distrito').val();
            const infraSeleccionada = $('#filtro-infraestructura').val();
            
            const datosFiltrados = allData.filter(item => {
                // Check if the column exists in the item and the value matches the filter
                const cumpleDistrito = !distritoSeleccionado || 
                                       distritoSeleccionado === '' ||
                                       (item.hasOwnProperty(columnaDistrito) && item[columnaDistrito]?.toString() === distritoSeleccionado);
                
                const cumpleInfraestructura = !infraSeleccionada || 
                                             infraSeleccionada === '' ||
                                             (item.hasOwnProperty(columnaInfraestructura) && item[columnaInfraestructura]?.toString() === infraSeleccionada);
                
                return cumpleDistrito && cumpleInfraestructura;
            });

            actualizarTabla(datosFiltrados);
        }

        // Function to update the table body
        function actualizarTabla(datos) {
            const tbody = $('#tabla-body');
            tbody.empty();
            
            if (datos.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="${columnasPagina.length}" style="text-align: center;">
                            No se encontraron resultados con los filtros aplicados en la página actual.
                        </td>
                    </tr>
                `);
                return;
            }
            
            datos.forEach(item => {
                const row = $('<tr>');
                columnasPagina.forEach(col => {
                     // Use item[col] with fallback for robustness
                    row.append(`<td>${item[col] || ''}</td>`);
                });
                tbody.append(row);
            });
        }

        // Event listeners for filter buttons and select changes
        $('#aplicar-filtros').click(aplicarFiltros);

        $('#reset-filtros').click(function() {
            // Reset Select2 filters and trigger change event
            $('#filtro-distrito').val(null).trigger('change');
            $('#filtro-infraestructura').val(null).trigger('change');
            
            // Re-apply filters with empty values (shows all data for the page)
            aplicarFiltros();
        });

        // // Optional: Apply filters automatically when select changes (can be heavy for large datasets on a page)
        // $('.filtro-select').on('change', aplicarFiltros);

        // Initial table load is done by PHP.
        // If you want client-side filtering to be active on load based on URL params,
        // you would need to read URL params in JS and set Select2 values, then call aplicarFiltros().
    </script>

    <?php
    // The original snippet freed results and closed the connection here.
    // If your footer.php does connection handling, you might remove this.
    // pg_close($conexion); // Moved connection closing to a more explicit location if not in footer
    ?>



</body>
</html>