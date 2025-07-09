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
    body { 
        font-family: 'メイリオ', Meiryo, sans-serif; 
        margin: 0; 
        background-color: #f4f4f4; 
        color: #333; 
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        padding: 20px 0;
        box-sizing: border-box;
    }
    .container { 
        max-width: 400px;
        width: 90%;
        padding: 30px;
        border: 1px solid #ddd; 
        border-radius: 8px; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        background-color: #fff; 
        position: relative;
        overflow: hidden;
    }

    /* 新しく追加するスタイル */
    .name-section {
        display: flex; /* Flexboxを適用 */
        align-items: center; /* 縦方向の中央揃え */
        justify-content: flex-start; /* 左寄せ */
        padding-bottom: 10px;
        margin-bottom: 30px;
        position: relative;
    }
    .name-section::after { /* 下線をCSSで作成 */
        content: '';
        display: block;
        width: 100%;
        height: 1px;
        background-color: #eee;
        position: absolute;
        bottom: 0;
        left: 0;
    }

    /* 名前 (h1) のスタイル調整 */
    h1 { 
        font-size: 26px;
        color: #333; 
        text-align: center; /* Flexboxで中央寄せするので不要になるが、念のため残す */
        padding-bottom: 0; /* 親の.name-sectionで余白を管理 */
        margin-bottom: 0; /* 親の.name-sectionで余白を管理 */
        margin-right: 10px; /* 名前と猫の間のスペース */
    }

    .info-section {
        margin-bottom: 25px;
    }
    .info-label {
        background-color: #ffe0f0;
        color: #d1478f;
        padding: 4px 10px;
        border-radius: 5px;
        display: inline-block;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .info-content {
        padding-left: 5px;
        line-height: 1.8;
        font-size: 15px;
        word-wrap: break-word;
    }

    /* 担当科目リストのスタイル */
    .info-content ul {
        list-style: none;
        padding-left: 0;
        margin-top: 5px; 
        margin-bottom: 0;
    } 
    .info-content li { 
        margin-bottom: 3px; 
        position: relative;
        padding-left: 1.2em;
    }
    .info-content li::before {
        content: "・";
        position: absolute;
        left: 0;
        top: 0;
    }

    /* 部屋の場所の丸文字 */
    .info-content.room-content::before {
        content: " ";
        margin-right: 5px;
        display: inline-block;
    }

    /* 連絡先の矢印 */
    .info-content.contact-content::before {
        content: "gmail → ";
        margin-right: 5px;
        display: inline-block;
    }

    .back-link { 
        display: block;
        width: fit-content;
        margin: 30px auto 0;
        text-decoration: none; 
        color:rgb(247, 151, 224); 
        padding: 8px 15px; 
        border: 1px solid rgb(247, 151, 224);
        border-radius: 5px; 
        transition: background-color 0.3s, color 0.3s; 
        font-size: 15px;
    }
    .back-link:hover { 
        background-color:rgb(247, 151, 224); 
        color: #fff; 
        text-decoration: none; 
    }

    /* 猫の画像スタイル調整 */
    .cat-image {
        width: 50px; /* 画像のサイズを調整 */
        height: auto;
        opacity: 1; /* 透過度をなくすか調整 */
        vertical-align: middle; 
        margin-left: auto; /* 名前との間にスペースを追加 */
        margin-top: 5px;
    }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($teacher_data): // 先生のデータが見つかった場合 ?>
            <div class="name-section"> <h1><?php echo htmlspecialchars($teacher_data['name']); ?></h1>
            <img src="nyan.png" alt="猫のイラスト" class="cat-image"> 
        </div>
            
            <div class="info-section">
                <span class="info-label">部屋の場所</span>
                <p class="info-content room-content"><?php echo htmlspecialchars($teacher_data['room_number']); ?></p>
            </div>

            <div class="info-section">
                <span class="info-label">担当科目</span>
                <div class="info-content">
                <?php
                if (isset($teacher_data['subjects']) && $teacher_data['subjects'] !== '') {
                    $subjects_array = explode(',', $teacher_data['subjects']);
                    echo '<ul>';
                    foreach ($subjects_array as $subject) {
                        echo '<li>' . htmlspecialchars(trim($subject)) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>設定されていません</p>';
                }
                ?>
                </div>
            </div>

            <div class="info-section">
                <span class="info-label">連絡先</span>
                <p class="info-content contact-content"><?php echo htmlspecialchars($teacher_data['contact_info']); ?></p>
            </div>

            <div class="info-section">
                <span class="info-label">コメント</span>
                <p class="info-content"><?php echo nl2br(htmlspecialchars($teacher_data['comments'])); ?></p>
            </div>

            <!-- <img src="nyan.png" alt="猫のイラスト" class="cat-image">  --> 

        <?php else: // 先生のデータが見つからなかった場合 ?>
            <p>指定された先生は見つかりませんでした。</p>
        <?php endif; ?>
        <a href="teacher_list.php" class="back-link">← 先生リストに戻る</a>
    </div>
</body>
</html>