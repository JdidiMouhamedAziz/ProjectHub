<?php
session_start();
require_once(__DIR__ . "/../../config/database.php");
require_once(__DIR__ . "/../../config/auth.php");
require_once(__DIR__ . "/../../models/Project.php");
require_once(__DIR__ . "/../../models/ProjectMember.php");
require_once(__DIR__ . "/../../models/User.php");
require_once(__DIR__ . "/../../models/Task.php");
require_once(__DIR__ . "/../../models/TaskSubmission.php");

requireAdmin();

$projectModel = new Project($pdo);
$projectMemberModel = new ProjectMember($pdo);
$userModel = new User($pdo);
$taskModel = new Task($pdo);
$submissionModel = new TaskSubmission($pdo);

if (isset($_GET['export']) && $_GET['export'] === 'tasks_csv') {
    $tasks = $taskModel->findAllTasks();
    $projects = $projectModel->findAllProjects();
    $users = $userModel->findAllUsers();

    $projectMap = [];
    foreach ($projects as $project) {
        $projectMap[$project['id']] = $project['title'];
    }

    $userMap = [];
    foreach ($users as $user) {
        $userMap[$user['id']] = $user['username'];
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="admin-tasks-export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Title', 'Status', 'Complexity', 'Project', 'Assigned To', 'Created At']);

    foreach ($tasks as $task) {
        fputcsv($output, [
            $task['id'],
            $task['title'],
            $task['status'],
            $task['complexity'],
            $projectMap[$task['project_id']] ?? 'Unknown',
            !empty($task['assigned_to']) ? ($userMap[$task['assigned_to']] ?? 'Unknown') : 'Unassigned',
            $task['created_at'] ?? '',
        ]);
    }

    fclose($output);
    exit();
}

$totalProjects = count($projectModel->findAllProjects());
$totalUsers = count($userModel->findAllUsers());
$totalTasks = count($taskModel->findAllTasks());
$pendingReviews = count($submissionModel->findTaskSubmissionsByStatus('pending'));

$projects = $projectModel->findAllProjects();
$recentProjects = array_slice(array_reverse($projects), 0, 5);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .welcome-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            color: white;
        }

        .dashboard-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .soft-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .stat-icon {
            width: 65px;
            height: 65px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(102, 126, 234, 0.1);
        }

        .section-title {
            font-weight: 700;
        }

        .table thead th {
            border-bottom: 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #6b7280;
        }

        .quick-btn {
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 600;
        }



        .project-badge {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: white;
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

                    <a href="dashboard.php" class="nav-link text-white me-3 active">
                        <i class="fas fa-chart-line me-1"></i>
                        Dashboard
                    </a>

                    <a href="../../controllers/projectController.php?action=list" class="nav-link text-white me-3">

                        <i class="fas fa-folder-open me-1"></i>
                        Projects
                    </a>

                    <a href="../../controllers/userController.php?action=list" class="nav-link text-white me-3">

                        <i class="fas fa-users me-1"></i>
                        Users
                    </a>

                    <a href="../../controllers/taskController.php?action=list" class="nav-link text-white me-3">

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
                                <a class="dropdown-item text-danger" href="../../views/logout.php">
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
        <div class="row g-4">



            <!-- Main Content -->
            <div class="">

                <!-- Welcome -->
                <div class="welcome-box p-4 shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="badge project-badge rounded-pill px-3 py-2 mb-3">
                                <i class="fas fa-shield-alt me-1"></i> Admin Panel
                            </span>

                            <h2 class="fw-bold mb-1">
                                Welcome, <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?>
                            </h2>

                            <p class="mb-0 text-white-50">
                                Manage projects, users, tasks and monitor platform activity.
                            </p>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="../../controllers/projectController.php?action=create"
                                class="btn btn-outline-light">
                                <i class="fas fa-plus me-1"></i> New Project
                            </a>

                            <a href="../../controllers/userController.php?action=create" class="btn btn-light">
                                <i class="fas fa-user-plus me-1"></i> Add User
                            </a>

                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#exportModal">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row g-4 mb-4">

                    <div class="col-md-6 col-xl-3">
                        <div class="card dashboard-card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-project-diagram"></i>
                                </div>

                                <div>
                                    <div class="text-muted small">Projects</div>
                                    <div class="h2 fw-bold mb-0">
                                        <?php echo $totalProjects; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card dashboard-card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon text-success">
                                    <i class="fas fa-users"></i>
                                </div>

                                <div>
                                    <div class="text-muted small">Users</div>
                                    <div class="h2 fw-bold mb-0">
                                        <?php echo $totalUsers; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card dashboard-card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon text-warning">
                                    <i class="fas fa-tasks"></i>
                                </div>

                                <div>
                                    <div class="text-muted small">Tasks</div>
                                    <div class="h2 fw-bold mb-0">
                                        <?php echo $totalTasks; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card dashboard-card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon text-info">
                                    <i class="fas fa-file-upload"></i>
                                </div>

                                <div>
                                    <div class="text-muted small">Pending Reviews</div>
                                    <div class="h2 fw-bold mb-0">
                                        <?php echo $pendingReviews; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Quick Actions -->
                <div class="card soft-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                            <div>
                                <h5 class="section-title mb-1">
                                    <i class="fas fa-bolt text-warning me-2"></i>
                                    Quick Actions
                                </h5>

                                <small class="text-muted">
                                    Quickly access important admin features.
                                </small>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <a href="../../controllers/projectController.php?action=create"
                                class="btn btn-primary quick-btn">
                                <i class="fas fa-plus me-1"></i> New Project
                            </a>

                            <a href="../../controllers/userController.php?action=create"
                                class="btn btn-success quick-btn">
                                <i class="fas fa-user-plus me-1"></i> Add User
                            </a>

                            <a href="../../controllers/taskController.php?action=list"
                                class="btn btn-warning text-white quick-btn">
                                <i class="fas fa-tasks me-1"></i> Manage Tasks
                            </a>

                            <a href="../../controllers/projectController.php?action=list"
                                class="btn btn-info text-white quick-btn">
                                <i class="fas fa-folder-open me-1"></i> Projects
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Projects -->
                <div class="card soft-card">
                    <div class="card-header bg-white border-0 py-3">
                        <div>
                            <h5 class="section-title mb-1">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Recent Projects
                            </h5>

                            <small class="text-muted">
                                Latest created projects on the platform.
                            </small>
                        </div>
                    </div>

                    <div class="card-body">

                        <?php if (empty($recentProjects)): ?>

                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No projects available.</p>
                            </div>

                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table align-middle table-hover">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>Created At</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php foreach ($recentProjects as $proj): ?>

                                            <tr>

                                                <td class="fw-semibold">
                                                    <?php echo htmlspecialchars($proj['title']); ?>
                                                </td>

                                                <td>
                                                    <small>
                                                        <?php echo date('M d, Y', strtotime($proj['created_at'])); ?>
                                                    </small>
                                                </td>

                                                <td class="text-end">
                                                    <a href="../../controllers/projectController.php?action=show&id=<?php echo $proj['id']; ?>"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> View
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
        </div>
    </div>

    <!-- EXPORT MODAL -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px; border:none; overflow:hidden;">
                <div class="modal-header text-white" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                    <h5 class="modal-title">
                        <i class="fas fa-file-csv me-2"></i>
                        Export CSV
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../../controllers/exportController.php?action=download" method="post">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="export_type" class="form-label fw-semibold">Select data to export</label>
                            <select id="export_type" name="export_type" class="form-select" required>
                                <option value="" selected disabled>Choose dataset</option>
                                <option value="projects">Projects</option>
                                <option value="users">Users</option>
                                <option value="tasks">Tasks</option>
                                <option value="submissions">Task Submissions</option>
                                <option value="members">Project Members</option>
                            </select>
                        </div>

                        <input type="hidden" name="format" value="csv">

                        <div class="alert alert-light border small mb-0">
                            The file will download immediately after you choose a dataset and click Export.
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>