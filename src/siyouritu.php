<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用率</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">

</head>
<body>
    <div class="title-wrapper">
        <h1 class="title">施設使用状況</h1>
        <img src="neko2.gif" alt="装飾GIF" class="title-gif">
    </div>
    <div class="facilities">
        <div class="facility-block">
            <div class="facility-title">図書館</div>
            <div class="button-container">
                <form action="library_input.php" method="get">
                    <button class="library">図書館の使用状況を<br>ポストする</button>
                </form>
            </div>

            <iframe src="library_chart.php" class="chart-frame"></iframe>
        </div>

        <div class="facility-block">
            <div class="facility-title">食堂</div>
            <div class="button-container">
                <form action="cafeteria_input.php" method="get">
                    <button class="cafeteria">食堂の使用状況を<br>ポストする</button>
                </form>
            </div>
            <iframe src="cafeteria_chart.php" width="400" height="400" class="chart-frame"></iframe>
        </div>
    </div>
</body>
</html>
