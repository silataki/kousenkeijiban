<?php
session_start();
?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/header.php'; ?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/php/menu.php'; ?>
<?php
// 現在のセッション情報をクリア（これは必要に応じて）
//unset($_SESSION['users']);

$pdo = new PDO('mysql:host=localhost;dbname=testtest;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを設定しておくとデバッグしやすい

// ユーザーがフォームから送信したログイン名とパスワードを取得
$input_login = $_REQUEST['login'] ?? '';
$input_password = $_REQUEST['password'] ?? '';

// ----------------------------------------------------
// 1. ユーザー名でデータベースからユーザー情報を取得
// ----------------------------------------------------
// パスワードは後でpassword_verifyで検証するため、ここではWHERE句に入れない
$stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
$stmt->execute([$input_login]);
$users_data = $stmt->fetch(PDO::FETCH_ASSOC); // 連想配列でデータを取得

if ($users_data) {
    // ----------------------------------------------------
    // 2. パスワードを検証
    // ----------------------------------------------------
    // 入力されたパスワードと、データベースに保存されているハッシュ化されたパスワードを比較
    if (password_verify($input_password, $users_data['password'])) {
        // パスワードが一致した場合、セッションにユーザー情報を保存
        $_SESSION['users'] = [
            'id'       => $users_data['id'],
            'name'     => $users_data['name'],
            'address'  => $users_data['address'] ?? null, // addressカラムがない可能性も考慮
            'login'    => $users_data['login'],
            // パスワードはセッションに入れないのが一般的です（セキュリティのため）
            // 'password' => $users_data['password'] 
        ];

        /* セッション時にランダムで数字を発行→ハッシュ化する */
        /*
        if (!isset($_SESSION['user_id'])) {
        $session_id = session_id();
        $_SESSION['user_id'] = hexdec(substr(md5($session_id), 0, 8));
        }
        */

        echo 'ようこそ、', htmlspecialchars($_SESSION['users']['name']), 'さん。';
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