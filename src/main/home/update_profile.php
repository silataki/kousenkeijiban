<?php
session_start();

$pdo = new PDO('mysql:host=localhost;dbname=shop2;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['users'])) {
    echo "Unauthorized access.";
    exit;
}

$id    = $_SESSION['users']['id'];
$name  = $_POST['name'] ?? '';
$login = $_POST['login'] ?? '';
$mail = $_POST['mail'] ?? '';
$profile_pic = null;

// mail format check
if (!preg_match('/^k[0-9]{5}[a-z]{2}@apps\.kct\.ac\.jp$/', $mail)) {
    echo "Invalid mail format.";
    exit;
}

// File upload check
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $profile_pic = file_get_contents($_FILES['profile_pic']['tmp_name']);
}

try {
    if ($profile_pic !== null) {
        // Update including profile picture
        $stmt = $pdo->prepare("UPDATE users SET name = ?, login = ?, mail = ?, profile_pic = ? WHERE id = ?");
        $stmt->execute([$name, $login, $mail, $profile_pic, $id]);
    } else {
        // Update without changing profile picture
        $stmt = $pdo->prepare("UPDATE users SET name = ?, login = ?, mail = ? WHERE id = ?");
        $stmt->execute([$name, $login, $mail, $id]);
    }

    // Refresh session data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $users = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['users'] = $users;

    header('Location: homepage.php');
    exit;
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
