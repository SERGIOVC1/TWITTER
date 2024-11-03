<?php

include("connection.php");
$con = connection();

$id=$_GET["idAlumnos"];

$sql="DELETE FROM products WHERE idAlumnos='$idAlumnos'";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: ../index.php");
}else{

}

?>