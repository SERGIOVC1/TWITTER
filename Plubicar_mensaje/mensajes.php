<?php
session_start();
include '../CRUD/connection.php';
$conn = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensaje'])) {
    $user_id = $_SESSION['user_id'];
    $mensaje = $_POST['mensaje'];

    $sql = "INSERT INTO publications (userId, text, createDate) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $mensaje);

    if ($stmt->execute()) {
        header("Location: ../Pagina_perfil/Pagina_perfil.php");
    } else {
        echo "Error al publicar el mensaje.";
    }

    $stmt->close();
}

$conn->close();
?>
