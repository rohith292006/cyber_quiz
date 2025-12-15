<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['user_id'])) die("Not logged in");
$user_id = (int)$_SESSION['user_id'];
$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;

$stmt = $mysqli->prepare("UPDATE users SET score=? WHERE id=?");
$stmt->bind_param("ii", $score, $user_id);
$stmt->execute();

echo "Score saved!";
?>
