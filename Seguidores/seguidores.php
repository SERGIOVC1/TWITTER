<?php
session_start();
include '../CRUD/connection.php'; 
$conn = connection();

$user_id = $_SESSION['user_id']; 
$profile_user_id = $_GET['user_id']; 

$sql_followers = "SELECT users.username, users.id FROM follows JOIN users ON follows.users_id = users.id WHERE follows.userToFollowId = ?";
$stmt = $conn->prepare($sql_followers);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$followers_result = $stmt->get_result();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguidores</title>
    <link rel="stylesheet" href="../CSS/perfil.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .profile-link {
            color: black; 
            text-decoration: none; 
        }

        .profile-link:hover {
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Twitter</h1>
        <div class="nav-links">
            <a href="../Pagina_perfil/Pagina_perfil.php">Perfil</a>
            <a href="../index.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </div>

    <div class="followers-container">
        <h2>Seguidores  </h2> 
        <ul>
            <?php while ($follower = $followers_result->fetch_assoc()): ?>
                <li>
                    <a href="../Perfil_Usuario/UsuarioPerfil.php?user_id=<?php echo $follower['id']; ?>" class="profile-link">
                        <?php echo htmlspecialchars($follower['username']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
