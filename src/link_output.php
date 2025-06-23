<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>リンク集</title>
    <style>
        /* ページ全体の基本スタイル */
        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'メイリオ', Meiryo, sans-serif;
            background-color: #FEFEFE;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        #webclass-logo {
            width: 200px; /* WebClassロゴの横幅 */
            height: auto; /* 高さは自動調整 */
        }

        #nitkit-logo {
            width: 90px;  /* NIT-KITロゴの横幅 */
            height: 90px; /* NIT-KITロゴの高さ */
        }

        /* 全ての画像に共通のスタイルを適用 */
        .links img {
            border: 1px solid #ccc;     /* 枠線を追加 */
            vertical-align: middle;    /* 垂直方向の揃えを調整 */
            transition: opacity 0.3s;  /* ホバー時のアニメーション */
        }
        .links a:hover img {
            opacity: 0.7; /* マウスを乗せると少し透明にする */
        }

        /* リンク集全体を囲むコンテナ */
        .link-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 30px;
            background-color:rgb(255, 243, 252); /* 全体の薄いピンク背景 */
            border: 1px rgb(250, 195, 237);
            border-radius: 8px;
            position: relative;
        }

        /* 大見出し「リンク集」 */
        h1 {
            font-size: 2em;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
            margin-top: 0;
            margin-bottom: 30px;
        }

        /* 各セクションのスタイル */
        .link-section {
            margin-bottom: 30px;
        }

        /* セクションのタイトル（ピンクの背景） */
        .section-title {
            background-color:rgb(254, 217, 245); /* ピンク色の背景 */
            font-size: 1.1em;
            font-weight: bold;
            display: inline-block;
            padding: 8px 16px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* リンク要素のフレックスコンテナ */
        .links {
            display: flex;
            align-items: center;
            gap: 25px; /* 要素間のスペース */
            flex-wrap: wrap; /* 画面が小さい時に折り返す */
        }

        /* 画像リンクのスタイル */
        .links a.image-link img {
            border: 1px solid #ccc;
            vertical-align: middle;
            transition: opacity 0.3s;
        }
        .links a.image-link:hover img {
            opacity: 0.8;
        }

        /* ボタン風リンクのスタイル */
        .links a.button-link {
            border: 1px solid #ccc;
            padding: 12px 45px;
            text-decoration: none;
            color: #333;
            background-color: #f9f9f9;
            border-radius: 5px;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .links a.button-link:hover {
            background-color: #f0f0f0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        }

        /* 電話番号のセクション */
        .tel-info p {
            font-size: 1.2em;
            margin: 0;
        }

        .cat-illustration {
            position: absolute;
            bottom: 10px; /* フッターの下から10pxの位置 */
            right: 25px;  /* フッターの右から25pxの位置 */
            width: 15px;
            opacity: 1.0;
        }
    </style>
</head>
<body>
    <div class="link-container">
        <div class="cat-illustration">
            <img src="cat.png" alt="cat Logo" id="cat-logo">
        </div>
        <h1>リンク集</h1>

        <div class="link-section">
            <h2 class="section-title">関連サイトへのリンク</h2>
            <div class="links">
                <a href="https://webclass.edu.kct.ac.jp/webclass/login.php">
                    <img src="webclass.png" alt="WebClass Logo" id="webclass-logo">
                </a>
                <a href="https://www.kct.ac.jp/">
                    <img src="nitkit.png" alt="nitkit Logo" id="nitkit-logo">
                </a>
            </div>
        </div>

        <div class="link-section">
            <h2 class="section-title">シラバス・行事予定</h2>
            <div class="links">
                <a href="https://www.kct.ac.jp/students/シラバス" class="button-link">シラバス</a>
                <a href="https://www.kct.ac.jp/campuslife/schedule" class="button-link">行事予定</a>
            </div>
        </div>

        <div class="link-section tel-info">
            <h2 class="section-title">TEL</h2>
            <p>学生課総務 093-964-7200（代表）</p>
        </div>
    </div></body>
</html>
