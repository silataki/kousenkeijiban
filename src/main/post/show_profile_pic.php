<?php
$conn = new mysqli("localhost", "root", "", "testtest");
if ($conn->connect_error) {
    http_response_code(500);
    exit("DB接続失敗");
}

$userId = $_GET['id'] ?? 0;
if (!ctype_digit((string)$userId)) {
    http_response_code(400);
    exit("無効なID");
}

$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    if (!empty($row['profile_pic'])) {
        header("Content-Type: image/jpeg"); // PNGなら image/png に変更
        echo $row['profile_pic'];
        exit;
    }
}

http_response_code(404);
exit("画像が見つかりません");
