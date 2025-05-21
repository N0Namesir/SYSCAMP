<?php
// configuracion/conexion.php

$host = "localhost";
$port = "5432";
$dbname = "SYSCAMP";//bootcampst15
$user = "postgres";
$password = "bootpass"; // <-- cambia esto por la contraseña de tu usuario

// Conexión nativa a PostgreSQL
$conexion = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

?>
