<?php
session_start();
//$user_id = 1;

/*tomoka add */
if (!isset($_SESSION['users'])) {
    echo "<p>Please <a href='login.php'>log in</a> to access your profile.</p>";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "セッションIDが存在しません。ログインし直してください。";
    exit;
}

$user_id = $_SESSION['users']['id'];
//$loggedInUserId = $_SESSION['user_id'];

/* finish */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testtest";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

 // assuming you have sessions for logged-in users
//$user_id = $_SESSION['user_id'] ?? 0; // You need to set this properly during login
//if (!isset($_SESSION['user_id'])) {
//    $_SESSION['user_id'] = 1; // hardcode user ID 1 for testing only
//}



if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$reaction = $_POST['reaction'] ?? '';

if ($post_id <= 0 || !in_array($reaction, ['like', 'dislike'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection error']);
    exit;
}

$conn->begin_transaction();

// Check if user already reacted to this post
$stmt = $conn->prepare("SELECT reaction FROM post_reactions WHERE post_id = ? AND user_id = ?");
$stmt->bind_param("is", $post_id, $user_id);
$stmt->execute();
$stmt->bind_result($existing_reaction);
$stmt->fetch();
$stmt->close();

try {
    if (!$existing_reaction) {
        // No existing reaction, insert new
        $stmt = $conn->prepare("INSERT INTO post_reactions (post_id, user_id, reaction) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $post_id, $user_id, $reaction);
        $stmt->execute();
        $stmt->close();

        // Increment the count on posts table
        $column = $reaction === 'like' ? 'likes' : 'dislikes';
        $stmt = $conn->prepare("UPDATE posts SET $column = $column + 1 WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();

        $user_reaction = $reaction;

    } elseif ($existing_reaction === $reaction) {
        // Same reaction clicked again => remove reaction
        $stmt = $conn->prepare("DELETE FROM post_reactions WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("is", $post_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Decrement the count
        $column = $reaction === 'like' ? 'likes' : 'dislikes';
        $stmt = $conn->prepare("UPDATE posts SET $column = $column - 1 WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();

        $user_reaction = null;

    } else {
        // Different reaction clicked => update reaction
        $stmt = $conn->prepare("UPDATE post_reactions SET reaction = ? WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("sis", $reaction, $post_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Decrement old reaction, increment new one
        $old_column = $existing_reaction === 'like' ? 'likes' : 'dislikes';
        $new_column = $reaction === 'like' ? 'likes' : 'dislikes';

        $stmt = $conn->prepare("UPDATE posts SET $old_column = $old_column - 1, $new_column = $new_column + 1 WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();

        $user_reaction = $reaction;
    }

    // Get updated counts
    $result = $conn->query("SELECT likes, dislikes FROM posts WHERE post_id = $post_id");
    $row = $result->fetch_assoc();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'likes' => intval($row['likes']),
        'dislikes' => intval($row['dislikes']),
        'user_reaction' => $user_reaction
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$conn->close();
