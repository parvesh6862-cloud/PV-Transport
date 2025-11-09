<?php
// register.php
session_start();
require_once 'db_connect.php';

// Enable error display (for debugging; remove later)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Get form data
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// Validate input
if ($full_name === '' || $email === '' || $password === '' || $confirm === '') {
    echo "<script>alert('Please fill in all fields.'); window.location.href='index.html#auth';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email address.'); window.location.href='index.html#auth';</script>";
    exit;
}

if ($password !== $confirm) {
    echo "<script>alert('Passwords do not match.'); window.location.href='index.html#auth';</script>";
    exit;
}

// Check if user already exists
$stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('An account with this email already exists.'); window.location.href='index.html#auth';</script>";
    $stmt->close();
    exit;
}
$stmt->close();

// Insert new user
$hash = password_hash($password, PASSWORD_DEFAULT);
$insert = $mysqli->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
$insert->bind_param('sss', $full_name, $email, $hash);

if ($insert->execute()) {
    echo "<script>alert('Registration successful! You can now log in.'); window.location.href='index.html#auth';</script>";
    exit;
} else {
    echo "<script>alert('Registration failed. Please try again later.'); window.location.href='index.html#auth';</script>";
    exit;
}
?>
