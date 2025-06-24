<?php session_start(); ?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/header.php'; ?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php'; ?>
<?php
if (isset($_SESSION['customer'])) {
	unset($_SESSION['customer']);
	echo 'ログアウトしました。';
} else {
	echo 'すでにログアウトしています。';
}
?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/footer.php'; ?>
