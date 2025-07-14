<?php
// セッションがまだ開始されていなければ開始する
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
        height: 65vh;
        
      
    }

    .message-box {
        margin-top: 20px;
        
        color: salmon;
        padding: 20px;
        font-size: 3em;
        border-radius: 10px;
    }
</style>

<div class="center-content">
	<img src="bye.svg" alt="Welcome" class="welcome-image">
	<div class="message-box">
		<?php

		if (isset($_SESSION['users'])) {
			unset($_SESSION['users']);
			echo 'ログアウトしました。またね来てね～！';
		} else {
			echo 'すでにログアウトしています。';
		}
		?>
    </div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';
?>