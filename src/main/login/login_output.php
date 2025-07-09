<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<style>
    .center-content {
        width: 80%;
        margin: 0 auto;
        text-align: center;
        padding-top: 30px;
    }

    .welcome-image {
        height: 55vh;
       
     
    }

    .message-box {
        margin-top: 20px;
       
        color: salmon;
        padding: 20px;
        font-size: 4em;
        border-radius: 10px;
    }
</style>


<div class="center-content">
    <img src="welcome.svg" alt="Welcome" class="welcome-image">

    <div class="message-box">
        <?php
        $pdo = new PDO('mysql:host=localhost;dbname=testtest;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $input_login = $_REQUEST['login'] ?? '';
        $input_password = $_REQUEST['password'] ?? '';

        $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
        $stmt->execute([$input_login]);
        $users_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($users_data) {
            if (password_verify($input_password, $users_data['password'])) {
                $_SESSION['users'] = [
                    'id'       => $users_data['id'],
                    'name'     => $users_data['name'],
                    'mail'     => $users_data['mail'],
                    'login'    => $users_data['login'],
                    'profile_pic' => $users_data['profile_pic'],
                ];

                if (!isset($_SESSION['user_id'])) {
                    $session_id = session_id();
                    $_SESSION['user_id'] = hexdec(substr(md5($session_id), 0, 8));
                }

                echo 'ようこそ、', htmlspecialchars($_SESSION['users']['name']), 'さん。';
            } else {
                echo 'ログイン名またはパスワードが違います。';
            }
        } else {
            echo 'ログイン名またはパスワードが違います。';
        }
        ?>
    </div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';
?>