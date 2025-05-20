<?php
include("configuracion/conexion.php");

if (isset($_GET['id_departamento'])) {
    $id_departamento = $_GET['id_departamento'];

    $query = "SELECT id_distrito, nombre_distrito FROM distrito WHERE id_departamento = $1";
    $resultado = pg_query_params($conexion, $query, array($id_departamento));

    $distritos = [];

    while ($fila = pg_fetch_assoc($resultado)) {
        $distritos[] = $fila;
    }

    header('Content-Type: application/json');
    echo json_encode($distritos);
}
?>
