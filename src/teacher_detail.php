<?php
// teacher_detail.php
include 'config.php'; // データベース接続設定を読み込み

$teacher_id = null;
// URLのGETパラメータから'id'を取得し、数値であることを確認
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $teacher_id = $_GET['id'];
}

$teacher_data = null;
if ($teacher_id) {
    // SQLインジェクションを防ぐためにプリペアドステートメントを使用
    $stmt = $conn->prepare("SELECT name, room_number, subjects, contact_info, comments FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $teacher_id); // 'i'はIDが整数型であることを示す
    $stmt->execute(); // ステートメントを実行
    $result = $stmt->get_result(); // 結果を取得

    if ($result->num_rows > 0) {
        $teacher_data = $result->fetch_assoc(); // 結果を連想配列として取得
    }
    $stmt->close(); // ステートメントを閉じる
}
$conn->close(); // データベース接続を閉じる
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $teacher_data ? htmlspecialchars($teacher_data['name']) . '先生の詳細' : '先生の情報'; ?></title>
    <style>
        body { font-family: 'メイリオ', Meiryo, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 25px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); background-color: #fff; }
        h1 { color:rgb(247, 151, 224); border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        p { margin-bottom: 12px; line-height: 1.6; }
        strong { color: #555; display: inline-block; min-width: 90px; } /* ラベルの幅を揃える */
        ul { list-style: none; padding-left: 0; margin-top: 5px; } /* リストのデフォルトスタイルを調整 */
        li { margin-bottom: 3px; }
        .back-link { display: inline-block; margin-top: 25px; text-decoration: none; color:rgb(247, 151, 224); padding: 8px 15px; border: 1px solid #007bff; border-radius: 5px; transition: background-color 0.3s, color 0.3s; }
        .back-link:hover { background-color:rgb(247, 151, 224); color: #fff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($teacher_data): // 先生のデータが見つかった場合 ?>
            <h1><?php echo htmlspecialchars($teacher_data['name']); ?>先生</h1>
            <p><strong>部屋の場所:</strong> <?php echo htmlspecialchars($teacher_data['room_number']); ?></p>

            <?php
            // 担当科目を箇条書きで表示
            if (isset($teacher_data['subjects']) && $teacher_data['subjects'] !== '') {
                // データベースから取得した担当科目文字列をカンマ (,) で分割し、配列にする
                $subjects_array = explode(',', $teacher_data['subjects']);

                echo '<p><strong>担当科目:</strong></p>'; // 「担当科目:」のラベル表示
                echo '<ul>'; // 箇条書きリストの開始
                foreach ($subjects_array as $subject) {
                    // 各科目の前後の空白を取り除き (trim)、HTMLエスケープ (htmlspecialchars) してリストアイテムとして表示
                    echo '<li>・' . htmlspecialchars(trim($subject)) . '</li>';
                }
                echo '</ul>'; // 箇条書きリストの終了
            } else {
                // 担当科目が設定されていない場合の表示
                echo '<p><strong>担当科目:</strong> 設定されていません</p>';
            }
            ?>

            <p><strong>連絡先:</strong> <?php echo htmlspecialchars($teacher_data['contact_info']); ?></p>
            <p><strong>コメント:</strong> <?php echo nl2br(htmlspecialchars($teacher_data['comments'])); ?></p>
        <?php else: // 先生のデータが見つからなかった場合 ?>
            <p>指定された先生は見つかりませんでした。</p>
        <?php endif; ?>
        <a href="teacher_list.php" class="back-link">← 先生リストに戻る</a>
    </div>
</body>
</html>