<!-- tomoka add -->
<!-- start -->
<?php 
// セッションがまだ開始されていなければ開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!-- end -->

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用率</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nikumaru&display=swap" rel="stylesheet">
</head>
<body>
    <h1 class="title">施設使用状況</h1>
    <div class="facilities">
        <div class="facility-block left-align">
            <div class="facility-title">図書館</div>
            <div class="button-container">
                <form action="library_input.php" method="get">
                    <button type="submit" class="library">図書館の使用状況を<br>ポストする</button>
                </form>
            </div>

            <iframe src="library_chart.php" class="chart-frame left-align"></iframe>
        </div>

        <div class="facility-block">
            <div class="facility-title">食堂</div>
            <div class="button-container">
                <form action="cafeteria_input.php" method="get">
                    <button type="submit" class="cafeteria">食堂の使用状況を<br>ポストする</button>
                </form>
            </div>
            <iframe src="cafeteria_chart.php" width="400" height="400" class="chart-frame"></iframe>
        </div>
    </div>
</body>
</html>

<!-- tomoka add -->
<!-- start -->
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php';
?>
<!-- end -->