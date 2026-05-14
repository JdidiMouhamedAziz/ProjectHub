<?php
session_start();

require_once(__DIR__ . "/../../config/database.php");
require_once(__DIR__ . "/../../config/auth.php");
require_once(__DIR__ . "/../../models/Project.php");
require_once(__DIR__ . "/../../models/ProjectMember.php");
require_once(__DIR__ . "/../../models/Task.php");
require_once(__DIR__ . "/../../models/TaskSubmission.php");

requireManager();

$projectModel = new Project($pdo);
$projectMemberModel = new ProjectMember($pdo);
$taskModel = new Task($pdo);
$submissionModel = new TaskSubmission($pdo);

$memberships = $projectMemberModel->findProjectsByMember($_SESSION['user']['id']);

$projectsCount = count($memberships);

$allTasks = 0;
$inProgress = 0;

foreach ($memberships as $m) {
    $tasks = $taskModel->findTasksByProjectId($m['project_id']);
    $allTasks += count($tasks);
    $inProgress += count($taskModel->findTasksByStatusAndProject('in_progress', $m['project_id']));
}

$pendingReview = $submissionModel->findTaskSubmissionsByStatus('pending');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f4f7fc;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .dashboard-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 28px;
        }

        .table-card {
            border: none;
            border-radius: 20px;
        }

        .welcome-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            color: white;
        }

        .nav-link-custom {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 500;
        }

        .nav-link-custom:hover {
            color: #ddd;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
        <div class="container-fluid">

            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-project-diagram me-2"></i>ProjectHub - Manager
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">

                <div class="navbar-nav ms-auto align-items-lg-center">

                    <a href="dashboard.php" class="nav-link-custom">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>

                    <a href="../../controllers/projectController.php?action=list" class="nav-link-custom">
                        <i class="fas fa-project-diagram me-1"></i> Projects
                    </a>

                    

                    <div class="dropdown ms-lg-3">

                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li>
                                <a class="dropdown-item text-danger" href="../logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>

                    </div>

                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN -->
    <div class="container-fluid py-4">

        <!-- WELCOME BOX -->
        <div class="welcome-box p-4 shadow-sm mb-4">
            <div class="row align-items-center">

                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">
                        Manager Panel 👨‍💼
                    </h2>

                    <p class="mb-0">
                        Manage projects, track tasks and supervise your team.
                    </p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="../../controllers/projectController.php?action=create" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        New Project
                    </a>
                </div>

            </div>
        </div>

        <!-- STATS -->
        <div class="row g-4 mb-4">

            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Projects</p>
                            <h3 class="fw-bold mb-0"><?php echo $projectsCount; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-subtle text-warning me-3">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Tasks</p>
                            <h3 class="fw-bold mb-0"><?php echo $allTasks; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-subtle text-info me-3">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">In Progress</p>
                            <h3 class="fw-bold mb-0"><?php echo $inProgress; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Pending Review</p>
                            <h3 class="fw-bold mb-0"><?php echo count($pendingReview); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- PROJECTS TABLE -->
        <div class="card table-card shadow-sm">

            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="fas fa-folder text-primary me-2"></i>
                            My Projects
                        </h4>
                        <p class="text-muted mb-0">Overview of all managed projects</p>
                    </div>

                    <a href="../../controllers/projectController.php?action=list" class="btn btn-primary">
                        View All
                    </a>

                </div>
            </div>

            <div class="card-body">

                <?php
                $projects = [];
                foreach ($memberships as $m) {
                    $p = $projectModel->findProjectById($m['project_id']);
                    if ($p)
                        $projects[] = $p;
                }
                ?>

                <?php if (empty($projects)): ?>

                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5>No projects yet</h5>
                        <p class="text-muted">Start by creating a new project.</p>
                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>Project</th>
                                    <th>Tasks</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($projects as $p): ?>

                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($p['title']); ?></td>

                                        <td>
                                            <?php echo count($taskModel->findTasksByProjectId($p['id'])); ?>
                                        </td>

                                        <td>
                                            <?php echo date('M d, Y', strtotime($p['created_at'])); ?>
                                        </td>

                                        <td>
                                            <a href="../../controllers/projectController.php?action=show&id=<?php echo $p['id']; ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>