<?php
session_start();
include 'CRUD/connection.php'; 

$conn = connection(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: Pagina_perfil/Pagina_perfil.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Twitter</title>
    <link rel="stylesheet" href="CSS/index.css">
    <style>
        body {
            display: flex;
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            background-color: #f0f0f0; 
        }

        .replica {
            display: flex;
            justify-content: center; 
            align-items: center; 
            flex-direction: column; 
            text-align: center; 
            background-color: white; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            width: 90%; 
            max-width: 400px;
        }

        .container-menu {
            width: 100%; 
        }

        .button-siguiente {
            background-color: black; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            width: 100%; 
            text-align: center; 
            display: inline-block; 
            margin-top: 20px; 
        }

        .button-siguiente:hover {
            background-color: gray; 
        }

        img.left-image {
            position: absolute; 
            left: 20px; 
            top: 20px; 
            width: 100px; 
        }
    </style>
</head>
<body>
    <img src="./img/Twitter-X-Logo" alt="Imagen" class="left-image"> 

    <div class="replica">
        <div class="container-menu">
            <h3>Inicia sesión en <br> Twitter</h3>

           
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

            <form action="index.php" method="POST">
                <input type="text" name="username" placeholder="Nombre de usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" class="button-siguiente">Iniciar sesión</button>
            </form>
            <button><strong>¿Olvidaste tu contraseña?</strong></button>

            <div class="opcion-final">
                <p>¿No tienes cuenta?</p> <a href="/Pagina_registro/registro.php">Regístrate</a>
            </div>
        </div>
    </div>
</body>
</html>
