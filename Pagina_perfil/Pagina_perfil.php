<?php
session_start();
include '../CRUD/connection.php';
$conn = connection();


$user_id = $_SESSION['user_id'];

$sql_followers_count = "SELECT COUNT(*) as total FROM follows WHERE userToFollowId = ?";
$stmt = $conn->prepare($sql_followers_count);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$followers_count = $stmt->get_result()->fetch_assoc()['total'];

$sql_following_count = "SELECT COUNT(*) as total FROM follows WHERE users_id = ?";
$stmt = $conn->prepare($sql_following_count);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$following_count = $stmt->get_result()->fetch_assoc()['total'];

$sql_user_tweets = "SELECT * FROM publications WHERE userId = ? ORDER BY createDate DESC";
$stmt = $conn->prepare($sql_user_tweets);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_tweets = $stmt->get_result();

$sql_following_ids = "SELECT userToFollowId FROM follows WHERE users_id = ?";
$stmt = $conn->prepare($sql_following_ids);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$following_result = $stmt->get_result();
$following_ids = [];

while ($row = $following_result->fetch_assoc()) {
    $following_ids[] = $row['userToFollowId'];
}

$following_ids_list = implode(',', $following_ids);

$tweets_from_following = [];
if (!empty($following_ids_list)) {
    $sql_following_tweets = "SELECT * FROM publications WHERE userId IN ($following_ids_list) ORDER BY createDate DESC";
    $stmt = $conn->prepare($sql_following_tweets);
    $stmt->execute();
    $tweets_from_following = $stmt->get_result();
}

$sql_all_tweets = "SELECT * FROM publications ORDER BY createDate DESC";
$stmt = $conn->prepare($sql_all_tweets);
$stmt->execute();
$all_tweets = $stmt->get_result();

$sql_usernames = "SELECT id, username FROM users";
$result_usernames = $conn->query($sql_usernames);
$usernames = [];
while ($row = $result_usernames->fetch_assoc()) {
    $usernames[$row['id']] = $row['username'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usernames[$user_id]); ?></title>
    <link rel="stylesheet" href="../CSS/perfil.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .nav-links a {
            color: white; 
            text-decoration: none; 
        }

        .profile-header a {
            color: black; 
            text-decoration: none; 
        }

        .profile-header a:hover {
            text-decoration: underline; 
        }

        .tweets-container {
            display: none; 
            margin-top: 10px;
        }

        .toggle-button {
            background-color: black;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .toggle-button:hover {
            background-color: gray;
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

    <div class="profile-container">
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($usernames[$user_id]); ?></h2>
            <p>@<?php echo strtolower($usernames[$user_id]); ?></p>
            <p>
                <a href="../Seguidores/seguidores.php?user_id=<?php echo $user_id; ?>">
                    <?php echo $followers_count; ?> Seguidores
                </a> | 
                <a href="../Seguidos/seguidos.php?user_id=<?php echo $user_id; ?>">
                    <?php echo $following_count; ?> Seguidos
                </a>
            </p>
        </div>

        <form action="../Plubicar_mensaje/mensajes.php" method="POST">
            <textarea name="mensaje" placeholder="¿Qué estás pensando?" required></textarea>
            <button type="submit">Publicar</button>
        </form>

        <button class="toggle-button" onclick="toggleVisibility('userTweets')">Mis Publicaciones</button>
        <div class="tweets-container" id="userTweets">
            <?php while ($tweet = $user_tweets->fetch_assoc()): ?>
                <div class="tweet">
                    <p>
                        <a href="../Perfil_Usuario/UsuarioPerfil.php?user_id=<?php echo $tweet['userId']; ?>">
                            <?php echo htmlspecialchars($usernames[$tweet['userId']]); ?>
                        </a>: <?php echo htmlspecialchars($tweet['text']); ?>
                    </p>
                    <small><?php echo date('d M Y H:i', strtotime($tweet['createDate'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="toggle-button" onclick="toggleVisibility('followingTweets')">Tweets de las personas que sigo</button>
        <div class="tweets-container" id="followingTweets">
            <?php if (empty($following_ids)): ?>
                <p>No sigues a nadie aún.</p>
            <?php else: ?>
                <?php if ($tweets_from_following->num_rows > 0): ?>
                    <?php while ($tweet = $tweets_from_following->fetch_assoc()): ?>
                        <div class="tweet">
                            <p>
                                <a href="../Perfil_Usuario/UsuarioPerfil.php?user_id=<?php echo $tweet['userId']; ?>">
                                    <?php echo htmlspecialchars($usernames[$tweet['userId']]); ?>
                                </a>: <?php echo htmlspecialchars($tweet['text']); ?>
                            </p>
                            <small><?php echo date('d M Y H:i', strtotime($tweet['createDate'])); ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay tweets de las personas que sigues.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <button class="toggle-button" onclick="toggleVisibility('allTweets')">Tweets de Todo el Mundo</button>
        <div class="tweets-container" id="allTweets">
            <?php while ($tweet = $all_tweets->fetch_assoc()): ?>
                <div class="tweet">
                    <p>
                        <a href="../Perfil_Usuario/UsuarioPerfil.php?user_id=<?php echo $tweet['userId']; ?>">
                            <?php echo htmlspecialchars($usernames[$tweet['userId']]); ?>
                        </a>: <?php echo htmlspecialchars($tweet['text']); ?>
                    </p>
                    <small><?php echo date('d M Y H:i', strtotime($tweet['createDate'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function toggleVisibility(id) {
            var container = document.getElementById(id);
            if (container.style.display === "none" || container.style.display === "") {
                container.style.display = "block"; 
            } else {
                container.style.display = "none"; 
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tweets-container').forEach(function(container) {
                container.style.display = "none"; 
            });
        });
    </script>
</body>
</html>
