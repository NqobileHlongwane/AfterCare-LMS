# AfterCare-LMS
An Online Learning Management System developed to support aftercare classes in my community. The system enables interactive learning between students and teachers, with features like assignment uploads, group management, view results and feedback and notifications feature.

📌 Features
👩‍🏫 Teacher Dashboard

Upload assignments and study materials

Create and manage student groups

Send notifications to students

👨‍🎓 Student Dashboard
Track learning progress and results

Submit assignments

Receive notifications when new content is uploaded

📂 File Sharing

Teachers and students can send and receive files

🔔 Notifications

Real-time alerts for uploaded materials and assignments

🛠️ Tech Stack
| Technology  | Description              |
|-------------|--------------------------|
| HTML/CSS    | Front-end layout and styling |
| JavaScript  | Interactive behavior on the front end |
| PHP         | Server-side logic |
| MySQL       | Database for user data and content |
| XAMPP       | Local development environment (Apache + MySQL + PHP) |

🚀 Getting Started
1.Clone the repository:

bash
Copy
Edit
git clone https://github.com/your-username/your-repo-name.git


2.Move the project folder to your XAMPP htdocs directory:

makefile
Copy
Edit
C:\xampp\htdocs\

3.Start Apache and MySQL via XAMPP Control Panel

4.Create a MySQL database:

Visit http://localhost/phpmyadmin

Create a new database (e.g. lms_db)

Import the provided .sql file (if available)

5.Update database credentials in PHP files (if needed):

php
Copy
Edit
$host = "localhost";
$user = "root";
$password = "";
$db = "lms_db";

6.Open your browser and visit:

arduino
Copy
Edit
http://localhost/your-project-folder

📁 Folder Structure
pgsql
Copy
Edit
/lms
├── index.html
├── login.php
├── register.php
├── dashboard/
│   ├── student.php
│   └── teacher.php
├── includes/
│   ├── db.php
│   └── auth.php
├── uploads/
├── css/
│   └── style.css
└── js/
    └── script.js

    📄 License
This project is open source and available under the MIT License.

👤 Author
Nqobile Hlongwane
GitHub Profile

🙌 Acknowledgements
Built as part of a community effort to provide accessible digital learning tools for young learners.
