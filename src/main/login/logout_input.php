<?php
// セッションがまだ開始されていなければ開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<p>ログアウトしますか？</p>
<a href="logout_output.php">ログアウト</a>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';
?>