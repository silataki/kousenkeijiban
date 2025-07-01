<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/earlyaccess/nikukyu.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/earlyaccess/nicomoji.css" rel="stylesheet">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ページの基本設定 */
        body {
            margin: 0;
            font-family: 'Hiragino Kaku Gothic ProN', 'Meiryo', sans-serif;
            background-color: #fff;
            position: relative; /* 画像を配置する基準点にする */
            width: 100vw;
            min-height: 100vh;
            /*overflow: hidden; /* 画面からはみ出す画像を隠す */
        }

        /* 背景に配置する飾り画像 */
        .decoration {
            position: absolute; /* 自由な位置に配置する */
            background-repeat: no-repeat;
            background-size: contain;
            z-index: 1; /* フォームより後ろに表示 */
        }

        .paw-prints-top-left {
            top: 20px;
            left: 20px;
            width: 200px;
            height: 200px;
            background-image: url('paw_prints_top_left.png');
        }

        .paws-bottom-left {
            bottom: 0;
            left: 20px;
            width: 200px;
            height: 155px;
            background-image: url('paws_bottom_left.png');
        }

        .paws-bottom-right {
            bottom: 0;
            right: 20px;
            width: 200px;
            height: 160px;
            background-image: url('paws_bottom_right.png');
        }


        /* ログインフォーム全体のコンテナ */
        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            position: relative;
            z-index: 10; /* 飾り画像より手前に表示 */
        }

        /* 「ログイン」の見出し */
        .login-container h1 {
            font-family: "Nikukyu";
            width: 380px;         /* フォームの幅と合わせる */
            text-align: left;     /* 文字を左揃えにする */
            box-sizing: border-box; /* 崩れにくくするための設定 */
            font-size: 2.5em;
            font-weight: bold;
            margin: 30px 0 40px 0;
            color: #333;
        }
        
        /* フォームの各入力欄のグループ */
        .form-group {
            margin-bottom: 20px;
            width: 350px;
        }

        .form-group label {
            font-family: "Nico Moji";
            display: block; /* ラベルを独立した行に表示 */
            text-align: left;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        /* テキスト入力ボックスのスタイル */
        .form-group input {
            width: 100%;
            padding: 18px 15px;
            border: none; /* 枠線を消す */
            background-color: #FEE7EF; /* ピンク色の背景 */
            border-radius: 8px; /* 角を少し丸める */
            font-size: 1em;
            box-sizing: border-box; /* paddingを含めて幅を計算 */
        }

        /* ログインボタン */
        .login-button {
            font-family: "Nico Moji";
            width: 350px;
            height: 100px;
            margin-top: 30px;
            background-image: url('login_button_paw.png');
            background-color: transparent;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border: none;
            cursor: pointer; /* マウスカーソルを指の形にする */
            color: #E55277; /* 文字の色 */
            font-size: 2.0em;
            font-weight: bold;
            padding-bottom: 10px; /* 文字の位置を微調整 */
            padding-left: 0px;
            padding-right: 30px;
            transition: transform 0.1s ease-in-out, box-shadow 0.1s ease-in-out; /* ← アニメーションを設定 */
        }
        .login-button:active {
            transform: translateY(2px); /* 少し下に移動 */
        }
    </style>
</head>
<body>

    <div class="decoration paw-prints-top-left"></div>
    <div class="decoration paws-bottom-left"></div>
    <div class="decoration paws-bottom-right"></div>

    <div class="login-container">
        <h1>ログイン</h1>
        
        <form action="/php/main/login/login_output.php" method="post">
            <div class="form-group">
                <label for="username">ユーザーネーム</label>
                <input type="text" name="login">
            </div>
            
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password">
            </div>

            <button type="submit" class="login-button">login</button>
        </form>
        <form action="/php/main/user/user_input.php" method="post">
            <button type="submit" class="login-button">新規登録</button>
        </form>
    </div>

</body>
</html>