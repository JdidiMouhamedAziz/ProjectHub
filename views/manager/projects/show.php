<?php
$sessionRole = $_SESSION['user']['role'] ?? '';

// make sure required models are available
$taskModel = new Task($pdo);
$projectModel = new Project($pdo);
$userModel = new User($pdo);
$projectMemberModel = new ProjectMember($pdo);

// determine project id (controller normally provides $project)
$projectId = $_GET['id'] ?? ($project['id'] ?? null);
if (!$projectId) die("Project ID is required");

// ensure $project is available
if (!isset($project) || !$project) {
    $project = $projectModel->findProjectById($projectId);
    if (!$project) die("Project not found");
}

// ensure $tasks and $members are available (controller normally sets these)
if (!isset($tasks)) {
    $tasks = $taskModel->findTasksByProjectId($projectId);
}
if (!isset($members)) {
    $members = $projectMemberModel->findMembersByProject($projectId);
}

// build list of assignable users from members for edit task modal
$users = [];
$seenUsers = [];
foreach ($members as $member) {
    $memberUser = $userModel->findUserById($member['user_id']);
    if ($memberUser && $memberUser['role'] === 'user' && !isset($seenUsers[$memberUser['id']])) {
        $users[] = $memberUser;
        $seenUsers[$memberUser['id']] = true;
    }
}

$returnUrl = $_SERVER['HTTP_REFERER'] ?? '../../../views/manager/projects/show.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css">
    <style>
        body {
            background: #f4f7fc;
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
            transform: translateY(-4px);
        }

        .welcome-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            color: white;
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            font-size: 24px;
            background: rgba(255, 255, 255, 0.18);
        }

        .soft-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .section-title {
            font-weight: 700;
        }

        .table thead th {
            border-bottom: 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6b7280;
        }

        .nav-pills .nav-link {
            border-radius: 999px;
            font-weight: 600;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .project-badge {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- navbar -->
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
                    <a href="../views/manager/dashboard.php" class="nav-link text-white me-3">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>
                    <a href="../controllers/projectController.php?action=list" class="nav-link text-white me-3">
                        <i class="fas fa-folder-open me-1"></i> Projects
                    </a>
                    <div class="dropdown ms-lg-3">
                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'User'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li>
                                <a class="dropdown-item text-danger" href="../views/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="welcome-box p-4 shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge project-badge rounded-pill mb-3 px-3 py-2">
                        <i class="fas fa-folder me-1"></i> Project Overview
                    </span>
                    <h2 class="fw-bold mb-1">
                        <?php echo htmlspecialchars($project['title']); ?>
                    </h2>
                    <p class="mb-0 text-white-50">
                        <?php echo htmlspecialchars($project['description']); ?>
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="fas fa-plus me-1"></i> Add Task
                    </button>
                    <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fas fa-user-plus me-1"></i> Add Member
                    </button>
                </div>
            </div>
        </div>
        <!-- stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon text-primary">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Tasks</div>
                            <div class="h3 mb-0 fw-bold"><?php echo $totalTasks ?? 0; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon text-warning">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div>
                            <div class="text-muted small">In Progress</div>
                            <div class="h3 mb-0 fw-bold"><?php echo $inProgressTasks ?? 0; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon text-info">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Submitted</div>
                            <div class="h3 mb-0 fw-bold"><?php echo $submittedTasks ?? 0; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-card shadow-sm h-100 border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Completed</div>
                            <div class="h3 mb-0 fw-bold"><?php echo $doneTasks ?? 0; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card soft-card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="section-title mb-0">Project Progress</h5>
                    <span class="badge bg-success-subtle text-success px-3 py-2"><?php echo $progress ?? 0; ?>%</span>
                </div>
                <div class="progress" style="height: 16px; border-radius: 999px;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: <?php echo $progress ?? 0; ?>%;" aria-valuenow="<?php echo $progress ?? 0; ?>"
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <!-- tabs -->
        <ul class="nav nav-pills gap-2 mb-3" id="projectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks"
                    type="button" role="tab">
                    <i class="fas fa-tasks me-1"></i> Tasks
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="submissions-tab" data-bs-toggle="tab" data-bs-target="#submissions"
                    type="button" role="tab">
                    <i class="fas fa-file-upload me-1"></i> Submissions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button"
                    role="tab">
                    <i class="fas fa-users me-1"></i> Members
                </button>
            </li>
        </ul>
        <div class="tab-content" id="projectTabsContent">
            <!-- Tasks tab -->
            <div class="tab-pane fade show active" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                <div class="card soft-card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-0 section-title"><i class="fas fa-tasks me-2 text-primary"></i>All Tasks</h5>
                            <small class="text-muted">Manage, assign, and track task status.</small>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            <i class="fas fa-plus me-1"></i> Add Task
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($tasks)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No tasks found for this project.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Complexity</th>
                                            <th>Assigned To</th>
                                            <th>Submission</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tasks as $task): ?>
                                            <?php
                                            $statusClass = 'secondary';
                                            $statusIcon = 'circle';
                                            switch ($task['status']) {
                                                case 'open':
                                                    $statusClass = 'danger';
                                                    $statusIcon = 'circle';
                                                    break;
                                                case 'in_progress':
                                                    $statusClass = 'warning';
                                                    $statusIcon = 'spinner';
                                                    break;
                                                case 'submitted':
                                                    $statusClass = 'info';
                                                    $statusIcon = 'file-upload';
                                                    break;
                                                case 'done':
                                                    $statusClass = 'success';
                                                    $statusIcon = 'check-circle';
                                                    break;
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($task['title']); ?>
                                                    </div>
                                                    <small
                                                        class="text-muted"><?php echo htmlspecialchars(substr($task['description'], 0, 60)); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                                        <i class="fas fa-<?php echo $statusIcon; ?> me-1"></i>
                                                        <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-light text-dark border"><?php echo (int) $task['complexity']; ?>/9</span>
                                                </td>
                                                <td><?php echo htmlspecialchars($task['assignee_name'] ?? 'Unassigned'); ?></td>
                                                <td>
                                                    <?php if (!empty($task['submission'])): ?>
                                                        <?php
                                                        $submissionClass = 'secondary';
                                                        switch ($task['submission']['status']) {
                                                            case 'pending':
                                                                $submissionClass = 'warning';
                                                                break;
                                                            case 'approved':
                                                                $submissionClass = 'success';
                                                                break;
                                                            case 'rejected':
                                                                $submissionClass = 'danger';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?php echo $submissionClass; ?>">
                                                            <?php echo ucfirst($task['submission']['status']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark border">None</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-outline-warning btn-sm"
    data-bs-toggle="modal"
    data-bs-target="#editTaskModal<?php echo $task['id']; ?>">
    <i class="fas fa-edit"></i>
</button>
                                                        <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
                                                            <a href="taskController.php?action=delete&id=<?php echo $task['id']; ?>"
                                                                class="btn btn-outline-danger" title="Delete"
                                                                onclick="return confirm('Are you sure?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- Edit Task Modal for this task -->
                                            <div class="modal fade" id="editTaskModal<?php echo $task['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content" style="border-radius:20px; border:none; overflow:hidden;">

                                                        <!-- Header -->
                                                        <div class="modal-header text-white"
                                                            style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                                                            <div>
                                                                <h5 class="modal-title fw-bold">
                                                                    <i class="fas fa-edit me-2"></i>Edit Task
                                                                </h5>
                                                                <small class="text-white-50">
                                                                    Update task information and assignment
                                                                </small>
                                                            </div>

                                                            <button type="button"
                                                                class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <!-- Form -->
                                                        <form action="/projectHub/controllers/taskController.php?action=update&id=<?php echo $task['id']; ?>"
                                                            method="POST">

                                                            <div class="modal-body p-4">

                                                                <input type="hidden"
                                                                    name="return_to"
                                                                    value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">

                                                                <!-- title -->
                                                                <div class="mb-4">
                                                                    <label class="form-label fw-semibold">
                                                                        <i class="fas fa-heading me-1 text-warning"></i>
                                                                        Task Title *
                                                                    </label>

                                                                    <input type="text"
                                                                        class="form-control form-control-lg"
                                                                        name="title"
                                                                        required
                                                                        value="<?php echo htmlspecialchars($task['title']); ?>"
                                                                        placeholder="Enter task title">
                                                                </div>

                                                                <!-- description -->
                                                                <div class="mb-4">
                                                                    <label class="form-label fw-semibold">
                                                                        <i class="fas fa-align-left me-1 text-warning"></i>
                                                                        Description
                                                                    </label>

                                                                    <textarea class="form-control"
                                                                        name="description"
                                                                        rows="4"
                                                                        placeholder="Enter task description"><?php echo htmlspecialchars($task['description']); ?></textarea>
                                                                </div>

                                                                <!-- row -->
                                                                <div class="row g-3 mb-3">

                                                                    <!-- complexity -->
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-semibold">
                                                                            <i class="fas fa-layer-group me-1 text-warning"></i>
                                                                            Complexity *
                                                                        </label>

                                                                        <input type="number"
                                                                            class="form-control"
                                                                            name="complexity"
                                                                            min="1"
                                                                            max="9"
                                                                            required
                                                                            value="<?php echo $task['complexity']; ?>">
                                                                    </div>

                                                                    <!-- status -->
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-semibold">
                                                                            <i class="fas fa-chart-line me-1 text-warning"></i>
                                                                            Status *
                                                                        </label>

                                                                        <select class="form-select" name="status" required>
                                                                            <option value="open" <?php echo $task['status'] === 'open' ? 'selected' : ''; ?>>
                                                                                Open
                                                                            </option>

                                                                            <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>
                                                                                In Progress
                                                                            </option>

                                                                            <option value="submitted" <?php echo $task['status'] === 'submitted' ? 'selected' : ''; ?>>
                                                                                Submitted
                                                                            </option>

                                                                            <option value="done" <?php echo $task['status'] === 'done' ? 'selected' : ''; ?>>
                                                                                Done
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <!-- row -->
                                                                <div class="row g-3">

                                                                    <!-- project -->
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-semibold">
                                                                            <i class="fas fa-folder-open me-1 text-warning"></i>
                                                                            Project *
                                                                        </label>

                                                                        <select class="form-select" name="project_id" required>
                                                                            <?php foreach ($projects as $proj): ?>
                                                                                <option value="<?php echo $proj['id']; ?>"
                                                                                    <?php echo $proj['id'] == $task['project_id'] ? 'selected' : ''; ?>>

                                                                                    <?php echo htmlspecialchars($proj['title']); ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>

                                                                    <!-- assign -->
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-semibold">
                                                                            <i class="fas fa-user-check me-1 text-warning"></i>
                                                                            Assign To
                                                                        </label>

                                                                        <select class="form-select" name="assigned_to">
                                                                            <option value="">Unassigned</option>

                                                                            <?php foreach ($users as $user): ?>
                                                                                <option value="<?php echo $user['id']; ?>"
                                                                                    <?php echo $user['id'] == $task['assigned_to'] ? 'selected' : ''; ?>>

                                                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <!-- info -->
                                                                <div class="alert alert-light border mt-4 mb-0">
                                                                    <i class="fas fa-clock text-warning me-2"></i>

                                                                    <strong>Created:</strong>
                                                                    <?php echo date('M d, Y H:i', strtotime($task['created_at'])); ?>
                                                                </div>

                                                            </div>

                                                            <!-- footer -->
                                                            <div class="modal-footer border-0 px-4 pb-4">
                                                                <button type="button"
                                                                    class="btn btn-light"
                                                                    data-bs-dismiss="modal">
                                                                    Cancel
                                                                </button>

                                                                <button type="submit" class="btn btn-warning text-white">
                                                                    <i class="fas fa-save me-1"></i>
                                                                    Update Task
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- submissions Tab -->
            <div class="tab-pane fade" id="submissions" role="tabpanel" aria-labelledby="submissions-tab">
                <div class="card soft-card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-0 section-title"><i class="fas fa-file-upload me-2 text-info"></i>All
                                Submissions</h5>
                            <small class="text-muted">Review task submissions for this project.</small>
                        </div>
                       
                    </div>
                    <div class="card-body">
                        <?php
                        $allSubmissions = [];
                        foreach ($tasks as $task) {
                            // only consider submissions for tasks that are currently marked as 'submitted'
                            if (!empty($task['submission']) && ($task['status'] ?? '') === 'submitted') {
                                $task['submission']['task_title'] = $task['title'];
                                $allSubmissions[] = $task['submission'];
                            }
                        }
                        ?>
                        <?php if (empty($allSubmissions)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-file-circle-xmark fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No submissions found for this project.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle table-hover">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Status</th>
                                            <th>Git Link</th>
                                            <th>Message</th>
                                            <th>Submitted</th>
                                            <th class="text-end">Review</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allSubmissions as $submission): ?>
                                            <?php
                                            $class = 'secondary';
                                            switch ($submission['status']) {
                                                case 'pending':
                                                    $class = 'warning';
                                                    break;
                                                case 'approved':
                                                    $class = 'success';
                                                    break;
                                                case 'rejected':
                                                    $class = 'danger';
                                                    break;
                                            }
                                            ?>
                                            <tr>
                                                <td class="fw-semibold">
                                                    <?php echo htmlspecialchars($submission['task_title']); ?>
                                                </td>
                                                <td><span
                                                        class="badge bg-<?php echo $class; ?>"><?php echo ucfirst($submission['status']); ?></span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo htmlspecialchars($submission['git_link']); ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fab fa-github me-1"></i> View Repo
                                                    </a>
                                                </td>
                                                <td><small><?php echo htmlspecialchars(substr($submission['message'], 0, 60)); ?></small>
                                                </td>
                                                <td><small><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></small>
                                                </td>
                                                <td class="text-end">
                                                    <?php if ($submission['status'] === 'pending'): ?>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="../controllers/taskSubmissionController.php?action=review&id=<?php echo $submission['id']; ?>&status=approved"
                                                                class="btn btn-success"
                                                                onclick="return confirm('Approve this submission?')">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                            <a href="../controllers/taskSubmissionController.php?action=review&id=<?php echo $submission['id']; ?>&status=rejected"
                                                                class="btn btn-danger"
                                                                onclick="return confirm('Reject this submission?')">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">Reviewed</span>
                                                    <?php endif; ?>
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
            <!-- Memeber projectTab -->
            <div class="tab-pane fade" id="members" role="tabpanel" aria-labelledby="members-tab">
                <div class="card soft-card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-0 section-title"><i class="fas fa-users me-2 text-success"></i>Team Members
                            </h5>
                            <small class="text-muted">Project members and their roles.</small>
                        </div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="fas fa-user-plus me-1"></i> Add Member
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($members)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users-slash fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No members found for this project.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle table-hover">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>User Role</th>
                                            <th>Project Role</th>
                                            <th>Joined</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($members as $member): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($member['username']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                                <td><span
                                                        class="badge bg-secondary"><?php echo ucfirst($member['role_user']); ?></span>
                                                </td>
                                                <td><span
                                                        class="badge bg-info text-dark"><?php echo ucfirst($member['role']); ?></span>
                                                </td>
                                                <td><small><?php echo date('M d, Y', strtotime($member['joined_at'])); ?></small>
                                                </td>
                                                <td class="text-end">
                                                    <a href="../../../controllers/projectController.php?action=removeMember&project_id=<?php echo $project['id']; ?>&user_id=<?php echo $member['user_id']; ?>"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Remove this member?')">
                                                        <i class="fas fa-times me-1"></i> Remove
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
    <!-- add Task modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px; border:none; overflow:hidden;">
                <div class="modal-header text-white" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Task</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="taskController.php?action=create" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label fw-semibold">Title *</label>
                            <input type="text" class="form-control" id="taskTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDesc" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="taskDesc" name="description" rows="3"></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="taskComplexity" class="form-label fw-semibold">Complexity (1-9) *</label>
                                <input type="number" class="form-control" id="taskComplexity" name="complexity" min="1"
                                    max="9" value="5" required>
                            </div>
                            <div class="col-md-6">
                                <label for="taskAssigned" class="form-label fw-semibold">Assign To</label>
                                <select class="form-select" id="taskAssigned" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo $member['user_id']; ?>">
                                            <?php echo htmlspecialchars($member['username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- add member modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px; border:none; overflow:hidden;">
                <div class="modal-header text-white" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Member to Project</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="projectController.php?action=addMember" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <div class="mb-3">
                            <label for="selectUser" class="form-label fw-semibold">Select User *</label>
                            <select class="form-select" id="selectUser" name="user_id" required>
                                <option value="">Choose a user...</option>
                                <?php
                                $allUsers = $userModel->findAllUsers();
                                $existingMembers = array_map(fn($m) => $m['user_id'], $members);
                                foreach ($allUsers as $u):
                                    if (!in_array($u['id'], $existingMembers)):
                                        ?>
                                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['username']); ?>
                                            (<?php echo htmlspecialchars($u['email']); ?>)</option>
                                        <?php
                                    endif;
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="memberRole" class="form-label fw-semibold">Role *</label>
                            <select class="form-select" id="memberRole" name="role" required>
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>