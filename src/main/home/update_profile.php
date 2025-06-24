<?php
session_start();

$pdo = new PDO('mysql:host=localhost;dbname=shop2;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['customer'])) {
    echo "Unauthorized access.";
    exit;
}

$id    = $_SESSION['customer']['id'];
$name  = $_POST['name'] ?? '';
$login = $_POST['login'] ?? '';
$email = $_POST['email'] ?? '';
$profile_picture = null;

// Email format check
if (!preg_match('/^k[0-9]{5}[a-z]{2}@apps\.kct\.ac\.jp$/', $email)) {
    echo "Invalid email format.";
    exit;
}

// File upload check
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
}

try {
    if ($profile_picture !== null) {
        // Update including profile picture
        $stmt = $pdo->prepare("UPDATE customer SET name = ?, login = ?, email = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$name, $login, $email, $profile_picture, $id]);
    } else {
        // Update without changing profile picture
        $stmt = $pdo->prepare("UPDATE customer SET name = ?, login = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $login, $email, $id]);
    }

    // Refresh session data
    $stmt = $pdo->prepare("SELECT * FROM customer WHERE id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['customer'] = $customer;

    header('Location: homepage.php');
    exit;
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
