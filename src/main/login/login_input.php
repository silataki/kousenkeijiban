<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/header.php'; ?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php'; ?>
<form action="login_output.php" method="post">
ログイン名<input type="text" name="login"><br>
パスワード<input type="password" name="password"><br>
<input type="submit" value="ログイン">
</form>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/footer.php'; ?>