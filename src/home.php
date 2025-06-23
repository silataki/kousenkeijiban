<!-- メニュー -->
<a href="home.php">home</a>
<a href="login_input.php">ログイン</a>
<a href="logout_input.php">ログアウト</a>
<a href="user_input.php">ユーザ登録</a>
<hr>

<!-- メイン画像 -->
<div style="position: relative; margin-top: 0px;">
    <img src="home_kousen.jpg" alt="高専画像" style="width: 100%; height: auto; display: block;">
    <div style="
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        background-color: rgba(0, 0, 0, 0.5);
        padding: 10px 30px;
        border-radius: 20px;
        font-size: 2.5rem;
        font-weight: bold;
        text-align: center;
        white-space: nowrap;
    ">
        Welcom to NitKit's Bullent Board
    </div>
</div>

<!-- 画像リンク４つ（均等配置＋説明付き） -->
<div style="
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-top: 30px;
">
    <div style="flex: 1; text-align: center;">
        <a href="teacher_show.php">
            <img src="teacher_aicon.png" alt="教員情報" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">教員情報</div>
        </a>
    </div>

    <div style="flex: 1; text-align: center;">
        <a href="facilities_show.php">
            <img src="facilities_aicon.png" alt="設備情報" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">利用状況</div>
        </a>
    </div>

    <div style="flex: 1; text-align: center;">
        <a href="link_output.php">
            <img src="link_aicon.png" alt="リンク集" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">リンク集</div>
        </a>
    </div>

    <div style="flex: 1; text-align: center;">
        <a href="post_show.php">
            <img src="post_aicon.png" alt="投稿" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">投稿一覧</div>
        </a>
    </div>
</div>

<?php require '../footer.php'; ?>
