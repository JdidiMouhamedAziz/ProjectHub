<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
        }

        /* NAVBAR SAME STYLE */
        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        /* CARD STYLE */
        .soft-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        /* HEADER BOX */
        .header-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
        }

        /* BUTTON STYLE */
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

    <div class="container py-5">

        <!-- HEADER -->
        <div class="header-box p-4 mb-4">
            <h3 class="fw-bold mb-1">
                <i class="fas fa-edit me-2"></i>
                Edit Project
            </h3>
            <p class="mb-0 text-white-50">
                Update project information
            </p>
        </div>

        <!-- FORM CARD -->
        <div class="card soft-card p-4">

            <?php if (!$project): ?>

                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Project not found
                </div>

            <?php else: ?>

                <form action="projectController.php?action=update&id=<?= $project['id'] ?>" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Project Title</label>
                        <input type="text" name="title" class="form-control"
                            value="<?= htmlspecialchars($project['title']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                            rows="5"><?= htmlspecialchars($project['description']) ?></textarea>
                    </div>

                    <div class="text-muted mb-3">
                        <i class="fas fa-calendar me-1"></i>
                        Created: <?= date('M d, Y H:i', strtotime($project['created_at'])) ?>
                    </div>

                    <div class="d-flex gap-2">

                        <button type="submit" class="btn btn-warning flex-grow-1 soft-btn">
                            <i class="fas fa-save me-1"></i> Update Project
                        </button>

                        <a href="projectController.php?action=show&id=<?= $project['id'] ?>"
                            class="btn btn-secondary flex-grow-1 soft-btn">
                            Cancel
                        </a>

                    </div>

                </form>

            <?php endif; ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>