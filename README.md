# 🔥 Cyber Computer Quiz

A **PHP and MySQL based quiz application** built using XAMPP. Users can register, take a timed quiz, and track their scores. Admins can manage questions and view user scores.

---

## 📝 Features

- User registration and login
- Timed quiz (10 minutes)
- Questions loaded dynamically from the database
- Admin panel to **insert, update, delete questions**
- Score tracking for users
- Responsive and interactive quiz interface

---

## 💻 Technologies Used

- **Backend:** PHP  
- **Database:** MySQL  
- **Frontend:** HTML, CSS, JavaScript  
- **Server:** XAMPP (Apache + MySQL)

---

## 🗂️ Project Structure

cyber_quiz/
├── admin.php # Admin panel
├── admin_actions.php # Admin actions backend
├── auth.php # Authentication checks
├── connect.example.php # DB config template (for GitHub)
├── connect.php # Local DB config (not uploaded)
├── getquestions.php # Fetch questions API
├── index.php # Home page
├── login.php # User/Admin login
├── logout.php # Logout
├── quiz.php # Quiz interface
├── save_score.php # Save user score
├── quiz_db.sql # Database structure + sample questions
├── README.md # Project documentation
└── .gitignore # Ignore sensitive files
