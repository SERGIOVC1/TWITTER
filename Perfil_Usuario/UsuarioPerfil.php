<?php
session_start();
include '../CRUD/connection.php'; 
$conn = connection(); 

if (!isset($_GET['user_id'])) {
    header("Location: Pagina_perfil.php"); 
    exit();
}

$user_id = $_SESSION['user_id']; 
$profile_user_id = $_GET['user_id'];

$profile_user = [];
$followers_count = 0;
$following_count = 0;
$is_following = false;
$description = '';
$user_tweets = [];
$tweets_from_following = [];
$all_tweets = [];
$usernames = [];

$sql_profile_user = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_profile_user);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile_user = $result->fetch_assoc();
} else {
    echo "Usuario no encontrado.";
    exit();
}

$sql_followers_count = "SELECT COUNT(*) as total FROM follows WHERE userToFollowId = ?";
$stmt = $conn->prepare($sql_followers_count);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$followers_count = $stmt->get_result()->fetch_assoc()['total'];

$sql_following_count = "SELECT COUNT(*) as total FROM follows WHERE users_id = ?";
$stmt = $conn->prepare($sql_following_count);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$following_count = $stmt->get_result()->fetch_assoc()['total'];

$sql_following_check = "SELECT COUNT(*) as total FROM follows WHERE users_id = ? AND userToFollowId = ?";
$stmt = $conn->prepare($sql_following_check);
$stmt->bind_param("ii", $user_id, $profile_user_id);
$stmt->execute();
$is_following = $stmt->get_result()->fetch_assoc()['total'] > 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['follow'])) {
        $sql_follow = "INSERT INTO follows (users_id, userToFollowId) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_follow);
        $stmt->bind_param("ii", $user_id, $profile_user_id);
        $stmt->execute();
        $is_following = true; 
    } elseif (isset($_POST['unfollow'])) {
        $sql_unfollow = "DELETE FROM follows WHERE users_id = ? AND userToFollowId = ?";
        $stmt = $conn->prepare($sql_unfollow);
        $stmt->bind_param("ii", $user_id, $profile_user_id);
        $stmt->execute();
        $is_following = false; 
    }
}

$sql_user_tweets = "SELECT * FROM publications WHERE userId = ? ORDER BY createDate DESC";
$stmt = $conn->prepare($sql_user_tweets);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$user_tweets = $stmt->get_result();

$sql_following_ids = "SELECT userToFollowId FROM follows WHERE users_id = ?";
$stmt = $conn->prepare($sql_following_ids);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$following_result = $stmt->get_result();
$following_ids = [];

while ($row = $following_result->fetch_assoc()) {
    $following_ids[] = $row['userToFollowId'];
}

$following_ids_list = implode(',', $following_ids);

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
    <title>Perfil de <?php echo htmlspecialchars($profile_user['username']); ?></title>
    <link rel="stylesheet" href="../CSS/perfil.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
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

    <div class="profile-container">
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($profile_user['username']); ?></h2>
            <p>@<?php echo strtolower($profile_user['username']); ?></p>
            <p>
                <a href="../Seguidores/seguidores.php?user_id=<?php echo $profile_user_id; ?>" class="profile-link">
                    <?php echo $followers_count; ?> Seguidores
                </a> | 
                <a href="../Seguidos/seguidos.php?user_id=<?php echo $profile_user_id; ?>" class="profile-link">
                    <?php echo $following_count; ?> Seguidos
                </a>
            </p>
            <?php if ($profile_user_id == $user_id):  ?>
                <form action="UsuarioPerfil.php?user_id=<?php echo $profile_user_id; ?>" method="POST">
                    <textarea name="description" placeholder="Editar descripción" required><?php echo htmlspecialchars($description); ?></textarea>
                    <button type="submit" name="edit_description">Guardar Descripción</button>
                </form>
            <?php endif; ?>
        </div>

        <form action="UsuarioPerfil.php?user_id=<?php echo $profile_user_id; ?>" method="POST">
            <?php if (!$is_following): ?>
                <button type="submit" name="follow">Seguir</button>
            <?php else: ?>
                <button type="submit" name="unfollow">Dejar de Seguir</button>
            <?php endif; ?>
        </form>

        <button class="toggle-button" onclick="toggleVisibility('userTweets')">Publicaciones de <?php echo htmlspecialchars($profile_user['username']); ?></button>
        <div class="tweets-container" id="userTweets">
            <?php while ($tweet = $user_tweets->fetch_assoc()): ?>
                <div class="tweet">
                    <p>
                        <a href="UsuarioPerfil.php?user_id=<?php echo $tweet['userId']; ?>">
                            <?php echo htmlspecialchars($usernames[$tweet['userId']]); ?>
                        </a>: <?php echo htmlspecialchars($tweet['text']); ?>
                    </p>
                    <small><?php echo date('d M Y H:i', strtotime($tweet['createDate'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="toggle-button" onclick="toggleVisibility('followingTweets')">Tweets de las personas a las que sigue <?php echo htmlspecialchars($profile_user['username']); ?></button>
        <div class="tweets-container" id="followingTweets">
            <?php while ($tweet = $tweets_from_following->fetch_assoc()): ?>
                <div class="tweet">
                    <p>
                        <a href="UsuarioPerfil.php?user_id=<?php echo $tweet['userId']; ?>">
                            <?php echo htmlspecialchars($usernames[$tweet['userId']]); ?>
                        </a>: <?php echo htmlspecialchars($tweet['text']); ?>
                    </p>
                    <small><?php echo date('d M Y H:i', strtotime($tweet['createDate'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <button class="toggle-button" onclick="toggleVisibility('allTweets')">Tweets de Todo el Mundo</button>
        <div class="tweets-container" id="allTweets">
            <?php while ($tweet = $all_tweets->fetch_assoc()): ?>
                <div class="tweet">
                    <p>
                        <a href="UsuarioPerfil.php?user_id=<?php echo $tweet['userId']; ?>">
                            <?php echo htmlspecialchars($usernames[$tweet['userId']]); ?>
                        </a>: <?php echo htmlspecialchars($tweet['text']); ?>
                    </p>
                    <small><?php echo date('d M Y H:i', strtotime($tweet['createDate'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function toggleVisibility(elementId) {
            var element = document.getElementById(elementId);
            element.style.display = (element.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>
