<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';

if (isset($_SESSION['users'])) {
    // 既にログイン中ならホームなどにリダイレクト
    header('Location: /php/main/user/please_logout.php');
    exit;
}


$name = $login = $password = $mail = '';

if (isset($_SESSION['users'])) {
    $name     = $_SESSION['users']['name'];
    $login    = $_SESSION['users']['login'];
    $password = $_SESSION['users']['password'];
    $mail    = $_SESSION['users']['mail'] ?? '';
}

$csrf = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf;

echo '<form action="user_output.php" method="post" enctype="multipart/form-data">';
echo '<input type="hidden" name="csrf_token" value="' . $csrf . '">';
echo '<table>';
echo '<tr><td>お名前</td><td><input type="text" name="name" value="' . htmlspecialchars($name) . '"></td></tr>';
echo '<tr><td>ログイン名</td><td><input type="text" name="login" value="' . htmlspecialchars($login) . '"></td></tr>';
echo '<tr><td>パスワード</td><td><input type="password" name="password" value="' . htmlspecialchars($password) . '"></td></tr>';
echo '<tr><td>メールアドレス</td><td><input type="text" name="mail" value="' . htmlspecialchars($mail) . '" placeholder="k12345ab@apps.kct.ac.jp"></td></tr>';
echo '<tr><td>プロフィール画像</td><td><input type="file" name="profile_pic" accept="image/*"></td></tr>';
echo '</table>';
echo '<input type="submit" value="確定">';
echo '</form>';
?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/footer.php';?>
