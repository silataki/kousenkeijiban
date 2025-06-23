<?php session_start(); ?>
<?php require '../header.php'; ?>
<?php require 'menu.php'; ?>
<?php
$name=$address=$login=$password='';
if (isset($_SESSION['customer'])) {
	$name=$_SESSION['customer']['name'];
	$login=$_SESSION['customer']['login'];
	$password=$_SESSION['customer']['password'];
}
echo '<form action="user_output.php" method="post">';
echo '<table>';
echo '<tr><td>お名前</td><td>';
echo '<input type="text" name="name" value="', htmlspecialchars($name), '">';
echo '</td></tr>';
echo '<tr><td>ログイン名</td><td>';
echo '<input type="text" name="login" value="', htmlspecialchars($login), '">';
echo '</td></tr>';
echo '<tr><td>パスワード</td><td>';
echo '<input type="password" name="password" value="', htmlspecialchars($password), '">';
echo '</td></tr>';
echo '</table>';
echo '<input type="submit" value="確定">';
echo '</form>';
?>
<?php require '../footer.php'; ?>
