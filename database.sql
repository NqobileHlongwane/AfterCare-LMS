CREATE TABLE announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT,
  teacher_id INT,
  title VARCHAR(255),
  message TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES groups(id),
  FOREIGN KEY (teacher_id) REFERENCES users(id)
);
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  role ENUM('learner','teacher','admin'),
  grade VARCHAR(20) NULL,
  password_reset_token VARCHAR(255) NULL,
  token_expiration DATETIME NULL
);

CREATE TABLE groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  teacher_id INT,
  FOREIGN KEY (teacher_id) REFERENCES users(id)
);

CREATE TABLE group_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT,
  learner_id INT,
  FOREIGN KEY (group_id) REFERENCES groups(id),
  FOREIGN KEY (learner_id) REFERENCES users(id)
);

CREATE TABLE assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT,
  title VARCHAR(255),
  description TEXT,
  file_path VARCHAR(255),
  due_date DATE,
  FOREIGN KEY (group_id) REFERENCES groups(id)
);

CREATE TABLE submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT,
  learner_id INT,
  file_path VARCHAR(255),
  status ENUM('pending','submitted','graded'),
  submitted_at DATETIME,
  FOREIGN KEY (assignment_id) REFERENCES assignments(id),
  FOREIGN KEY (learner_id) REFERENCES users(id)
);

CREATE TABLE grades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  submission_id INT,
  teacher_id INT,
  mark INT,
  comment TEXT,
  FOREIGN KEY (submission_id) REFERENCES submissions(id),
  FOREIGN KEY (teacher_id) REFERENCES users(id)
);

CREATE TABLE meetings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT,
  link VARCHAR(255),
  date DATETIME,
  description TEXT,
  FOREIGN KEY (group_id) REFERENCES groups(id)
);

CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  message TEXT,
  is_read BOOLEAN DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE files (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT,
  receiver_id INT NULL,
  group_id INT NULL,
  file_path VARCHAR(255),
  file_type VARCHAR(20),
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id),
  FOREIGN KEY (receiver_id) REFERENCES users(id),
  FOREIGN KEY (group_id) REFERENCES groups(id)
);