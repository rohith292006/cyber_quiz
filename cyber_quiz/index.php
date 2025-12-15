<?php
require_once 'connect.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cyber Quiz</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
    .card{background:#fff;padding:28px;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.06);text-align:center}
    a.btn{display:inline-block;margin:8px;padding:10px 18px;border-radius:6px;background:#007bff;color:#fff;text-decoration:none}
  </style>
</head>
<body>
  <div class="card">
    <h1>Cyber Quiz</h1>
    <p>Choose role to continue</p>
    <a class="btn" href="login.php?role=user">User</a>
    <a class="btn" href="login.php?role=admin">Admin</a>
  </div>
</body>
</html>
