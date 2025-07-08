<?php
// セッションがまだ開始されていなければ開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メニュー例</title>
    <style>
        /* ここに既存のCSSスタイルを追加 */
        body {
            font-family: sans-serif;
            margin: 0;
        }

        .global-menu {
            border-radius: 25px;
            background-color: rgb(255, 255, 255);

            padding: 10px 20px;
            /* ここからFlexboxの追加 */
            display: flex; /* 子要素を横並びにする */
            align-items: center; /* 垂直方向の中央揃え */
            flex-wrap: wrap; /* 必要に応じて折り返す */
            color: rgb(39, 39, 39); /* ログイン情報の文字色*/
        }

        .global-menu a {
        color: rgb(0,0,0) !important; /*ここで文字の色を変えられる*/
        text-decoration: none;
        padding: 5px 15px;
        transition: background-color 0.3s ease;
        border-left: 2px solid rgb(206, 204, 204); /* 項目の左側に縦線を引く */
        line-height: 1.2; /* 文字の高さと線を揃える */

        }
        .global-menu a:first-child {
            border-left: none;
        }
        
        .global-menu a:hover {
            background-color:rgb(206, 204, 204);
        }

        .menu-separator {
            /* この要素がログイン情報を右に押しやる役割を担う */
            flex-grow: 1; /* 利用可能なスペースをすべて占める */
            /* または単純に margin-left: auto; でも可。flex-growの方が確実です。 */
            margin-left: auto;
            /* 必要であれば高さやボーダーを設定 */
            /* border-right: 1px solid #777; */
            /* height: 20px; */
        }

        .login-info {
            margin: 0 0 0 15px; /* 左側に少し余白を持たせる */
            white-space: nowrap; /* テキストの折り返しを防ぐ */
        }
        
        .border {
            border: 3px solid rgb(231, 227, 227);
            border-radius: 25px;
        }
    </style>
</head>
<body>

<div class="border">
    <nav class="global-menu">
        <a href="/php/main/home/homepage.php">home</a>
        <a href="/php/main/siyouritu/siyouritu.php">利用状況</a>
        <a href="/php/main/teacher/teacher_list.php">教員情報</a>
        <a href="/php/main/link/link_output.php">リンク集</a>
        <a href="/php/main/post/post.php">投稿</a>
        <a href="/php/main/login/login_input.php">ログイン</a>
        <a href="/php/main/login/logout_input.php">ログアウト</a>
        <a href="/php/main/user/user_input.php">ユーザ登録</a>
        
        <div class="menu-separator"></div> <?php
        // ログインしていれば名前を表示
        if (isset($_SESSION['users'])) {
            echo '<p class="login-info">ログイン中：', htmlspecialchars($_SESSION['users']['name']), '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
        }
        ?>
    </nav>
</div>


</body>
</html>
