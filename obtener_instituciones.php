<?php
include("configuracion/conexion.php");

if (isset($_GET['id_distrito'])) {
    $id_distrito = $_GET['id_distrito'];

    $query = "SELECT nombre_institucion FROM institucion WHERE id_distrito = $1 ORDER BY nombre_institucion";
    $resultado = pg_query_params($conexion, $query, array($id_distrito));

    $instituciones = array();
    while ($fila = pg_fetch_assoc($resultado)) {
        $instituciones[] = $fila;
    }

    header('Content-Type: application/json');
    echo json_encode($instituciones);
}
?>
