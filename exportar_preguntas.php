<?php
include("configuracion/conexion.php");

// Obtener formato desde el formulario
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'html'; // html simula PDF

// Unir los datos de las tres tablas
$query = "
    SELECT cod_pregunta, pregunta, categoria, 'categoria1' AS origen FROM categoria1
    UNION ALL
    SELECT cod_pregunta, pregunta, categoria, 'categoria2' AS origen FROM categoria2
    UNION ALL
    SELECT cod_pregunta, pregunta, categoria, 'categoria3' AS origen FROM categoria3
    ORDER BY categoria, cod_pregunta
";
$resultado = pg_query($conexion, $query);

if (!$resultado) {
    die("Error al ejecutar la consulta: " . pg_last_error());
}

$datos = pg_fetch_all($resultado);

// Generar el archivo
if ($formato == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reporte_preguntas.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Pregunta', 'Categoría', 'Origen']);
    foreach ($datos as $fila) {
        fputcsv($output, [$fila['cod_pregunta'], $fila['pregunta'], $fila['categoria'], $fila['origen']]);
    }
    fclose($output);
    exit;
} elseif ($formato == 'txt') {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="reporte_preguntas.txt"');
    foreach ($datos as $fila) {
        echo "ID: {$fila['cod_pregunta']}\n";
        echo "Pregunta: {$fila['pregunta']}\n";
        echo "Categoría: {$fila['categoria']}\n";
        echo "Origen: {$fila['origen']}\n";
        echo "-----------------------------\n";
    }
    exit;
} else {
    // HTML simula PDF
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Reporte de Preguntas</title>';
    echo '<style>body { font-family: Arial; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; } th { background-color: #333; color: white; }</style>';
    echo '</head><body>';
    echo '<h2>Reporte de Preguntas por Categoría</h2>';
    echo '<table><thead><tr><th>ID</th><th>Pregunta</th><th>Categoría</th><th>Origen</th></tr></thead><tbody>';
    foreach ($datos as $fila) {
        echo "<tr><td>{$fila['cod_pregunta']}</td><td>{$fila['pregunta']}</td><td>{$fila['categoria']}</td><td>{$fila['origen']}</td></tr>";
    }
    echo '</tbody></table></body></html>';
    exit;
}
?>