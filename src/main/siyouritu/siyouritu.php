<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>siyouritu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>施設使用状況</h1>
    <div class="text-container">
        <p>図書館</p>
        <p>食堂</p>
    </div>
    <div class="button-container">
        <form action="library_input.php" method="get">
            <button type="submit" class="library">図書館の使用状況を<br>ポストする</button>
        </form>
        <form action="cafeteria_input.php" method="get">
            <button type="submit" class="cafeteria">食堂の利用状況を<br>ポストする</button>
        </form>
    </div>

    <div class="charts">
        <iframe src="library_chart.php" width="400" height="400"></iframe>
        <iframe src="cafeteria_chart.php" width="400" height="400"></iframe>
    </div>
</body>
</html>