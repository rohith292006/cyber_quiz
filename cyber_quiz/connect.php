<?php
// connect.php - edit DB credentials if necessary
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'quiz_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('DB connect error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
