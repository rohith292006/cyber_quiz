<?php
require_once "connect.php";
require_once "auth.php";

// Start session safely
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$role = $_GET["role"] ?? "user"; // default role
$error = "";

// POST handling
if($_SERVER["REQUEST_METHOD"] === "POST"){

    // ---------- USER REGISTRATION ----------
    if($role === "user" && ($_POST["action"] ?? "") === "register"){
        $uid = trim($_POST["user_identifier"]);
        $name = trim($_POST["name"]);
        $password = $_POST["password"];

        if($uid === "" || $name === "" || $password === ""){
            $error = "All fields are required.";
        } else {
            $stmt = $mysqli->prepare("SELECT id FROM users WHERE user_identifier=?");
            $stmt->bind_param("s", $uid);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows > 0){
                $error = "User ID already exists.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $mysqli->prepare("INSERT INTO users (user_identifier, name, password_hash) VALUES (?,?,?)");
                $ins->bind_param("sss", $uid, $name, $hash);

                if($ins->execute()){
                    $_SESSION["user_id"] = $ins->insert_id;
                    $_SESSION["user_identifier"] = $uid;
                    header("Location: quiz.php");
                    exit;
                } else {
                    $error = "Registration failed.";
                }
            }
        }
    }

    // ---------- ADMIN LOGIN ----------
    if($role === "admin" && ($_POST["action"] ?? "") === "admin_login"){
        $admin_pass = $_POST["password"] ?? "";
        if($admin_pass === "rohith29"){ // your admin password
            $_SESSION["is_admin"] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Wrong password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= ucfirst($role) ?> Login</title>
<style>
body {
    background:#050009;
    font-family:'Orbitron',sans-serif;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}
.login-box {
    background: rgba(20,0,40,0.95);
    padding:40px;
    border-radius:15px;
    border:2px solid #7b2fff;
    box-shadow:0 0 25px #7b2fff;
    width:320px;
    text-align:center;
}
.login-box h2 {
    color:#00eaff;
    margin-bottom:20px;
}
.login-box input {
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:10px;
    border:1px solid #7b2fff;
    background:#120020;
    color:#fff;
}
.login-box button {
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#7b2fff;
    color:#fff;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}
.login-box button:hover {
    background:#00eaff;
    color:#000;
}
.login-box p.error {
    color:red;
    margin-bottom:10px;
}
.login-box a {
    color:#00eaff;
    text-decoration:none;
    font-size:14px;
}
.login-box a:hover {
    text-decoration:underline;
}
</style>
</head>
<body>
<div class="login-box">
    <h2><?= ucfirst($role) ?> Login</h2>
    <?php if($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if($role === "user"): ?>
    <form method="post">
        <input type="hidden" name="action" value="register">
        <input type="text" name="user_identifier" placeholder="User ID" required>
        <input type="text" name="name" placeholder="Name" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register & Start Quiz</button>
    </form>
    <?php else: ?>
    <form method="post">
        <input type="hidden" name="action" value="admin_login">
        <input type="password" name="password" placeholder="Admin Password" required>
        <button type="submit">Login as Admin</button>
    </form>
    <?php endif; ?>

    <p><a href="index.php">Back</a></p>
</div>
</body>
</html>
