<?php

include("connection.php");
$con = connection();

$idAlumnos=0;
$nameAlumnos = $_POST['nameAlumnos'];
$edad = $_POST['edad'];



$sql = "INSERT INTO alumnos VALUES('$idAlumnos,$nameAlumnos,$edad')";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: ../index.php");
}else{
    
}

?>