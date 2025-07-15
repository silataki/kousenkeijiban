<?php
require_once 'db_config.php';
$filename = basename(__FILE__, '.php');
$facility = explode('_', $filename)[0];
$date = date('Y-m-d');
$weekday = date('w');

$timeSlotAverages = [];
$startHour = 12;
$endHour = 19;

for($hour = $startHour; $hour < $endHour; $hour++) {
    $start = sprintf('%02d:00:00', $hour);
    $end = sprintf('%02d:59:59', $hour);
    $label = sprintf('%02d:00', $hour);

    $stmt = $pdo->prepare("
        SELECT AVG(rating) as avg_rating 
        FROM votes
        WHERE facility = ? AND vote_date = ? AND vote_time BETWEEN ? AND ?
    ");
    $stmt->execute([$facility, $date, $start, $end]);
    $avg = $stmt->fetchColumn();    
    $avg = $avg !== null ? round($avg, 2) : 0;

    $timeSlotAverages[$label] = $avg;
    }

$stmt = $pdo->prepare("SELECT rating, COUNT(*) as count FROM votes WHERE facility = ? AND vote_date = ? GROUP BY rating");
$stmt->execute([$facility, $date]);

$data = array_fill(1, 5, 0);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[intval($row['rating'])] = intval($row['count']);
}

$totalVotes = array_sum($data);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $facility ?>Chart</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
    </head>
<body>
    <p class="lead">今日の混雑度</p>
    <canvas id="chart" class="canvas"></canvas>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['1. たくさん空いてる', '2. 少し埋まってる', '3. 4割ほど埋まってる', '4. 8割ほど埋まってる', '5. 満席！誰も座れない！'],
                datasets: [{
                    label: '投票数',
                    data: <?= json_encode(array_values($data)) ?>,
                    backgroundColor: ['#ffb6c1', '#ffa07a', '#f08080', '#db7093', '#ff69b4']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 20,
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                layout: {
                    padding: 0
                }
            }
        });
    </script>
    <p class="lead_bar">過去の平均混雑度:<?= ['日','月','火','水','木','金','土'][$weekday] ?>曜日</p>
    <canvas id="barChart" class="canvas" style="margin-top: 40px;"></canvas>
    <script>
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($timeSlotAverages)) ?>,
                datasets: [{
                    label: '平均混雑度（<?= ['日','月','火','水','木','金','土'][$weekday] ?>曜日）',
                    data: <?= json_encode(array_values($timeSlotAverages)) ?>,
                    backgroundColor: '#87cefa'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        title: {
                            display: true,
                            text: '評価（1〜5）'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
    <p><strong>投票日：<?= $date . '（' . ['日','月','火','水','木','金','土'][$weekday] . '）' ?></strong></p>
    <p><strong>総投票数：<?= $totalVotes ?> 件</strong></p>
</body>
</html>