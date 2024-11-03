<?php
session_start();
include '../CRUD/connection.php';
$conn = connection(); 

if (isset($_POST['action']) && isset($_POST['userToFollowId'])) {
    $user_id = $_SESSION['user_id'];
    $userToFollowId = $_POST['userToFollowId'];
    
    if ($_POST['action'] == 'follow') {
        
        $sql_follow = "INSERT INTO follows (users_id, userToFollowId) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_follow);
        $stmt->bind_param("ii", $user_id, $userToFollowId);
        $stmt->execute();
        $response = ['success' => true];
    } elseif ($_POST['action'] == 'unfollow') {
      
        $sql_unfollow = "DELETE FROM follows WHERE users_id = ? AND userToFollowId = ?";
        $stmt = $conn->prepare($sql_unfollow);
        $stmt->bind_param("ii", $user_id, $userToFollowId);
        $stmt->execute();
        $response = ['success' => true];
    } else {
        $response = ['success' => false];
    }
    
    echo json_encode($response); 
}

$conn->close();
