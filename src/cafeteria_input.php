<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$facility = basename(__FILE__, '.php');
$facility_name = explode('_', $facility)[0];

require_once 'db_config.php';

session_start();
if (!isset($_SESSION['users']['id'])) {
    echo "<p>Please <a href='login.php'>log in</a> to access your profile.</p>";
    exit;
}
$user_id = $_SESSION['users']['id'];
$date = date('Y-m-d');
/*すでに投票済みかチェック*/
//$stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE user_id = ? AND facility = ? AND vote_date = ?");
$stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE user_id = ? AND facility = ? AND vote_date = ?");
$stmt->execute([$user_id, $facility_name, $date]);
$alreadyVoted = $stmt->fetchColumn();

if ($alreadyVoted > 0) {
    echo "<p>投票ありがとうございました！ <a href='siyouritu.php'>戻る</a></p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $facility = $facility_name;
    $date = date('Y-m-d');

     /*投票を記録*/
    $stmt = $pdo->prepare("INSERT INTO votes (user_id, facility, rating, vote_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $facility_name, $rating, $date]);

    header("Location: siyouritu.php");
    exit();
}

$date = date('Y-m-d');
$data = array_fill(1, 5, 0);
$stmt = $pdo->prepare("SELECT rating, COUNT(*) as count FROM votes WHERE facility = ? AND vote_date = ? GROUP BY rating");
$stmt->execute([$facility_name, $date]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[intval($row['rating'])] = intval($row['count']);
}
$totalVotes = array_sum($data);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>食堂の投票</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
</head>
<body class="vote-page">
    <h2>食堂の混雑状況を投稿</h2>
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
        <p>現在の状況を選んでください：</p>
        <select name="rating" required>
            <option value="1">たくさん空いてる</option>
            <option value="2">少し埋まってる</option>
            <option value="3">4割ほど埋まってる</option>
            <option value="4">8割ほど埋まってる</option>
            <option value="5">満席！誰も座れない！</option>
        </select>
        <button type="submit">送信</button>
    </form>

    <div class="chart-wrapper">
        <h3>投票状況 (<?= $date ?>)</h3>
        <canvas id="chart" class="canvas-input"></canvas>
        <p><strong>総投票数：<?= $totalVotes ?> 件</strong></p>
    </div>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['たくさん空いてる', '少し埋まってる', '4割ほど埋まってる' ,'8割埋まってる', '満席！誰も座れない！'],
                datasets: [{
                    label: '投票数',
                    data: <?= json_encode(array_values($data)) ?>,
                    backgroundColor: ['#ffb6c1', '#ffa07a', '#f08080', '#db7093', '#ff69b4']
                }]
            }
        });
    </script>
    <div class="back-button">
    <a href="siyouritu.php">
        <img src="/php/main/siyouritu/image/return.png" alt="戻る" class="return-icon">
    </a>
    <p class="return-text">もどる</p>
</div>
</body>
</html>