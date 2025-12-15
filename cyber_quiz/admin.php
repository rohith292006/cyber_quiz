<?php
require_once "connect.php";
require_once "auth.php";
require_admin();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

/* ---------------- RESET USER DATABASE ---------------- */
if(isset($_POST['reset_db'])){
    $mysqli->query("TRUNCATE TABLE users");
    $message = "User database reset successfully!";
}

/* ---------------- INSERT QUESTION ---------------- */
if(isset($_POST['insert'])){
    $q  = trim($_POST['question']);
    $o1 = trim($_POST['option1']);
    $o2 = trim($_POST['option2']);
    $o3 = trim($_POST['option3']);
    $o4 = trim($_POST['option4']);
    $correct = (int)$_POST['correct'];

    if($q && $o1 && $o2 && $o3 && $o4 && $correct>=1 && $correct<=4){
        $stmt = $mysqli->prepare(
            "INSERT INTO questions (question, option1, option2, option3, option4, correct)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->bind_param("sssssi",$q,$o1,$o2,$o3,$o4,$correct);
        $stmt->execute();
        $stmt->close();
        $message = "Question added successfully!";
    } else {
        $message = "Fill all fields correctly!";
    }
}

/* ---------------- UPDATE QUESTION ---------------- */
if(isset($_POST['update'])){
    $id = (int)$_POST['update_id'];
    $q  = trim($_POST['question']);
    $o1 = trim($_POST['option1']);
    $o2 = trim($_POST['option2']);
    $o3 = trim($_POST['option3']);
    $o4 = trim($_POST['option4']);
    $correct = (int)$_POST['correct'];

    if($id && $q && $o1 && $o2 && $o3 && $o4){
        $stmt = $mysqli->prepare(
            "UPDATE questions SET question=?, option1=?, option2=?, option3=?, option4=?, correct=? WHERE id=?"
        );
        $stmt->bind_param("ssssssi",$q,$o1,$o2,$o3,$o4,$correct,$id);
        $stmt->execute();
        $stmt->close();
        $message = "Question updated successfully!";
    }
}

/* ---------------- DELETE QUESTION ---------------- */
if(isset($_POST['delete'])){
    $id = (int)$_POST['delete_id'];
    if($id){
        $stmt = $mysqli->prepare("DELETE FROM questions WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $stmt->close();
        $message = "Question deleted!";
    }
}

/* ---------------- FETCH DATA ---------------- */
$qList  = $mysqli->query("SELECT * FROM questions");
$users  = $mysqli->query("SELECT * FROM users ORDER BY score DESC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Portal</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Orbitron',sans-serif}
body{background:#050009;color:#fff;display:flex;justify-content:center;padding:30px}
.container{width:75%;max-width:950px;background:rgba(20,0,40,.9);padding:30px;border:2px solid #7b2fff;border-radius:20px;box-shadow:0 0 30px #7b2fff}
h1{text-align:center;margin-bottom:20px}
button.tab{padding:12px 20px;margin:5px;border:none;border-radius:12px;background:#7b2fff;color:#fff;cursor:pointer}
button.tab.active,button.tab:hover{background:#00eaff;color:#000}
.tabcontent{display:none;margin-top:20px}
input,textarea,select{width:100%;padding:12px;margin:8px 0;border-radius:10px;background:#120020;color:#fff;border:1px solid #7b2fff}
button.submit{width:100%;padding:12px;background:#7b2fff;border:none;border-radius:10px;color:#fff;cursor:pointer}
button.submit:hover{background:#00eaff;color:#000}
table{width:100%;border-collapse:collapse;margin-top:15px}
th,td{border:1px solid #7b2fff;padding:10px}
th{background:#7b2fff;color:#000}
.message{text-align:center;color:#00eaff;margin-bottom:15px}
.exit{background:red;padding:10px 20px;border:none;border-radius:10px;color:#fff;float:right;cursor:pointer}
</style>
<script>
function openTab(id){
 document.querySelectorAll('.tabcontent').forEach(t=>t.style.display='none');
 document.querySelectorAll('.tab').forEach(b=>b.classList.remove('active'));
 document.getElementById(id).style.display='block';
 document.getElementById(id+'Btn').classList.add('active');
}
window.onload=()=>openTab('Insert');
</script>
</head>
<body>
<div class="container">
<h1>Admin Portal</h1>
<button class="exit" onclick="location.href='index.php'">Exit</button>
<p class="message"><?= $message ?></p>

<div style="text-align:center">
<button class="tab" id="InsertBtn" onclick="openTab('Insert')">Insert</button>
<button class="tab" id="UpdateBtn" onclick="openTab('Update')">Update</button>
<button class="tab" id="DeleteBtn" onclick="openTab('Delete')">Delete</button>
<button class="tab" id="ScoresBtn" onclick="openTab('Scores')">Scores</button>
</div>

<!-- INSERT -->
<div id="Insert" class="tabcontent">
<form method="post">
<textarea name="question" placeholder="Question" required></textarea>
<input name="option1" placeholder="Option 1" required>
<input name="option2" placeholder="Option 2" required>
<input name="option3" placeholder="Option 3" required>
<input name="option4" placeholder="Option 4" required>
<input type="number" name="correct" min="1" max="4" placeholder="Correct option" required>
<button name="insert" class="submit">Add Question</button>
</form>
</div>

<!-- UPDATE -->
<div id="Update" class="tabcontent">
<form method="post">
<select name="update_id" onchange="this.form.submit()" required>
<option value="">Select question</option>
<?php while($r=$qList->fetch_assoc()): ?>
<option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['question']) ?></option>
<?php endwhile; ?>
</select>
</form>

<?php if(isset($_POST['update_id'])):
$id=(int)$_POST['update_id'];
$q=$mysqli->query("SELECT * FROM questions WHERE id=$id")->fetch_assoc();
?>
<form method="post">
<input type="hidden" name="update_id" value="<?= $id ?>">
<textarea name="question"><?= $q['question'] ?></textarea>
<input name="option1" value="<?= $q['option1'] ?>">
<input name="option2" value="<?= $q['option2'] ?>">
<input name="option3" value="<?= $q['option3'] ?>">
<input name="option4" value="<?= $q['option4'] ?>">
<input type="number" name="correct" value="<?= $q['correct'] ?>" min="1" max="4">
<button name="update" class="submit">Update</button>
</form>
<?php endif; ?>
</div>

<!-- DELETE -->
<div id="Delete" class="tabcontent">
<form method="post">
<select name="delete_id" required>
<?php
$qList2=$mysqli->query("SELECT * FROM questions");
while($r=$qList2->fetch_assoc()):
?>
<option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['question']) ?></option>
<?php endwhile; ?>
</select>
<button name="delete" class="submit">Delete</button>
</form>
</div>

<!-- SCORES -->
<div id="Scores" class="tabcontent">
<table>
<tr><th>Name</th><th>User ID</th><th>Score</th></tr>
<?php while($u=$users->fetch_assoc()): ?>
<tr>
<td><?= $u['name'] ?></td>
<td><?= $u['user_identifier'] ?></td>
<td><?= $u['score'] ?></td>
</tr>
<?php endwhile; ?>
</table>
<form method="post" onsubmit="return confirm('Delete ALL users?')">
<button name="reset_db" class="submit" style="background:red">Reset User Database</button>
</form>
</div>

</div>
</body>
</html>
