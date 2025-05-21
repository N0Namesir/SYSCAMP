<?php
include("configuracion/conexion.php");

$formato = $_GET['formato'] ?? 'csv';

$query = "SELECT cod_pregunta, ver_pregunta, categoria FROM categoria1 ORDER BY cod_pregunta ASC";
$resultado = pg_query($conexion, $query);

if (!$resultado) {
    die("Error en la consulta: " . pg_last_error());
}

$preguntas = pg_fetch_all($resultado);

// Procesar exportación
switch ($formato) {
    case 'csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="preguntas.csv"');
        $f = fopen('php://output', 'w');
        fputcsv($f, ['ID', 'Pregunta', 'Categoría']);
        foreach ($preguntas as $fila) {
            fputcsv($f, $fila);
        }
        fclose($f);
        break;

    case 'txt':
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="preguntas.txt"');
        echo "Listado de preguntas\n\n";
        foreach ($preguntas as $fila) {
            echo "ID: {$fila['cod_pregunta']}\n";
            echo "Pregunta: {$fila['pregunta']}\n";
            echo "Categoría: {$fila['categoria']}\n";
            echo "------------------------\n";
        }
        break;

    case 'pdf':
        // Exportación en HTML para "Guardar como PDF" desde el navegador
        echo "<html><head><title>Reporte PDF</title></head><body>";
        echo "<h2 style='text-align:center;'>Listado de preguntas</h2>";
        echo "<table border='1' width='100%' cellpadding='8' cellspacing='0'>";
        echo "<thead><tr><th>ID</th><th>Pregunta</th><th>Categoría</th></tr></thead><tbody>";
        foreach ($preguntas as $fila) {
            echo "<tr>";
            echo "<td>{$fila['cod_pregunta']}</td>";
            echo "<td>{$fila['pregunta']}</td>";
            echo "<td>{$fila['categoria']}</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</body></html>";
        break;

    default:
        echo "Formato no soportado.";
}
?>