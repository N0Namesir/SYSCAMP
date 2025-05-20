<?php

$host = "localhost";
$port = "5432";
$dbname = "bootcampst11";
$user = "postgres";
$password = "bootcamp2025"; // <-- cambia esto por la contraseña de tu usuario

$conexion = pg_connect("host=$host port=$port 
dbname=$dbname user=$user password=$password");

if (!$conexion) {
    die("Error de conexión a la base de datos.");
}else{
    print"";
}
?>
