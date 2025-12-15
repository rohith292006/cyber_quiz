<?php
require_once "connect.php";
require_once "auth.php";
require_user();

// Fetch all questions from DB
$q = $mysqli->query("SELECT * FROM questions ORDER BY id ASC");

$questions = [];
while ($row = $q->fetch_assoc()) {
    // Force correct column to INT
    $row["correct"] = intval($row["correct"]);
    $questions[] = $row;
}

// Encode questions for JS
$questionsJSON = json_encode($questions);
$username = $_SESSION["user_identifier"];
$user_id  = $_SESSION["user_id"];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cyber Computer Quiz</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Orbitron',sans-serif; }
body { background:#050009; color:#fff; display:flex; justify-content:center; padding-top:20px; }
#quizScreen, #resultScreen { width:70%; max-width:700px; padding:30px; background:rgba(20,0,40,0.85); border:2px solid #7b2fff; border-radius:15px; box-shadow:0 0 25px #7b2fff; }
button { padding:12px 25px; margin:10px; background:#7b2fff; border:none; border-radius:10px; color:#fff; cursor:pointer; }
button:hover { background:#00eaff; color:#000; }
#options button { width:100%; margin:5px 0; padding:12px; border-radius:8px; }
</style>
</head>
<body>

<div id="quizScreen">
    <h2 id="timer">Time: 10:00</h2>
    <h3 id="questionText"></h3>
    <div id="options"></div>
    <div>
        <button onclick="prevQuestion()">Previous</button>
        <button id="nextBtn" onclick="nextQuestion()">Next</button>
        <button id="finishBtn" onclick="finishQuiz()" style="display:none;">Finish</button>
    </div>
</div>

<div id="resultScreen" style="display:none;"></div>

<script>
// Load questions from PHP
const questions = <?php echo $questionsJSON; ?>;
const username  = "<?php echo $username; ?>";
const userId    = "<?php echo $user_id; ?>";

let current = 0;
let timeLeft = 600; // 10 minutes
let answers = Array(questions.length).fill(null);

// ----------------- TIMER -----------------
function startTimer(){
    const t = setInterval(()=>{
        timeLeft--;
        let m = Math.floor(timeLeft/60);
        let s = timeLeft%60;
        document.getElementById("timer").innerText = `Time: ${m}:${s<10?"0":""}${s}`;
        if(timeLeft<=0){ clearInterval(t); finishQuiz(); }
    },1000);
}

// ----------------- RENDER QUESTION -----------------
function renderQ(){
    const q = questions[current];
    document.getElementById("questionText").innerText = (current+1)+". "+q.question;

    let opt = "";
    [q.option1, q.option2, q.option3, q.option4].forEach((o,i)=>{
        let btnNum = i+1;
        opt += `<button onclick="selectOpt(${btnNum})" style="background:${answers[current]===btnNum?'#00eaff':'#7b2fff'}">${o}</button>`;
    });

    document.getElementById("options").innerHTML = opt;

    document.getElementById("nextBtn").style.display = current===questions.length-1 ? "none":"inline-block";
    document.getElementById("finishBtn").style.display = current===questions.length-1 ? "inline-block":"none";
}

function selectOpt(i){
    answers[current] = i;
    renderQ();
}

function nextQuestion(){ if(current<questions.length-1){ current++; renderQ(); } }
function prevQuestion(){ if(current>0){ current--; renderQ(); } }

// ----------------- FINISH QUIZ -----------------
function finishQuiz(){
    let score = 0;
    questions.forEach((q,i)=>{
        if(answers[i] === q.correct) score++; // compare with correct
    });

    // Save score to DB using fetch
    fetch("save_score.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`score=${score}&user_id=${userId}`
    });

    // Show results
    let html = `<h2>${username}'s Result</h2><h3>Score: ${score}/${questions.length}</h3><br>`;

    questions.forEach((q,i)=>{
        const userAns = answers[i] ? q["option"+answers[i]] : "No Answer";
        const correctAns = q["option"+q.correct];
        html += `
        <div style="border:1px solid #7b2fff; padding:10px; margin:10px 0;">
            <b>${i+1}. ${q.question}</b><br>
            Correct: ${correctAns}<br>
            Your Answer: ${userAns}
        </div>`;
    });

    html += `<button onclick="location.href='index.php'">Home</button>`;

    document.getElementById("quizScreen").style.display = "none";
    let r = document.getElementById("resultScreen");
    r.innerHTML = html;
    r.style.display = "block";
}

// ----------------- INIT -----------------
renderQ();
startTimer();
</script>

</body>
</html>
