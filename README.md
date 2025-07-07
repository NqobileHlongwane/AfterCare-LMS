# AfterCare LMS

A modern Online Learning Management System (LMS) for Elementary Learners and Teachers, built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features
- Secure registration and login for learners, teachers, and admins
- Role-based dashboards and navigation
- Group and assignment management
- File uploads and downloads
- Grading and feedback system
- Announcements and notifications
- Meeting link management
- Progress tracking (optional/future)
- Modern, responsive UI

## Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) or any LAMP/WAMP stack
- PHP 7.4+
- MySQL
- Git (for cloning)

### Installation
1. **Clone the repository:**
   ```bash
   git clone https://github.com/NqobileHlongwane/AfterCare-LMS.git
   ```
2. **Move the project to your XAMPP `htdocs` directory:**
   ```bash
   # Example for Windows
   move AfterCare-LMS C:/xampp/htdocs/LMS
   ```
3. **Import the database:**
   - Open phpMyAdmin
   - Create a new database (e.g., `lms`)
   - Import `database.sql` from the project root
4. **Configure the database connection:**
   - Edit `config.php` with your MySQL credentials
5. **Start Apache and MySQL from XAMPP Control Panel**
6. **Access the app:**
   - Go to [http://localhost/LMS/](http://localhost/LMS/) in your browser

## Usage
- Register as a learner or teacher
- Log in to access your personalized dashboard
- Teachers can create groups, assignments, upload files, and grade submissions
- Learners can view tasks, submit assignments, join meetings, and see feedback
- Admins can manage users and oversee the platform

## Folder Structure
- `learner/` — Learner features and dashboard
- `teacher/` — Teacher features and dashboard
- `admin/` — Admin dashboard (if implemented)
- `assets/` — CSS, uploads, and static files

## Security
- Passwords are hashed using PHP's `password_hash`
- Role-based access control for all dashboards
- Sensitive files and uploads are excluded from version control via `.gitignore`

## License
This project is for educational purposes. Please contact the author for commercial use or contributions.

---

**Author:** Nqobile Hlongwane  
**Contact:** nqobiletototo@gmail.com
