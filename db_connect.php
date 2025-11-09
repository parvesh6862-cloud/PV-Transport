<?php
// db_connect.php
// Adjust these settings if your MySQL credentials differ
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default is empty
$DB_NAME = 'pv_transport';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_error) {
    // In production, log error rather than echo
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Set charset
$mysqli->set_charset('utf8mb4');
