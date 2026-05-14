<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
        }

        .soft-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .header-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
        }

        .btn-soft {
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 600;
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
        <div class="header-box p-4 mb-4 shadow-sm">
            <h3 class="fw-bold mb-1">
                <i class="fas fa-plus-circle me-2"></i>
                Create New Project
            </h3>
            <p class="mb-0 text-white-50">
                Add a new project to the system
            </p>
        </div>

        <!-- FORM CARD -->
        <div class="card soft-card">
            <div class="card-body p-4">

                <form action="projectController.php?action=create" method="POST">

                    <!-- TITLE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Project Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter project title" required>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="5" class="form-control"
                            placeholder="Enter project description"></textarea>
                    </div>

                    <!-- BUTTONS -->
                    <div class="d-flex gap-2 mt-4">

                        <button type="submit" class="btn btn-primary btn-soft flex-grow-1">
                            <i class="fas fa-save me-1"></i> Create Project
                        </button>

                        <a href="projectController.php?action=list" class="btn btn-secondary btn-soft flex-grow-1">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>

                    </div>

                </form>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>