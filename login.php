<?php
// login.php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';

if ($identifier === '' || $password === '') {
    echo "<script>alert('Please enter both fields'); window.location.href='index.html#auth';</script>";
    exit;
}

$stmt = $mysqli->prepare("SELECT id, full_name, email, username, password FROM users WHERE email = ? OR username = ? LIMIT 1");
$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];

        echo "<script>alert('Login Successful!'); window.location.href='index.html';</script>";
        exit;
    } else {
        echo "<script>alert('Incorrect password.'); window.location.href='index.html#auth';</script>";
        exit;
    }
} else {
    echo "<script>alert('No user found with this email or username.'); window.location.href='index.html#auth';</script>";
    exit;
}
?>
