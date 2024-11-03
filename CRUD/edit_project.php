<?php

include("connection.php");
$con = connection();

$idAlumnos=$_POST["idAlumnos"];
$nameAlumnos = $_POST['nombreAlumnos'];
$edad = $_POST['edad'];
$enlace = $_POST['link'];

$sql="UPDATE Alumnos SET nombreAlumno='$nombreAlumno', edad='$edad',link='$enlace' WHERE idAlumnos='$idAlumnos'";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: ../index.php");
}else{

}

?>