<?php
session_start();
$name = isset($_SESSION['name']) ? $_SESSION['name'] : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%);
            margin: 0;
            min-height: 100vh;
        }
        .navbar {
            background: #1e293b;
            color: #fff;
            padding: 16px 0 16px 0;
            text-align: center;
            font-size: 1.3rem;
            letter-spacing: 1px;
            margin-bottom: 40px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.08);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .navbar-logo {
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            height: 48px;
            display: flex;
            align-items: center;
            background: #fff;
            padding: 4px 12px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.10);
        }
        .navbar-logo img {
            max-height: 48px;
            width: auto;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(30,41,59,0.10);
            padding: 32px 28px 24px 28px;
        }
        h2 {
            color: #1e293b;
            margin-bottom: 8px;
            text-align: center;
            font-weight: 700;
        }
        .welcome {
            text-align: center;
            font-size: 1.2rem;
            color: #334155;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="logo/aftercare-logo.jpg" alt="AfterCare LMS Logo">
        </div>
        <span style="flex:1;text-align:center;">Online Learning Management System</span>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" style="margin-right:24px;color:#fff;font-weight:500;text-decoration:none;background:#6366f1;padding:8px 18px;border-radius:7px;transition:background 0.2s;">Logout</a>
        <?php endif; ?>
    </div>
    <div class="container">
        <?php if ($name && $role === 'learner'): ?>
            <div class="welcome">Welcome, <?php echo htmlspecialchars($name); ?></div>
        <?php endif; ?>
        <!-- ...existing homepage content (banners, links, etc.)... -->
        <?php include 'index.html'; ?>
    </div>
</body>
</html>
