<?php
session_start();

require_once(__DIR__ . "/../../config/database.php");
require_once(__DIR__ . "/../../config/auth.php");
require_once(__DIR__ . "/../../models/Task.php");
require_once(__DIR__ . "/../../models/Notification.php");

requireLogin();

$taskModel = new Task($pdo);
$notificationModel = new Notification($pdo);

$tasks = $taskModel->findTasksByUser($_SESSION['user']['id']);
$notifications = $notificationModel->findNotificationsByUser($_SESSION['user']['id']);

$inProgress = count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress'));
$completed = count(array_filter($tasks, fn($t) => $t['status'] === 'done'));
$unread = count(array_filter($notifications, fn($n) => !$n['is_read']));


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
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
                <i class="fas fa-project-diagram me-2"></i>ProjectHub
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">

                <div class="navbar-nav ms-auto align-items-lg-center">

                    <a href="dashboard.php" class="nav-link-custom">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>

                    <a href="kanban/kanban.php" class="nav-link-custom">
                        <i class="fas fa-table-columns me-1"></i> Kanban
                    </a>

                    <a href="notifications/list.php" class="nav-link-custom position-relative">
                        <i class="fas fa-bell me-1"></i> Notifications

                        <?php if ($unread > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $unread; ?>
                            </span>
                        <?php endif; ?>
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

    <!-- MAIN CONTENT -->
    <div class="container-fluid py-4">

        <!-- Welcome -->
        <div class="welcome-box p-4 shadow-sm mb-4">
            <div class="row align-items-center">

                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">
                        Welcome back,
                        <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?> 👋
                    </h2>

                    <p class="mb-0">
                        Manage your tasks, track progress and stay productive.
                    </p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="kanban/kanban.php" class="btn btn-light btn-lg">
                        <i class="fas fa-table-columns me-2"></i>
                        Open Kanban
                    </a>
                </div>

            </div>
        </div>

        <!-- STATS -->
        <div class="row g-4 mb-4">

            <!-- Total Tasks -->
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">

                    <div class="card-body d-flex align-items-center">

                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="fas fa-tasks"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">My Tasks</p>
                            <h3 class="fw-bold mb-0"><?php echo count($tasks); ?></h3>
                        </div>

                    </div>

                </div>
            </div>

            <!-- In Progress -->
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">

                    <div class="card-body d-flex align-items-center">

                        <div class="stat-icon bg-warning-subtle text-warning me-3">
                            <i class="fas fa-spinner"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">In Progress</p>
                            <h3 class="fw-bold mb-0"><?php echo $inProgress; ?></h3>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Completed -->
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">

                    <div class="card-body d-flex align-items-center">

                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">Completed</p>
                            <h3 class="fw-bold mb-0"><?php echo $completed; ?></h3>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Notifications -->
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100">

                    <div class="card-body d-flex align-items-center">

                        <div class="stat-icon bg-info-subtle text-info me-3">
                            <i class="fas fa-bell"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">Notifications</p>
                            <h3 class="fw-bold mb-0"><?php echo $unread; ?></h3>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <!-- TASKS TABLE -->
        <div class="card table-card shadow-sm">

            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="fas fa-list-check text-primary me-2"></i>
                            My Assigned Tasks
                        </h4>

                        <p class="text-muted mb-0">
                            Latest assigned tasks overview
                        </p>
                    </div>

                    <a href="kanban/kanban.php" class="btn btn-primary">
                        <i class="fas fa-table-columns me-2"></i>
                        View Kanban
                    </a>

                </div>
            </div>

            <div class="card-body">

                <?php if (empty($tasks)): ?>

                    <div class="text-center py-5">

                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>

                        <h5>No tasks assigned yet</h5>

                        <p class="text-muted">
                            Your assigned tasks will appear here.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table align-middle table-hover">

                            <thead class="table-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Status</th>
                                    <th>Complexity</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach (array_slice($tasks, 0, 5) as $task): ?>

                                    <?php
                                    $statusClass = match ($task['status']) {
                                        'open' => 'danger',
                                        'in_progress' => 'warning',
                                        'submitted' => 'info',
                                        'done' => 'success',
                                        default => 'secondary'
                                    };
                                    ?>

                                    <tr>

                                        <td>
                                            <h6 class="fw-bold mb-1">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </h6>

                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($task['description'], 0, 70)); ?>
                                            </small>
                                        </td>

                                        <td>
                                            <span class="badge bg-<?php echo $statusClass; ?> rounded-pill px-3 py-2">
                                                <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-dark">
                                                <?php echo $task['complexity']; ?>/9
                                            </span>
                                        </td>

                                        <td>
                                            <?php echo date('M d, Y', strtotime($task['created_at'])); ?>
                                        </td>

                                        <td>
                                            <a href="kanban/kanban.php" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>
                                                Open
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