<?php
// セッションがまだ開始されていなければ開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php require '../header.php'; ?>

<!-- メニュー -->
<a href="home.php">home</a>
<a href="facilities_show.php">利用状況</a>
<a href="teacher_show.php">教員情報</a>
<a href="link_output.php">リンク集</a>
<a href="post_show.php">投稿</a>
<a href="login_input.php">ログイン</a>
<a href="logout_input.php">ログアウト</a>
<a href="user_input.php">ユーザ登録</a>
<hr>

<?php
// ログインしていれば名前を表示
if (isset($_SESSION['customer'])) {
    echo '<p>', htmlspecialchars($_SESSION['customer']['name']), 'さんがログインしています</p>';
}
?>

<?php require '../footer.php'; ?>
