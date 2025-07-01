<?php
// index.php
include 'config.php'; // データベース接続設定を読み込み
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>先生リスト</title>
    <style>
        body { font-family: 'メイリオ', Meiryo, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        h1 { color:rgb(247, 151, 224); }
        h2 { color:rgb(247, 151, 224); border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 30px; margin-bottom: 15px; } /* 学科の見出し用スタイル */
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 10px; background-color: #fff; padding: 10px 15px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        a { text-decoration: none; color:rgb(247, 151, 224); font-weight: bold; }
        a:hover { text-decoration: underline; color:rgb(247, 151, 224); }
    </style>
</head>
<body>
    <h1>先生リスト</h1>

    <?php
    // teachersテーブルから先生のID、名前、学科を取得し、学科と名前でソート
    $sql = "SELECT id, name, department FROM teachers ORDER BY id ASC";
    $result = $conn->query($sql);

    $current_department = null; // 現在処理中の学科名を保持する変数

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // 学科が変わった場合、新しい学科の見出しを表示
            if ($row["department"] !== $current_department) {
                // 前の学科のリストが閉じられていない場合は閉じる
                if ($current_department !== null) {
                    echo '</ul>';
                }
                echo '<h2>' . htmlspecialchars($row["department"]) . '</h2>';
                echo '<ul>'; // 新しい学科のリストを開始
                $current_department = $row["department"];
            }

            // 先生の名前をリンクとして表示
            echo "<li><a href='teacher_detail.php?id=" . $row["id"] . "'>" . htmlspecialchars($row["name"]) . "</a></li>";
        }
        // 最後の学科のリストを閉じる
        echo '</ul>';
    } else {
        echo "<p>先生が見つかりませんでした。</p>";
    }
    $conn->close();
    ?>
</body>
</html>