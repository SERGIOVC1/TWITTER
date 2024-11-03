<?php
ini_set('memory_limit', '256M');
include('../CRUD/connection.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 

    $createDate = date('Y-m-d H:i:s');

    $connect = connection();

    $checkUser = mysqli_prepare($connect, "SELECT * FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($checkUser, 'ss', $username, $email);
    mysqli_stmt_execute($checkUser);
    $result = mysqli_stmt_get_result($checkUser);

    if (mysqli_num_rows($result) > 0) {
        $error = "El nombre de usuario o el correo electrónico ya están registrados.";
    } else {
        $stmt = mysqli_prepare($connect, "INSERT INTO users (username, email, password, createDate) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $password, $createDate);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: /../../index.php');  
            exit; 
        } else {
            $error = "Error al registrar el usuario. Inténtalo de nuevo.";
        }
    }

    mysqli_close($connect);
}
?>
