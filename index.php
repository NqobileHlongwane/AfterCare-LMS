<?php
session_start();
$name = isset($_SESSION['name']) ? $_SESSION['name'] : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Learning Management System</title>
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
            padding: 16px 0;
            text-align: center;
            font-size: 1.3rem;
            letter-spacing: 1px;
            margin-bottom: 40px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.08);
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
        Online Learning Management System
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
