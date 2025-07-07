<?php
// セッションがまだ開始されていなければ開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="global-menu">
    <a href="/php/main/home/homepage.php">home</a>
    <a href="/php/main/siyouritu/siyouritu.php">利用状況</a>
    <a href="/php/main/teacher/teacher_show.php">教員情報</a>
    <a href="/php/main/link/link_output.php">リンク集</a>
    <a href="/php/main/post/post.php">投稿</a>
    <a href="/php/main/login/login_input.php">ログイン</a>
    <a href="/php/main/login/logout_input.php">ログアウト</a>
    <a href="/php/main/user/user_input.php">ユーザ登録</a>
    
    <div class="menu-separator"></div> <?php
    // ログインしていれば名前を表示
    if (isset($_SESSION['customer'])) {
        echo '<p class="login-info">ログイン中：', htmlspecialchars($_SESSION['customer']['name']), '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
    }
    ?>
</nav>

</body>
</html>