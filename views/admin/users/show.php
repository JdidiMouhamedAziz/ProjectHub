<?php


require_once(__DIR__ . "/../../../config/auth.php");
require_once(__DIR__ . "/../../../config/database.php");
require_once(__DIR__ . "/../../../models/User.php");
require_once(__DIR__ . "/../../../models/ProjectMember.php");
require_once(__DIR__ . "/../../../models/Task.php");
require_once(__DIR__ . "/../../../models/Project.php");

requireAdmin();

$userModel = new User($pdo);
$projectMemberModel = new ProjectMember($pdo);
$taskModel = new Task($pdo);
$projectModel = new Project($pdo);

$id = $_GET['id'] ?? null;
if (!$id)
    die("User ID is required");

$user = $userModel->findUserById($id);
if (!$user)
    die("User not found");

$projects = $projectMemberModel->findProjectsByMember($id);
$tasks = $taskModel->findTasksByUser($id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .soft-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .info-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
        }

        .stat-card {
            border: none;
            border-radius: 18px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .icon-box {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(102, 126, 234, 0.15);
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

    <div class="container-fluid py-4">




        <!-- USER INFO HEADER -->
        <div class="info-box p-4 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h2 class="fw-bold mb-1">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </h2>

                    <p class="mb-0 text-white-50">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="userController.php?action=update&id=<?php echo $user['id']; ?>" class="btn btn-light">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>

                    <a href="userController.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger"
                        onclick="return confirm('Delete this user?')">
                        <i class="fas fa-trash me-1"></i> Delete
                    </a>
                </div>

            </div>
        </div>

        <!-- STATS -->
        <div class="row g-4 mb-4">

            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-box text-primary">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Projects</div>
                            <div class="h4 mb-0 fw-bold"><?php echo count($projects); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-box text-warning">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Tasks</div>
                            <div class="h4 mb-0 fw-bold"><?php echo count($tasks); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon-box text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Completed</div>
                            <div class="h4 mb-0 fw-bold">
                                <?php echo count(array_filter($tasks, fn($t) => $t['status'] === 'done')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">

            <!-- TASKS -->
            <div class="col-md-8">
                <div class="card soft-card">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Tasks</h5>
                    </div>

                    <div class="card-body">

                        <?php if (empty($tasks)): ?>
                            <p class="text-muted">No tasks assigned</p>
                        <?php else: ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Complexity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($task['title']); ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo ucfirst($task['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?php echo $task['complexity']; ?>/9
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- PROJECTS -->
            <div class="col-md-4">
                <div class="card soft-card">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0"><i class="fas fa-folder me-2"></i> Projects</h5>
                    </div>

                    <div class="card-body">

                        <?php if (empty($projects)): ?>
                            <p class="text-muted">No projects</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($projects as $pm): ?>
                                    <?php $project = $projectModel->findProjectById($pm['project_id']); ?>
                                    <li class="list-group-item">
                                        <strong>
                                            <?php echo $project ? htmlspecialchars($project['title']) : 'Unknown'; ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            Role: <?php echo ucfirst($pm['role']); ?>
                                        </small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>