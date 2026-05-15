<?php
session_start();
require_once(__DIR__ . "/../../../config/database.php");
require_once(__DIR__ . "/../../../config/auth.php");
require_once(__DIR__ . "/../../../models/Task.php");
require_once(__DIR__ . "/../../../models/Notification.php");
require_once(__DIR__ . "/../../../models/Project.php");
require_once(__DIR__ . "/../../../models/ProjectMember.php");

requireLogin();

$taskModel = new Task($pdo);
$projectModel = new Project($pdo);
$pmModel = new ProjectMember($pdo);

// load user's projects
$memberships = $pmModel->findProjectsByMember($_SESSION['user']['id']);
$userProjects = [];
foreach ($memberships as $m) {
    $p = $projectModel->findProjectById($m['project_id']);
    if ($p)
        $userProjects[] = $p;
}

// selected project from query or default to first
$selectedProjectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : ($userProjects[0]['id'] ?? null);

$tasks = $taskModel->findTasksByUser($_SESSION['user']['id']);
if ($selectedProjectId) {
    $tasks = array_values(array_filter($tasks, fn($t) => intval($t['project_id']) === intval($selectedProjectId)));
}

$notificationModel = new Notification($pdo);
$unread = $notificationModel->countUnread($_SESSION['user']['id']);

$tasksByStatus = [
    'open' => array_filter($tasks, fn($t) => $t['status'] === 'open'),
    'in_progress' => array_filter($tasks, fn($t) => $t['status'] === 'in_progress'),
    'submitted' => array_filter($tasks, fn($t) => $t['status'] === 'submitted'),
    'done' => array_filter($tasks, fn($t) => $t['status'] === 'done'),
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
        }

        /* NAVBAR (same as dashboard) */
        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        /* COLUMN CARD */
        .kanban-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
        }

        .kanban-card:hover {
            transform: translateY(-5px);
        }

        /* TASK CARD */
        .task-card {
            background: #fff;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            border-left: 5px solid transparent;
            transition: 0.2s;
            cursor: pointer;
        }

        .task-card:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }

        /* STATUS COLORS */
        .open {
            border-left-color: #dc3545;
        }

        .in_progress {
            border-left-color: #ffc107;
        }

        .submitted {
            border-left-color: #0dcaf0;
        }

        .done {
            border-left-color: #198754;
        }

        /* HEADER BADGE STYLE */
        .status-title {
            font-weight: 700;
            font-size: 16px;
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

                    <a href="../dashboard.php" class="nav-link-custom">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>

                    <a href="kanban.php" class="nav-link-custom">
                        <i class="fas fa-table-columns me-1"></i> Kanban
                    </a>

                    <a href="../notifications/list.php" class="nav-link-custom position-relative">
                        <i class="fas fa-bell me-1"></i> Notifications

                        <?php if (!empty($unread) && $unread > 0): ?>
                            <span id="notifBadge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $unread; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php if (!empty($unread) && $unread > 0): ?>
                        <button class="btn btn-sm btn-light ms-2" onclick="markAllRead()"
                            title="Mark all notifications read">
                            <i class="fas fa-check-double"></i>
                        </button>
                    <?php endif; ?>

                    <div class="dropdown ms-lg-3">

                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">

                            <li>
                                <a class="dropdown-item text-danger" href="../../logout.php">
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

        <!-- TITLE -->
        <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
            <div>
                <h3 class="fw-bold">Kanban Board</h3>
                <p class="text-muted">Manage tasks by status</p>
            </div>

            <div>
                <label class="form-label mb-0 small text-muted">Project</label>
                <select id="projectSelect" class="form-select" style="min-width:220px;">
                    <option value="" disabled>All projects</option>
                    <?php foreach ($userProjects as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($selectedProjectId && $selectedProjectId == $p['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- KANBAN GRID -->
        <div class="row g-4">

            <!-- OPEN -->
            <div class="col-md-3">
                <div class="card kanban-card shadow-sm p-3">
                    <div class="status-title text-danger mb-3">
                        <i class="fas fa-circle me-1"></i>
                        Open (<?php echo count($tasksByStatus['open']); ?>)
                    </div>

                    <?php foreach ($tasksByStatus['open'] as $task): ?>
                        <div class="task-card open"
                            onclick="window.location.href='../../../controllers/taskController.php?action=show&id=<?php echo $task['id']; ?>'">

                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                            <p class="text-muted mb-1 small">
                                <?php echo htmlspecialchars(substr($task['description'], 0, 60)); ?>
                            </p>

                            <span class="badge bg-dark">
                                Complexity <?php echo $task['complexity']; ?>/9
                            </span>

                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="startTask(event, <?php echo $task['id']; ?>, this)">
                                    Start
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- IN PROGRESS -->
            <div class="col-md-3">
                <div class="card kanban-card shadow-sm p-3">
                    <div class="status-title text-warning mb-3">
                        <i class="fas fa-spinner me-1"></i>
                        In Progress (<?php echo count($tasksByStatus['in_progress']); ?>)
                    </div>

                    <?php foreach ($tasksByStatus['in_progress'] as $task): ?>
                        <div class="task-card in_progress">
                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                            <p class="text-muted mb-1 small">
                                <?php echo htmlspecialchars(substr($task['description'], 0, 60)); ?>
                            </p>

                            <button class="btn btn-sm btn-outline-warning"
                                onclick="openSubmitModal(<?php echo $task['id']; ?>,'<?php echo htmlspecialchars($task['title'], ENT_QUOTES); ?>')">
                                Submit
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SUBMITTED -->
            <div class="col-md-3">
                <div class="card kanban-card shadow-sm p-3">
                    <div class="status-title text-info mb-3">
                        <i class="fas fa-paper-plane me-1"></i>
                        Submitted (<?php echo count($tasksByStatus['submitted']); ?>)
                    </div>

                    <?php foreach ($tasksByStatus['submitted'] as $task): ?>
                        <div class="task-card submitted">
                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                            <p class="text-muted small">
                                <?php echo htmlspecialchars(substr($task['description'], 0, 60)); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- DONE -->
            <div class="col-md-3">
                <div class="card kanban-card shadow-sm p-3">
                    <div class="status-title text-success mb-3">
                        <i class="fas fa-check-circle me-1"></i>
                        Done (<?php echo count($tasksByStatus['done']); ?>)
                    </div>

                    <?php foreach ($tasksByStatus['done'] as $task): ?>
                        <div class="task-card done">
                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                            <p class="text-muted small">
                                <?php echo htmlspecialchars(substr($task['description'], 0, 60)); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
    <!-- SUBMIT MODAL -->
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px; border:none; overflow:hidden;">

                <!-- HEADER -->
                <div class="modal-header text-white" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                    <h5 class="modal-title">
                        <i class="fas fa-paper-plane me-2"></i>
                        Submit Task
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Task Title
                        </label>
                        <input type="text" id="submitTaskTitle" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fab fa-github me-1"></i> Git Link
                        </label>
                        <input type="url" id="submitGitLink" class="form-control" placeholder="https://github.com/..."
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-comment me-1"></i> Message
                        </label>
                        <textarea id="submitMessage" class="form-control" rows="3"
                            placeholder="Explain your work..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button class="btn btn-primary" onclick="submitTask()">
                            <i class="fas fa-paper-plane me-1"></i> Submit
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let _submitTaskId = null;
        const currentProjectId = <?php echo json_encode($selectedProjectId); ?>;

        // project select navigation
        document.addEventListener('DOMContentLoaded', function () {
            const sel = document.getElementById('projectSelect');
            if (sel) {
                sel.addEventListener('change', function () {
                    const v = sel.value;
                    const q = v ? '?project_id=' + encodeURIComponent(v) : '';
                    window.location.href = window.location.pathname + q;
                });
            }
        });

        function openSubmitModal(taskId, title) {
            _submitTaskId = taskId;

            document.getElementById('submitTaskTitle').value = title;
            document.getElementById('submitGitLink').value = '';
            document.getElementById('submitMessage').value = '';

            new bootstrap.Modal(document.getElementById('submitModal')).show();
        }

        function submitTask() {
            const git_link = document.getElementById('submitGitLink').value.trim();
            const message = document.getElementById('submitMessage').value.trim();

            if (!git_link) {
                alert("Git link is required");
                return;
            }

            fetch('../../../controllers/taskSubmissionController.php?action=create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `task_id=${_submitTaskId}&git_link=${encodeURIComponent(git_link)}&message=${encodeURIComponent(message)}&ajax=1`
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('submitModal')).hide();
                        const q = currentProjectId ? '?project_id=' + encodeURIComponent(currentProjectId) : '';
                        window.location.href = window.location.pathname + q;
                    } else {
                        alert(data.message || "Submission failed");
                    }
                });
        }

        function startTask(e, taskId, btn) {
            e.stopPropagation();
            if (btn) btn.disabled = true;

            fetch('../../../controllers/taskController.php?action=updateStatus&id=' + encodeURIComponent(taskId) + "&status=in_progress&ajax=1")
                .then(r => r.json())
                .then(data => {
                    if (data && data.success) {
                        const q = currentProjectId ? '?project_id=' + encodeURIComponent(currentProjectId) : '';
                        window.location.href = window.location.pathname + q;
                    } else {
                        alert(data && data.message ? data.message : 'Failed to start task');
                        if (btn) btn.disabled = false;
                    }
                }).catch(() => {
                    alert('Failed to start task');
                    if (btn) btn.disabled = false;
                });
        }

        function markAllRead() {
            fetch('../../../controllers/notificationController.php?action=markAllRead')
                .then(() => {
                    const b = document.getElementById('notifBadge');
                    if (b) b.remove();
                }).catch(() => {
                    alert('Failed to mark notifications read');
                });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>