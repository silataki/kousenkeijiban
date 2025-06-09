<!--トップページの表示-->
<!--トップページのデザインは独自なので、コード内に記述-->
<!--他のサイトでは共通のヘッダー等を作成-->
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>NitKit's bulletin board</title>
  <style>
    .image-container {
      position: relative;
      display: inline-block;
      text-align: center;
    }
    .image-container img {
      display: block;
    }
    .centered-text {
      position: absolute;
      top: 45%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: black;
      font-size: 36px;
      font-weight: bold;
      text-shadow: none;
      pointer-events: none;
      user-select: none;
      white-space: nowrap;
    }
    .login-link {
      position: absolute;
      top: 60%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 20px;
      font-weight: normal;
      pointer-events: auto; /* リンクはクリック可能に */
      user-select: text;
      background: rgba(255, 255, 255, 0.7);
      padding: 4px 8px;
      border-radius: 5px;
      text-decoration: none;
      color: blue;
      box-shadow: 1px 1px 3px rgba(0,0,0,0.2);
    }
    .login-link:hover {
      text-decoration: underline;
      color: darkblue;
    }
  </style>
</head>
<body>
  <p style="text-align:center;">
    <span class="image-container">
      <img src="/php/main/login_top.png" alt="top_page" />
      <span class="centered-text">NitKit's bulletin board</span>
      <a href="login_input.php" class="login-link">ログインページへ</a>
    </span>
  </p>
</body>
</html>
