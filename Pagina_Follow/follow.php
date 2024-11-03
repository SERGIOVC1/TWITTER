<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirige si no hay sesión activa
    exit();
}

include '../CRUD/connection.php'; // Asegúrate de que esta ruta sea correcta
$conn = connection(); // Llama a la función y asigna la conexión

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['follow'])) {
    $userToFollowId = $_POST['userToFollowId'];

    // Verificar si ya sigues a este usuario
    $sql_check = "SELECT * FROM follows WHERE users_id = ? AND userToFollowId = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("ii", $user_id, $userToFollowId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) { 
        
        $sql_follow = "INSERT INTO follows (users_id, userToFollowId) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_follow);
        $stmt->bind_param("ii", $user_id, $userToFollowId);

        if ($stmt->execute()) {
           
            header("Location: Perfil_Usuario/UsuarioPerfil.php?users_id=" . $userToFollowId);
            exit();
        } else {
            echo "Error al seguir al usuario.";
        }
    } else {
        echo "Ya sigues a este usuario.";
    }
}

$stmt->close();
$conn->close();
?>
