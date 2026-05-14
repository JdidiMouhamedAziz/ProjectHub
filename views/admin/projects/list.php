<?php
require_once __DIR__ . "/../../../config/auth.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../models/Project.php";

requireLogin();

$projectModel = new Project($pdo);
$sessionRole = $_SESSION['user']['role'];
$projects = $projectModel->findAllProjects();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
        }

        /* SAME NAVBAR STYLE */
        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        /* CARDS STYLE */
        .project-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            transition: 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
        }

        /* HEADER BOX */
        .header-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
        }

        /* BUTTON */
        .soft-btn {
            border-radius: 12px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top"
        style="background: linear-gradient(135deg, #667eea, #764ba2);">

        <div class="container-fluid">

            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-project-diagram me-2"></i>
                ProjectHub - Admin
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">

                <div class="navbar-nav ms-auto align-items-lg-center">

                    <a href="../views/dashboard.php" class="nav-link text-white me-3 active">
                        <i class="fas fa-chart-line me-1"></i>
                        Dashboard
                    </a>

                    <a href="projectController.php?action=list" class="nav-link text-white me-3">

                        <i class="fas fa-folder-open me-1"></i>
                        Projects
                    </a>

                    <a href="userController.php?action=list" class="nav-link text-white me-3">

                        <i class="fas fa-users me-1"></i>
                        Users
                    </a>

                    <a href="taskController.php?action=list" class="nav-link text-white me-3">

                        <i class="fas fa-tasks me-1"></i>
                        Tasks
                    </a>

                    <div class="dropdown ms-lg-3">

                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">

                            <i class="fas fa-user-circle me-2"></i>

                            <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">

                            <li>
                                <a class="dropdown-item text-danger" href="../views/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Logout
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>

            </div>

        </div>

    </nav>

    <div class="container py-4">

        <!-- HEADER -->
        <div class="header-box p-4 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <!-- LEFT TEXT -->
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="fas fa-folder-open me-2"></i>
                    Projects Management
                </h3>

                <p class="mb-0 text-white-50">
                    Manage all projects in the system
                </p>
            </div>

            <!-- RIGHT BUTTON -->
            <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
                <div>
                    <a href="projectController.php?action=create" class="btn btn-light soft-btn">
                        <i class="fas fa-plus me-1"></i> New Project
                    </a>
                </div>
            <?php endif; ?>

        </div>



        <!-- PROJECTS -->
        <div class="row g-4">

            <?php if (empty($projects)): ?>

                <div class="text-center text-muted">
                    No projects found.
                </div>

            <?php else: ?>

                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4">

                        <div class="card project-card p-3 h-100">

                            <h5 class="fw-bold">
                                <i class="fas fa-folder me-2 text-primary"></i>
                                <?= htmlspecialchars($project['title']) ?>
                            </h5>

                            <p class="text-muted small">
                                <?= htmlspecialchars(substr($project['description'], 0, 100)) ?>
                            </p>

                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date('M d, Y', strtotime($project['created_at'])) ?>
                            </small>

                            <div class="mt-3 d-flex gap-2">

                                <a href="projectController.php?action=show&id=<?= $project['id'] ?>"
                                    class="btn btn-outline-primary btn-sm flex-grow-1">
                                    View
                                </a>

                                <?php if ($sessionRole === 'admin'): ?>
                                    <a href="projectController.php?action=update&id=<?= $project['id'] ?>"
                                        class="btn btn-outline-warning btn-sm flex-grow-1">
                                        Edit
                                    </a>

                                    <a href="projectController.php?action=delete&id=<?= $project['id'] ?>"
                                        class="btn btn-outline-danger btn-sm flex-grow-1" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </a>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>