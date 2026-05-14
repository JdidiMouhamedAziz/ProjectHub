<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .welcome-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            color: white;
        }

        .project-badge {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: white;
        }

        .soft-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .section-title {
            font-weight: 700;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #dbe2ea;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .btn-modern {
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 600;
        }

        .role-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 16px;
            border: 1px solid #e5e7eb;
        }

        .role-box strong {
            color: #111827;
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

                    <a href="../views/admin/dashboard.php" class="nav-link text-white me-3 active">
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

        <!-- PAGE HEADER -->
        <div class="welcome-box p-4 shadow-sm mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>

                    <span class="badge project-badge rounded-pill px-3 py-2 mb-3">
                        <i class="fas fa-user-plus me-1"></i>
                        User Management
                    </span>

                    <h2 class="fw-bold mb-1">
                        Create New User
                    </h2>

                    <p class="mb-0 text-white-50">
                        Add a new user and assign permissions to the platform.
                    </p>

                </div>

                

            </div>

        </div>

        <!-- FORM -->
        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="card soft-card">

                    <div class="card-body p-4">

                        <div class="mb-4">

                            <h4 class="section-title mb-1">
                                <i class="fas fa-user-plus text-primary me-2"></i>
                                User Information
                            </h4>

                            <p class="text-muted mb-0">
                                Fill in the user details to create a new account.
                            </p>

                        </div>

                        <form action="/projectHub/controllers/userController.php?action=create" method="POST">

                            <!-- USERNAME -->
                            <div class="mb-4">

                                <label for="username" class="form-label fw-semibold">
                                    Username *
                                </label>

                                <input type="text" class="form-control" id="username" name="username" required
                                    placeholder="Enter username">

                                <small class="text-muted">
                                    Username must be unique.
                                </small>

                            </div>

                            <!-- EMAIL -->
                            <div class="mb-4">

                                <label for="email" class="form-label fw-semibold">
                                    Email *
                                </label>

                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter email address">

                                <small class="text-muted">
                                    Email must be unique and valid.
                                </small>

                            </div>

                            <!-- PASSWORD -->
                            <div class="mb-4">

                                <label for="password" class="form-label fw-semibold">
                                    Password *
                                </label>

                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Enter password">

                                <small class="text-muted">
                                    Use a strong password for better security.
                                </small>

                            </div>

                            <!-- ROLE -->
                            <div class="mb-4">

                                <label for="role" class="form-label fw-semibold">
                                    Role *
                                </label>

                                <select class="form-select" id="role" name="role" required>

                                    <option value="user">User</option>
                                    <option value="manager">Manager</option>
                                    <option value="admin">Admin</option>

                                </select>

                            </div>

                            <!-- ROLE INFO -->
                            <div class="role-box mb-4">

                                <div class="mb-2">
                                    <strong>User:</strong>
                                    Can manage personal tasks and kanban board.
                                </div>

                                <div class="mb-2">
                                    <strong>Manager:</strong>
                                    Can manage projects, teams and tasks.
                                </div>

                                <div>
                                    <strong>Admin:</strong>
                                    Full access to the entire platform.
                                </div>

                            </div>

                            <!-- BUTTONS -->
                            <div class="d-flex gap-3 flex-wrap">

                                <button type="submit" class="btn btn-primary btn-modern flex-grow-1">

                                    <i class="fas fa-plus me-1"></i>
                                    Create User
                                </button>

                                <a href="userController.php?action=list"
                                    class="btn btn-light btn-modern flex-grow-1">

                                    <i class="fas fa-times me-1"></i>
                                    Cancel
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>