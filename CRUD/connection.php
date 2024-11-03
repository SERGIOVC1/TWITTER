<?php

function connection(){
    $host = "localhost:3306";
    $user = "root";
    $pass = "root";
    $bd = "social_network";

    // Configurar el manejo de errores
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Crear conexión
    $connect = mysqli_connect($host, $user, $pass, $bd);

    // Verificar conexión
    if (!$connect) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    return $connect;
}
?>
