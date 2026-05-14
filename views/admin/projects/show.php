<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Project Details</title>
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

        .dropdown-menu {
            border: none;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.13);
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

    <div class="container-fluid py-4">
        <div class="row g-4">
            <div class="">

                <!-- Welcome / Hero -->
                <div class="welcome-box p-4 shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="badge project-badge rounded-pill px-3 py-2 mb-3">
                                <i class="fas fa-folder-open me-1"></i> Project Details
                            </span>
                            <h2 class="fw-bold mb-1">
                                <?php echo htmlspecialchars($project['title']); ?>
                            </h2>
                            <p class="mb-0 text-white-50">
                                <?php echo htmlspecialchars($project['description']); ?>
                            </p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="../controllers/projectController.php?action=list" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to Projects
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card dashboard-card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Total Tasks</div>
                                    <div class="h2 fw-bold mb-0"><?php echo $totalTasks; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card dashboard-card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon text-warning">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">In Progress</div>
                                    <div class="h2 fw-bold mb-0"><?php echo $inProgressTasks; ?></div>
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
                                    <div class="text-muted small">Submitted</div>
                                    <div class="h2 fw-bold mb-0"><?php echo $submittedTasks; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card dashboard-card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon text-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Completed</div>
                                    <div class="h2 fw-bold mb-0"><?php echo $doneTasks; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress -->
                <div class="card soft-card mb-4">
                    <div class="card-body">
                        <h6 class="section-title mb-2">
                            <i class="fas fa-chart-line text-primary me-2"></i> Project Progress
                        </h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: <?php echo $progress; ?>%"
                                aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                <?php echo $progress; ?>%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions — used as section switcher -->
                <div class="card soft-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                            <div>
                                <h5 class="section-title mb-1">
                                    <i class="fas fa-bolt text-warning me-2"></i> Quick Actions
                                </h5>
                                <small class="text-muted">Jump to tasks, submissions or team members.</small>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <button class="btn btn-primary quick-btn" onclick="showTab('tasks')">
                                <i class="fas fa-tasks me-1"></i> Tasks
                            </button>
                            <button class="btn btn-info text-white quick-btn" onclick="showTab('submissions')">
                                <i class="fas fa-file-upload me-1"></i> Submissions
                            </button>
                            <button class="btn btn-success quick-btn" onclick="showTab('members')">
                                <i class="fas fa-users me-1"></i> Members
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden native tabs (Bootstrap still drives the pane switching) -->
                <ul class="nav d-none" id="projectTabs">
                    <li class="nav-item"><button class="nav-link active" id="tasks-tab"       data-bs-toggle="tab" data-bs-target="#tasks"       type="button"></button></li>
                    <li class="nav-item"><button class="nav-link"        id="submissions-tab" data-bs-toggle="tab" data-bs-target="#submissions" type="button"></button></li>
                    <li class="nav-item"><button class="nav-link"        id="members-tab"     data-bs-toggle="tab" data-bs-target="#members"     type="button"></button></li>
                </ul>

                <div class="tab-content" id="projectTabsContent">

                    <!-- Tasks -->
                    <div class="tab-pane fade show active" id="tasks" role="tabpanel">
                        <div class="card soft-card">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="section-title mb-1">
                                        <i class="fas fa-tasks text-primary me-2"></i> All Tasks
                                    </h5>
                                    <small class="text-muted">All tasks belonging to this project.</small>
                                </div>
                                <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
                                <button class="btn btn-primary btn-sm quick-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                                    <i class="fas fa-plus me-1"></i> Add Task
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (empty($tasks)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
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
                                                $statusClass = 'secondary'; $statusIcon = 'circle';
                                                switch ($task['status']) {
                                                    case 'open':        $statusClass = 'danger';  $statusIcon = 'circle';       break;
                                                    case 'in_progress': $statusClass = 'warning'; $statusIcon = 'spinner';      break;
                                                    case 'submitted':   $statusClass = 'info';    $statusIcon = 'file-upload';  break;
                                                    case 'done':        $statusClass = 'success'; $statusIcon = 'check-circle'; break;
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($task['description'], 0, 50)); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                                        <i class="fas fa-<?php echo $statusIcon; ?>"></i>
                                                        <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><span class="badge bg-secondary"><?php echo $task['complexity']; ?>/9</span></td>
                                                <td><?php echo htmlspecialchars($task['assignee_name']); ?></td>
                                                <td>
                                                    <?php if ($task['submission']): ?>
                                                        <span class="badge bg-info"><i class="fas fa-file me-1"></i><?php echo ucfirst($task['submission']['status']); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">None</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="./taskController.php?action=update&id=<?php echo $task['id']; ?>" class="btn btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
                                                        <a href="./taskController.php?action=delete&id=<?php echo $task['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Submissions -->
                    <div class="tab-pane fade" id="submissions" role="tabpanel">
                        <div class="card soft-card">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="section-title mb-1">
                                    <i class="fas fa-file-upload text-primary me-2"></i> All Submissions
                                </h5>
                                <small class="text-muted">Task submissions and their review status.</small>
                            </div>
                            <div class="card-body">
                                <?php
                                    $allSubmissions = [];
                                    foreach ($tasks as $task) {
                                        if ($task['submission']) {
                                            $task['submission']['task_title'] = $task['title'];
                                            $allSubmissions[] = $task['submission'];
                                        }
                                    }
                                ?>
                                <?php if (empty($allSubmissions)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($allSubmissions as $submission): ?>
                                            <?php
                                                $class = 'secondary';
                                                switch ($submission['status']) {
                                                    case 'pending':  $class = 'warning'; break;
                                                    case 'approved': $class = 'success'; break;
                                                    case 'rejected': $class = 'danger';  break;
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($submission['task_title']); ?></td>
                                                <td><span class="badge bg-<?php echo $class; ?>"><?php echo ucfirst($submission['status']); ?></span></td>
                                                <td>
                                                    <a href="<?php echo htmlspecialchars($submission['git_link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fab fa-github"></i> View Repo
                                                    </a>
                                                </td>
                                                <td><small><?php echo htmlspecialchars(substr($submission['message'], 0, 50)); ?></small></td>
                                                <td><small><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Members -->
                    <div class="tab-pane fade" id="members" role="tabpanel">
                        <div class="card soft-card">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="section-title mb-1">
                                        <i class="fas fa-users text-primary me-2"></i> Team Members
                                    </h5>
                                    <small class="text-muted">Everyone assigned to this project.</small>
                                </div>
                                <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
                                <button class="btn btn-success btn-sm quick-btn" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                    <i class="fas fa-user-plus me-1"></i> Add Member
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (empty($members)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
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
                                                <td><strong><?php echo htmlspecialchars($member['username']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo ucfirst($member['role_user']); ?></span></td>
                                                <td><span class="badge bg-info"><?php echo ucfirst($member['role']); ?></span></td>
                                                <td><small><?php echo date('M d, Y', strtotime($member['joined_at'])); ?></small></td>
                                                <td class="text-end">
                                                    <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
                                                    <a href="projectController.php?action=removeMember&project_id=<?php echo $project['id']; ?>&user_id=<?php echo $member['user_id']; ?>"
                                                        class="btn btn-sm btn-danger" onclick="return confirm('Remove this member?')">
                                                        <i class="fas fa-times"></i> Remove
                                                    </a>
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

                </div><!-- /tab-content -->

            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:20px;border:none;box-shadow:0 10px 30px rgba(15,23,42,.1)">
                <div class="modal-header">
                    <h5 class="modal-title section-title">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/taskController.php?action=create" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Complexity (1-9) *</label>
                                <input type="number" class="form-control" name="complexity" min="1" max="9" value="5" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assign To</label>
                                <select class="form-select" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Add Member Modal -->
    <?php if ($sessionRole === 'admin' || $sessionRole === 'manager'): ?>
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:20px;border:none;box-shadow:0 10px 30px rgba(15,23,42,.1)">
                <div class="modal-header">
                    <h5 class="modal-title section-title">Add Member to Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/projectController.php?action=addMember" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Select User *</label>
                            <select class="form-select" name="user_id" required>
                                <option value="">Choose a user...</option>
                                <?php
                                    $allUsers = $userModel->findAllUsers();
                                    $existingMembers = array_map(function($m) { return $m['user_id']; }, $members);
                                    foreach ($allUsers as $u):
                                        if (!in_array($u['id'], $existingMembers)):
                                ?>
                                    <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['username']); ?> (<?php echo $u['email']; ?>)</option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role" required>
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showTab(name) {
            document.getElementById(name + '-tab').click();
        }
    </script>
</body>
</html> 