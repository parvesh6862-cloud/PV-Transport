<?php
// contact_form.php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    $_SESSION['error'] = 'Please fill all fields.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/#contact'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email address.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/#contact'));
    exit;
}

// Store in DB
$stmt = $mysqli->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $email, $message);
$stored = $stmt->execute();
$stmt->close();

// Try to send email (optional)
$to = 'pvtransport@gmail.com';
$subject = "New Contact Message from $name";
$body = "You have received a new message via the website contact form.\n\nName: $name\nEmail: $email\n\nMessage:\n$message\n";
$headers = "From: $name <$email>\r\nReply-To: $email\r\n";

$mail_sent = false;
if (function_exists('mail')) {
    // mail() may not work on localhost without configuration
    $mail_sent = @mail($to, $subject, $body, $headers);
}

if ($stored) {
    $_SESSION['success'] = 'Your message was sent. Thank you!';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/#contact'));
    exit;
} else {
    $_SESSION['error'] = 'Failed to send your message; please try again later.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/#contact'));
    exit;
}
