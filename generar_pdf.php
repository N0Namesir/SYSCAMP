<?php
require_once('TCPDF-main/tcpdf.php');
include("configuracion/conexion.php");

// Obtener el cod_respuesta desde URL
if (!isset($_GET['cod'])) {
    die("❌ Código de respuesta no especificado.");
}
$cod_respuesta = intval($_GET['cod']);

// Obtener datos generales
$query = "SELECT r.*, i.nombre_institucion, p.nombre_persona, p.apellido_persona 
          FROM respuestas r
          JOIN institucion i ON r.id_institucion = i.id_institucion
          JOIN persona p ON r.id_persona = p.id_persona
          WHERE r.cod_respuesta = $1";
$resultado = pg_query_params($conexion, $query, array($cod_respuesta));
$data = pg_fetch_assoc($resultado);

if (!$data) {
    die("❌ No se encontró el registro.");
}

// Obtener respuestas detalladas
$query_detalle = "SELECT pr.pregunta, rd.respuesta, rd.comentario
                  FROM respuestas_detalladas rd
                  JOIN preguntas pr ON rd.cod_pregunta = pr.cod_pregunta
                  WHERE rd.cod_respuesta = $1";
$resultado_detalle = pg_query_params($conexion, $query_detalle, array($cod_respuesta));

// Crear PDF
$pdf = new TCPDF();
$pdf->AddPage();

// Logo
$pdf->Image('logo.png', 15, 10, 30); // asegúrate que logo.png exista
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 15, 'Reporte de Evaluación Institucional', 0, 1, 'C');
$pdf->Ln(10);

// Datos Generales
$pdf->SetFont('helvetica', '', 12);
$html = '
<b>Institución:</b> ' . $data['nombre_institucion'] . '<br>
<b>Gestor:</b> ' . $data['nombre_persona'] . ' ' . $data['apellido_persona'] . '<br>
<b>Grado:</b> ' . $data['grado'] . '<br>
<b>Sección:</b> ' . $data['seccion'] . '<br>
<b>Turno:</b> ' . $data['turno'] . '<br>
<b>Cantidad de Estudiantes:</b> ' . $data['cantidad_estudiantes'] . '<br>
<b>Fecha:</b> ' . $data['fecha'] . '<br><br>
<b>Respuestas:</b><br><br>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Preguntas y respuestas
while ($fila = pg_fetch_assoc($resultado_detalle)) {
    $pdf->MultiCell(0, 10, "● Pregunta: " . $fila['pregunta'], 0, 'L');
    $pdf->MultiCell(0, 10, "   Respuesta: " . $fila['respuesta'], 0, 'L');
    if (!empty($fila['comentario'])) {
        $pdf->MultiCell(0, 10, "   Comentario: " . $fila['comentario'], 0, 'L');
    }
    $pdf->Ln(5);
}

// Pie de página
$pdf->SetY(-20);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 10, 'Reporte generado automáticamente - ' . date('d/m/Y H:i'), 0, 0, 'C');

// Descargar PDF
$pdf->Output('reporte_institucion.pdf', 'D');
?>
