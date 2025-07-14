<?php
// セッションがまだ開始されていなければ開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<p>ログアウトしますか？</p>

<?php
echo '<img src="/images/logout_cry.png" alt="ログアウト確認画像" style="max-width: 300px; height: auto;">';
?>

<a href="logout_output.php">ログアウト</a>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';
?>