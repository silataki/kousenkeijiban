<?php
session_start();
//require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';

if (!isset($_SESSION['users'])) {
    echo "<p>Please <a href='login.php'>log in</a> to access your profile.</p>";
    exit;
}

$users = $_SESSION['users'];


/* tomoka add */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testtest";



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
/* add finish */

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <style>
        .title {
            font-size: 2em;            
            margin: 0;
            font-family: sans-serif;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }

        .profile-tab {
            position: fixed;
            top: 50%;
            right: 0;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 10px 0 0 10px;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .profile-panel {
            position: fixed;
            top: 0;
            right: -700px;
            width: 400px;
            height: 100%;
            background: #f1f1f1;
            box-shadow: -2px 0 5px rgba(0,0,0,0.2);
            padding: 20px;
            transition: right 0.3s ease;
        }

        .profile-panel.open {
            right: 0;
        }

        .close-btn {
            float: right;
            cursor: pointer;
            font-size: 1.5em;
        }

        .profile-field {
            margin: 20px 0;
        }

        .profile-field img {
            max-width: 100px;
            border-radius: 50%;
        }

        .edit-btn {
            margin-left: 10px;
            cursor: pointer;
            color: blue;
        }

        .save-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!--追加分 -->
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
        Welcom to NitKit's Bulletin Board
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
        <a href="/php/main/teacher/teacher_show.php">
            <img src="teacher_aicon.png" alt="教員情報" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">教員情報</div>
        </a>
    </div>

    <div style="flex: 1; text-align: center;">
        <a href="/php/main/siyouritu/siyouritu.php">
            <img src="facilities_aicon.png" alt="設備情報" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">利用状況</div>
        </a>
    </div>

    <div style="flex: 1; text-align: center;">
        <a href="/php/main/link/link_output.php">
            <img src="link_aicon.png" alt="リンク集" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">リンク集</div>
        </a>
    </div>

    <div style="flex: 1; text-align: center;">
        <a href="/php/main/post/post.php">
            <img src="post_aicon.png" alt="投稿" style="width: 50%; border-radius: 10px; cursor: pointer;">
            <div style="margin-top: 8px; font-weight: bold;">投稿一覧</div>
        </a>
    </div>
</div>

<!--追加分終わり-->

<!-- <div class="profile-tab" onclick="toggleProfile(true)">Profile</div>

<div class="profile-panel" id="profilePanel">
    <span class="close-btn" onclick="toggleProfile(false)">&times;</span>
    <h2>Your Profile</h2>

    <form id="profileForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
        <div class="profile-field">
            <label>Name: </label>
            <span id="nameText"><?= htmlspecialchars($users['name']) ?></span>
            <input type="text" name="name" id="nameInput" value="<?= htmlspecialchars($users['name']) ?>" style="display:none;">
            <span class="edit-btn" onclick="toggleEdit('name')">Edit</span>
        </div>

        <div class="profile-field">
            <label>Login: </label>
            <span id="loginText"><?= htmlspecialchars($users['login']) ?></span>
            <input type="text" name="login" id="loginInput" value="<?= htmlspecialchars($users['login']) ?>" style="display:none;">
            <span class="edit-btn" onclick="toggleEdit('login')">Edit</span>
        </div>

        <div class="profile-field">
            <label>mail: </label>
            <span id="mailText"><?= htmlspecialchars($users['mail']) ?></span>
            <input type="text" name="mail" id="mailInput" value="<?= htmlspecialchars($users['mail']) ?>" style="display:none;">
            <span class="edit-btn" onclick="toggleEdit('mail')">Edit</span>
        </div>

        <div class="profile-field">
            <label>Profile Picture:</label><br>
            <?php if (!empty($users['profile_pic'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($users['profile_pic']) ?>" alt="Profile Picture">
            <?php else: ?>
                <p>No image uploaded</p>
            <?php endif; ?>
            <input type="file" name="profile_pic">
        </div>

        <input type="submit" class="save-btn" value="Save Changes">
    </form>
</div>

<script>
    function toggleProfile(show) {
        const panel = document.getElementById('profilePanel');
        panel.classList.toggle('open', show);
    }

    function toggleEdit(field) {
        document.getElementById(field + 'Text').style.display = 'none';
        document.getElementById(field + 'Input').style.display = 'inline';
    }
</script> -->

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';
?>

</body>
</html>
