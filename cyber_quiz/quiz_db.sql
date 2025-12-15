-- quiz_db.sql
CREATE DATABASE IF NOT EXISTS quiz_db;
USE quiz_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_identifier VARCHAR(120) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  score INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question TEXT NOT NULL,
  option1 VARCHAR(255) NOT NULL,
  option2 VARCHAR(255) NOT NULL,
  option3 VARCHAR(255) NOT NULL,
  option4 VARCHAR(255) NOT NULL,
  correct_option TINYINT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- sample questions
INSERT INTO questions (question, option1, option2, option3, option4, correct_option) VALUES
('What is CPU?', 'Central Processing Unit','Central Power Unit','Computer Process Utility','Control Program Unit',1),
('What is RAM?', 'Random Access Memory','Read Access Memory','Rapid Action Module','Run Active Memory',1),
('Which is an OS?', 'Windows','Chrome','Google','Intel',1);
