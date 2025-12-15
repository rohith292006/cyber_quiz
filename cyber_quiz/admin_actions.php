<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "connect.php";
require_once "auth.php";
require_admin();

$action = $_GET['action'] ?? '';
$message = '';

function option_has_text($str) {
    return preg_match('/[a-zA-Z]/', $str);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- INSERT ----------
    if ($action === 'insert') {
        $q = trim($_POST['question'] ?? '');
        $o1 = trim($_POST['option1'] ?? '');
        $o2 = trim($_POST['option2'] ?? '');
        $o3 = trim($_POST['option3'] ?? '');
        $o4 = trim($_POST['option4'] ?? '');
        $correct = (int)($_POST['correct'] ?? 0);

        if (!$q || !$o1 || !$o2 || !$o3 || !$o4) {
            $message = "All fields are required.";
        } elseif ($correct < 1 || $correct > 4) {
            $message = "Correct option must be between 1 and 4.";
        } elseif (!option_has_text($o1) || !option_has_text($o2) || !option_has_text($o3) || !option_has_text($o4)) {
            $message = "Options must contain text (not only numbers).";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO questions (question, option1, option2, option3, option4, correct) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("sssssi", $q, $o1, $o2, $o3, $o4, $correct);
            $message = $stmt->execute() ? "Question added successfully!" : "Error adding question: " . $stmt->error;
            $stmt->close();
        }
    }

    // ---------- UPDATE ----------
    if ($action === 'update') {
        $id = (int)($_POST['update_id'] ?? 0);
        $q = trim($_POST['question'] ?? '');
        $o1 = trim($_POST['option1'] ?? '');
        $o2 = trim($_POST['option2'] ?? '');
        $o3 = trim($_POST['option3'] ?? '');
        $o4 = trim($_POST['option4'] ?? '');
        $correct = (int)($_POST['correct'] ?? 0);

        if (!$id || !$q || !$o1 || !$o2 || !$o3 || !$o4) {
            $message = "Please select a question and fill all fields.";
        } elseif ($correct < 1 || $correct > 4) {
            $message = "Correct option must be between 1 and 4.";
        } elseif (!option_has_text($o1) || !option_has_text($o2) || !option_has_text($o3) || !option_has_text($o4)) {
            $message = "Options must contain text (not only numbers).";
        } else {
            $stmt = $mysqli->prepare("UPDATE questions SET question=?, option1=?, option2=?, option3=?, option4=?, correct=? WHERE id=?");
            $stmt->bind_param("ssssssi", $q, $o1, $o2, $o3, $o4, $correct, $id);
            $message = $stmt->execute() ? "Question updated successfully!" : "Error updating question: " . $stmt->error;
            $stmt->close();
        }
    }

    // ---------- DELETE ----------
    if ($action === 'delete') {
        $id = (int)($_POST['delete_id'] ?? 0);
        if ($id) {
            $stmt = $mysqli->prepare("DELETE FROM questions WHERE id=?");
            $stmt->bind_param("i", $id);
            $message = $stmt->execute() ? "Question deleted." : "Error deleting question: " . $stmt->error;
            $stmt->close();
        } else {
            $message = "Please select a question.";
        }
    }

    // ---------- RESET SCORES ----------
    if ($action === 'reset_scores') {
        $result = $mysqli->query("UPDATE users SET score=0");
        $message = $result ? "All user scores have been reset." : "Error resetting scores.";
    }

    // ---------- CHANGE PASSWORD ----------
    if ($action === 'change_password') {
        $current = $_POST['current_pass'] ?? '';
        $new     = $_POST['new_pass'] ?? '';
        $confirm = $_POST['confirm_pass'] ?? '';

        if (!$current || !$new || !$confirm) {
            $message = "All password fields are required.";
        } elseif ($new !== $confirm) {
            $message = "New password and confirm password do not match!";
        } else {
            $stmt = $mysqli->prepare("SELECT id, password FROM admins LIMIT 1");
            $stmt->execute();
            $stmt->bind_result($admin_id, $hash);
            if ($stmt->fetch() && $hash) {
                $stmt->close();
                if (password_verify($current, $hash)) {
                    $new_hash = password_hash($new, PASSWORD_DEFAULT);
                    $stmt = $mysqli->prepare("UPDATE admins SET password=? WHERE id=?");
                    $stmt->bind_param("si", $new_hash, $admin_id);
                    $message = $stmt->execute() ? "Password changed successfully!" : "Error updating password.";
                    $stmt->close();
                } else {
                    $message = "Current password is incorrect!";
                }
            } else {
                $message = "Admin account not found!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Actions</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Orbitron',sans-serif}
body{background:#050009;color:#fff;display:flex;justify-content:center;padding:30px}
.container{width:70%;max-width:800px;background:rgba(20,0,40,.85);padding:30px;border:2px solid #7b2fff;border-radius:15px;box-shadow:0 0 25px #7b2fff}
h1,h2{text-align:center;margin-bottom:20px}
textarea,input,select,button{width:100%;padding:12px;margin:8px 0;border-radius:10px}
textarea,input,select{background:#120020;color:#fff;border:1px solid #7b2fff}
button{background:#7b2fff;color:#fff;border:none;cursor:pointer}
button:hover{background:#00eaff;color:#000}
p.message{text-align:center;color:#00eaff;margin-bottom:20px}
.back{background:#ff4444}
.back:hover{background:#ff0000;color:#fff}
</style>
</head>
<body>
<div class="container">
<h1>Admin Panel → <?= strtoupper(htmlspecialchars($action ?: "HOME")) ?></h1>
<?php if ($message): ?>
<p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<br>
<button class="back" onclick="location.href='admin.php'">Back to Admin Panel</button>
</div>
</body>
</html>
