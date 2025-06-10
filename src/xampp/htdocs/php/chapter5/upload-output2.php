<?php require '../header.php'; ?>
<?php
if (is_uploaded_file($_FILES['file']['tmp_name'])) {
	if (!file_exists('upload')) {
		mkdir('upload');
	}
	$base=basename($_FILES['file']['name']);
	$file='upload/'.$base;
	if (!preg_match('/^[a-zA-Z0-9_]+\.(png|jpg|jpeg)$/', $base)) {
		echo '対応していないファイル形式です。';
	} else
	if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
		echo $file, 'のアップロードに成功しました。';
		echo '<p><img alt="image" src="', $file, '"></p>';
	} else {
		echo 'アップロードに失敗しました。';
	}
} else {
	echo 'ファイルを選択してください。';
}
?>
<?php require '../footer.php'; ?>
