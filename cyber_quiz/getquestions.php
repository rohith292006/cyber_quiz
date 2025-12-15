<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');
$res = $mysqli->query('SELECT id,question,option1,option2,option3,option4 FROM questions ORDER BY id');
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
echo json_encode(['questions' => $rows], JSON_UNESCAPED_UNICODE);
