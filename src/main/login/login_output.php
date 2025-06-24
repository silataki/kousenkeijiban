<?php
session_start();
?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/header.php'; ?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php'; ?>
<?php
// 現在のセッション情報をクリア（これは必要に応じて）
unset($_SESSION['customer']);

$pdo = new PDO('mysql:host=localhost;dbname=shop2;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを設定しておくとデバッグしやすい

// ユーザーがフォームから送信したログイン名とパスワードを取得
$input_login = $_REQUEST['login'] ?? '';
$input_password = $_REQUEST['password'] ?? '';

// ----------------------------------------------------
// 1. ユーザー名でデータベースからユーザー情報を取得
// ----------------------------------------------------
// パスワードは後でpassword_verifyで検証するため、ここではWHERE句に入れない
$stmt = $pdo->prepare('SELECT * FROM customer WHERE login = ?');
$stmt->execute([$input_login]);
$customer_data = $stmt->fetch(PDO::FETCH_ASSOC); // 連想配列でデータを取得

if ($customer_data) {
    // ----------------------------------------------------
    // 2. パスワードを検証
    // ----------------------------------------------------
    // 入力されたパスワードと、データベースに保存されているハッシュ化されたパスワードを比較
    if (password_verify($input_password, $customer_data['password'])) {
        // パスワードが一致した場合、セッションにユーザー情報を保存
        $_SESSION['customer'] = [
            'id'       => $customer_data['id'],
            'name'     => $customer_data['name'],
            'address'  => $customer_data['address'] ?? null, // addressカラムがない可能性も考慮
            'login'    => $customer_data['login'],
            // パスワードはセッションに入れないのが一般的です（セキュリティのため）
            // 'password' => $customer_data['password'] 
        ];

        echo 'ようこそ、', htmlspecialchars($_SESSION['customer']['name']), 'さん。';
    } else {
        // パスワードが一致しない場合
        echo 'ログイン名またはパスワードが違います。';
    }
} else {
    // ユーザー名が見つからない場合
    echo 'ログイン名またはパスワードが違います。';
}
?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/footer.php'; ?>