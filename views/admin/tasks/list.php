<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks Management</title>

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

        .page-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            color: white;
        }

        .soft-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .table thead th {
            border-bottom: 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #6b7280;
        }

        .badge-status {
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
        }

        .task-title {
            font-weight: 600;
            color: #111827;
        }

        .task-desc {
            color: #6b7280;
            font-size: 0.85rem;
        }

        .btn-action {
            border-radius: 10px;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.03);
        }

        .section-title {
            font-weight: 700;
        }

        .project-badge {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.18);
            color: white;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-project-diagram me-2"></i>
            ProjectHub - Tasks
        </a>

        <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">

            <div class="navbar-nav ms-auto align-items-lg-center">

                <a href="../views/admin/dashboard.php" class="nav-link text-white me-3">
                    <i class="fas fa-chart-line me-1"></i>
                    Dashboard
                </a>

                <a href="projectController.php?action=list"
                    class="nav-link text-white me-3">

                    <i class="fas fa-folder-open me-1"></i>
                    Projects
                </a>

                <a href="userController.php?action=list"
                    class="nav-link text-white me-3">

                    <i class="fas fa-users me-1"></i>
                    Users
                </a>

                <a href="taskController.php?action=list"
                    class="nav-link text-white active me-3">

                    <i class="fas fa-tasks me-1"></i>
                    Tasks
                </a>

                <div class="dropdown ms-lg-3">

                    <button class="btn btn-light dropdown-toggle"
                        data-bs-toggle="dropdown">

                        <i class="fas fa-user-circle me-2"></i>

                        <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">

                        <li>
                            <a class="dropdown-item text-danger"
                                href="../views/logout.php">

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

    <!-- Header -->
    <div class="page-header p-4 shadow-sm mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <span class="badge project-badge rounded-pill px-3 py-2 mb-3">
                    <i class="fas fa-tasks me-1"></i>
                    Tasks Management
                </span>

                <h2 class="fw-bold mb-1">
                    Manage Tasks
                </h2>

                <p class="mb-0 text-white-50">
                    View and manage all tasks in the platform.
                </p>

            </div>

           

        </div>

    </div>

    <?php if (empty($tasks)): ?>

        <div class="card soft-card">

            <div class="card-body text-center py-5">

                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>

                <h5>No Tasks Found</h5>

                <p class="text-muted mb-0">
                    There are currently no tasks available.
                </p>

            </div>

        </div>

    <?php else: ?>

        <div class="card soft-card">

            <div class="card-header bg-white border-0 py-3">

                <div>
                    <h5 class="section-title mb-1">
                        <i class="fas fa-list-check text-primary me-2"></i>
                        Tasks List
                    </h5>

                    <small class="text-muted">
                        All tasks created in the system.
                    </small>
                </div>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table align-middle table-hover">

                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Status</th>
                                <th>Complexity</th>
                                <th>Assigned To</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($tasks as $task): ?>

                                <tr>

                                    <td width="30%">

                                        <div class="task-title">
                                            <?php echo htmlspecialchars($task['title']); ?>
                                        </div>

                                        <div class="task-desc">
                                            <?php echo htmlspecialchars(substr($task['description'], 0, 70)); ?>...
                                        </div>

                                    </td>

                                    <td>

                                        <?php 
                                            $project = null;

                                            foreach ($projects as $p) {
                                                if ($p['id'] == $task['project_id']) {
                                                    $project = $p;
                                                    break;
                                                }
                                            }

                                            echo $project 
                                                ? htmlspecialchars($project['title']) 
                                                : 'Unknown';
                                        ?>

                                    </td>

                                    <td>

                                        <?php 
                                            $statusClass = 'secondary';

                                            switch($task['status']) {

                                                case 'open':
                                                    $statusClass = 'danger';
                                                    break;

                                                case 'in_progress':
                                                    $statusClass = 'warning';
                                                    break;

                                                case 'submitted':
                                                    $statusClass = 'info';
                                                    break;

                                                case 'done':
                                                    $statusClass = 'success';
                                                    break;
                                            }
                                        ?>

                                        <span class="badge bg-<?php echo $statusClass; ?> badge-status">
                                            <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge bg-dark rounded-pill px-3 py-2">
                                            <?php echo $task['complexity']; ?>/9
                                        </span>

                                    </td>

                                    <td>

                                        <?php 
                                            if ($task['assigned_to']) {

                                                $assignee = null;

                                                foreach ($users as $u) {

                                                    if ($u['id'] == $task['assigned_to']) {
                                                        $assignee = $u;
                                                        break;
                                                    }
                                                }

                                                echo $assignee
                                                    ? htmlspecialchars($assignee['username'])
                                                    : 'Unknown';

                                            } else {

                                                echo '<span class="text-muted">Unassigned</span>';
                                            }
                                        ?>

                                    </td>

                                    <td class="text-end">

                                        <div class="btn-group">

                                            <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>

                                                <a href="taskController.php?action=update&id=<?php echo $task['id']; ?>"
                                                    class="btn btn-warning btn-sm btn-action">

                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <a href="taskController.php?action=delete&id=<?php echo $task['id']; ?>"
                                                    class="btn btn-danger btn-sm btn-action"
                                                    onclick="return confirm('Are you sure?')">

                                                    <i class="fas fa-trash"></i>
                                                </a>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>