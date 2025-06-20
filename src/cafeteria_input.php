<?php 
$facility = basename(__FILE__, '.php');
$facility_name = explode('_', $facility)[0];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= $facility_name ?>投票</title>
</head>
<body>
    <h2><?= $facility_name === 'lirary' ? '図書館' : '食堂' ?>の混雑状況を投稿</h2>
    <form action="<?= $facility_name ?>_input.php" method="post">
        <p>1~5の値を選んでください：</p>
        <select name="rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
            </select>
        <button type="submit">送信</button>
    </form>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db_config.php';

    $rating = intval($_POST['rating']);
    $facility = $facility_name;
    $date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO votes (facility, rating, vote_date) VALUES (?, ?, ?)");
    $stmt->execute([$facility, $rating, $date]);

    header("Location: siyouritu.php");
    exit();
}
?>