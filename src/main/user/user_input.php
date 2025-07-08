<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php'; /*'/php/menu.php'*/

    // ログイン中のリダイレクト処理はここで行う
    if (isset($_SESSION['users']) && strpos($_SERVER['REQUEST_URI'], '/user_input.php') === false) { // user_input.php自体へのアクセスは許可
        header('Location: /php/main/user/please_logout.php');/*/php/main/user/please_logout.php'*/
        exit;
    }

    $name = $login = $password = $mail = '';

    // 既存ユーザーの情報を取得する場合
    if (isset($_SESSION['users'])) {
        $name     = $_SESSION['users']['name'];
        $login    = $_SESSION['users']['login'];
        $password = $_SESSION['users']['password'];
        $mail     = $_SESSION['users']['mail'] ?? '';
    }

    $csrf = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title> <link href="https://fonts.googleapis.com/earlyaccess/nikukyu.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/earlyaccess/nicomoji.css" rel="stylesheet">
    
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
        /* プレビュー画像のスタイル */
        #image_preview {
            display: block; /* ← imgをブロック要素として扱う */
            margin-top: 15px;
            width: 200px; /* ← max-widthをwidthに変更 */
            height: 200px; /* ← max-heightをheightに変更 */
            border: 3px dashed #fee7ef; /* ← 枠線を分かりやすく点線に変更 */
            border-radius: 8px;
            object-fit: cover; /* ← 画像がエリアに収まるように調整 */
            background-color: #fafafa; /* ← 背景色を追加 */
        }

        /* ===== スマホ用の設定（ここから） ===== */
        @media (max-width: 600px) {
        /* .decorationクラスを持つ要素（装飾用の画像）を非表示にする */
            .decoration {
                display: none;
            }
        }

    </style>
</head>
<body>

    <div class="decoration paw-prints-top-left"></div>
    <div class="decoration paws-bottom-left"></div>
    <div class="decoration paws-bottom-right"></div>

    <div class="login-container">
        
        <h1>ユーザー登録・編集</h1>
        
        <?php
        echo '<form action="user_output.php" method="post" enctype="multipart/form-data">';
        echo '<input type="hidden" name="csrf_token" value="' . $csrf . '">';

        // お名前
        echo '<div class="form-group">';
        echo '  <label for="name">お名前</label>';
        echo '  <input type="text" name="name" value="' . htmlspecialchars($name) . '">';
        echo '</div>';

        // ログイン名
        echo '<div class="form-group">';
        echo '  <label for="login">ログイン名</label>';
        echo '  <input type="text" name="login" value="' . htmlspecialchars($login) . '">';
        echo '</div>';

        // パスワード
        echo '<div class="form-group">';
        echo '  <label for="password">パスワード</label>';
        echo '  <input type="password" name="password" value="' . htmlspecialchars($password) . '">';
        echo '</div>';
        
        // メールアドレス
        echo '<div class="form-group">';
        echo '  <label for="mail">メールアドレス</label>';
        echo '  <input type="text" name="mail" value="' . htmlspecialchars($mail) . '" placeholder="k12345ab@apps.kct.ac.jp">';
        echo '</div>';
        
        // プロフィール画像
        echo '<div class="form-group">';
        echo '  <label for="profile_pic">プロフィール画像</label>';
        // ファイル選択はデザインを合わせるのが難しいため、標準のままにしますが、グループ化はします。
        echo '  <input type="file" id="profile_pic" name="profile_pic" accept="image/*">';
        echo '  <img id="image_preview" src="" alt="プレビュー画像">';
        echo '</div>';
        
        echo '<button type="submit" class="login-button">確定</button>';
        
        echo '</form>';
        ?>
    </div>

    <?php require $_SERVER['DOCUMENT_ROOT'] . '/php/footer.php';?> 

    <script>
        // idが'profile_pic'の要素（ファイル選択ボタン）を取得
        const fileInput = document.getElementById('profile_pic');
        // idが'image_preview'の要素（画像表示エリア）を取得
        const previewImage = document.getElementById('image_preview');

        // ファイル選択ボタンの値が変わったとき（ファイルが選択されたとき）に処理を実行
        fileInput.addEventListener('change', function(event) {
            // 選択されたファイルを取得
            const file = event.target.files[0];

            // ファイルが選択されていれば、以下の処理を実行
            if (file) {
                // FileReaderオブジェクトを作成
                const reader = new FileReader();

                // ファイルの読み込みが完了したときの処理を定義
                reader.onload = function(e) {
                    // 読み込んだ画像データをプレビュー用のimg要素のsrc属性に設定
                    previewImage.src = e.target.result;
                }

                // 選択されたファイルをData URLとして読み込む
                reader.readAsDataURL(file);
            } else {
                // ファイルが選択されなかった場合は、プレビューをクリア
                previewImage.src = "";
            }
        });
    </script>
</body>
</html>

