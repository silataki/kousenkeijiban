<?php
// config.php

$servername = "localhost"; // データベースサーバーのホスト名
$username = "root"; // あなたのデータベースのユーザー名に置き換えてください
$password = ""; // あなたのデータベースのパスワードに置き換えてください
$dbname = "school_db"; // 使用するデータベース名

// データベースへの接続を作成
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーの確認
if ($conn->connect_error) {
    die("データベース接続失敗: " . $conn->connect_error);
}

// 文字コードをUTF-8に設定（日本語表示のため重要）
$conn->set_charset("utf8mb4");
?>