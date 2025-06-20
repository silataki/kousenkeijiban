<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$facility = basename(__FILE__, '.php');
$facility_name = explode('_', $facility)[0];

require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $facility = $facility_name;
    $date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO votes (facility, rating, vote_date) VALUES (?, ?, ?)");
    $stmt->execute([$facility, $rating, $date]);

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
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= $facility_name ?>投票</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2><?= $facility_name === 'lirary' ? '図書館' : '食堂' ?>の混雑状況を投稿</h2>
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
        <p>1~5の値を選んでください：</p>
        <select name="rating" required>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
            </select>
        <button type="submit">送信</button>
    </form>

    <h3>投票状況 (<?= $date ?>)</h3>
    <canvas id="chart" width="400" height="400"></canvas>

    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['1', '2', '3' ,'4', '5'],
                datasets: [{
                    label: '投票数',
                    data: <?= json_encode(array_values($data)) ?>,
                    backgroundcolor: ['#ffb6c1', '#ffa07a', '#f08080', '#db7093', '#ff69b4']
                }]
            }
        });
    </script>
</body>
</html>
