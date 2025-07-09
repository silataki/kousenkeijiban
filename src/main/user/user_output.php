<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=testtest;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token.');
}

$name     = $_POST['name'] ?? '';
$login    = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';
$mail    = $_POST['mail'] ?? '';
$profile_pic = null;

if (!preg_match('/^k[0-9]{5}[a-z]{2}@apps\.kct\.ac\.jp$/', $mail)) {
    die('Invalid mail format.');
}

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $profile_pic = file_get_contents($_FILES['profile_pic']['tmp_name']);
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, login, password, mail, profile_pic) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $login, $hashed_password, $mail, $profile_pic]);

    // Auto login
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $users = $stmt->fetch();

    $_SESSION['users'] = $users;

    header('Location: /php/main/home/homepage.php');
    exit;
} catch (PDOException $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}