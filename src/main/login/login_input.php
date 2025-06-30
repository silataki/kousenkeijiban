<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <style>
        /* ページの基本設定 */
        body {
            margin: 0;
            font-family: 'Hiragino Kaku Gothic ProN', 'Meiryo', sans-serif;
            background-color: #fff;
            position: relative; /* 画像を配置する基準点にする */
            width: 100vw;
            height: 100vh;
            overflow: hidden; /* 画面からはみ出す画像を隠す */
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
            width: 150px;
            height: 150px;
            background-image: url('paw_prints_top_left.png');
        }

        .paws-bottom-left {
            bottom: 0;
            left: 20px;
            width: 200px;
            height: 140px;
            background-image: url('paws_bottom_left.png');
        }

        .paws-bottom-right {
            bottom: 0;
            right: 20px;
            width: 150px;
            height: 110px;
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
            font-size: 2.5em;
            font-weight: bold;
            margin: 0 0 30px 0;
            color: #333;
        }
        
        /* フォームの各入力欄のグループ */
        .form-group {
            margin-bottom: 20px;
            width: 320px;
        }

        .form-group label {
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
            width: 320px;
            height: 100px;
            background-image: url('login_button_paw.png');
            background-color: transparent;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border: none;
            cursor: pointer; /* マウスカーソルを指の形にする */
            color: #E55277; /* 文字の色 */
            font-size: 2em;
            font-weight: bold;
            padding-bottom: 10px; /* 文字の位置を微調整 */
        }
    </style>
</head>
<body>

    <div class="decoration paw-prints-top-left"></div>
    <div class="decoration paws-bottom-left"></div>
    <div class="decoration paws-bottom-right"></div>

    <div class="login-container">
        <h1>ログイン</h1>
        
        <form action="/your-login-script.php" method="post">
            <div class="form-group">
                <label for="username">ユーザーネーム</label>
                <input type="text" id="username" name="username">
            </div>

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password">
            </div>

            <button type="submit" class="login-button">login</button>
        </form>
    </div>

</body>
</html>