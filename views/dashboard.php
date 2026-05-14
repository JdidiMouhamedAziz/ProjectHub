<?php
session_start();
require_once(__DIR__ . "/../config/auth.php");

// Require user to be logged in
requireLogin();

$userRole = $_SESSION["user"]["role"];
$userName = $_SESSION["user"]["username"];

// Route based on role
if ($userRole === 'admin') {
    header("Location: admin/dashboard.php");
    exit();
} elseif ($userRole === 'manager') {
    header("Location: manager/dashboard.php");
    exit();
} else {
    header("Location: user/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        nav {
            background-color: #2c3e50;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-left {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            color: white;
            font-size: 0.9rem;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .welcome-title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .role-badge {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            text-transform: capitalize;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="nav-left">
            <div class="nav-brand">ProjectHub</div>
            <ul class="nav-links">
                <?php if ($userRole === "admin"): ?>
                    <li><a href="admin/dashboard.php">Dashboard</a></li>
                    <li><a href="admin/projects/list.php">Projects</a></li>
                    <li><a href="admin/users/list.php">Users</a></li>
                <?php elseif ($userRole === "manager"): ?>
                    <li><a href="manager/dashboard.php">Dashboard</a></li>
                    <li><a href="manager/projects/list.php">Projects</a></li>
                <?php elseif ($userRole === "user"): ?>
                    <li><a href="user/dashboard.php">Dashboard</a></li>
                    <li><a href="user/kanban/kanban.php">Kanban</a></li>
                    <li><a href="user/notifications/list.php">Notifications</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="nav-right">
            <span class="user-info">Welcome, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
         <?php
            header("Location: $userRole/dashboard.php");
            exit();
        ?>
    </div>
</body>
</html>
