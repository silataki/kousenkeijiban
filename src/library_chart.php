<?php
require_once 'db_config.php';
$facility = basename(__FILE__, '_chart.php');
$date = date('Y-m-d');

$stmt = $pdo->prepare("SELECT rating, COUNT(*) as count FROM votes WHERE facility = ? AND vote_date = ? GROUP BY rating");
$stmt->execute([$facility, $date]);

$date = array_fill(1, 5, 0);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[intval($row['rating'])] = intval($row['count']);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $facility ?>Chart</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
<body>
    <canvas id="chart" width="400" height="400"></canvas>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['1', '2', '3', '4', '5'],
                datasets: [{
                    label: '投票数',
                    data: <?= json_encode(array_values($data)) ?>,
                    backgroundcolor: ['#ffb6c1', '#ffa07a', '#f08080', '#db7093', '#ff69b4']
                }]
            }
        });
        </script>
        <p>投票日：<?= $date ?></p>
    </body>
</html>