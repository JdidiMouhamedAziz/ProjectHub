<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>

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

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #d1d5db;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }

        .btn-custom {
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 600;
        }

        .section-title {
            font-weight: 700;
        }

        .project-badge {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.18);
            color: white;
        }

        .info-box {
            border-radius: 15px;
            background: rgba(102, 126, 234, 0.08);
            padding: 15px;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-project-diagram me-2"></i>
            ProjectHub - Edit Task
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

<div class="container py-4">

    <!-- Header -->
    <div class="page-header p-4 shadow-sm mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <span class="badge project-badge rounded-pill px-3 py-2 mb-3">
                    <i class="fas fa-edit me-1"></i>
                    Task Management
                </span>

                <h2 class="fw-bold mb-1">
                    Edit Task
                </h2>

                <p class="mb-0 text-white-50">
                    Update task information, assignment and progress.
                </p>

            </div>

            

        </div>

    </div>

    <div class="row justify-content-center">

        <div class="col-lg-9">

            <div class="card soft-card">

                <div class="card-body p-4">

                    <?php if (!isset($task) || !$task): ?>

                        <div class="text-center py-5">

                            <i class="fas fa-circle-exclamation fa-3x text-danger mb-3"></i>

                            <h4>Task Not Found</h4>

                            <p class="text-muted">
                                The requested task does not exist.
                            </p>

                            <a href="taskController.php?action=list"
                                class="btn btn-secondary btn-custom">

                                Back
                            </a>

                        </div>

                    <?php else: ?>

                        <div class="mb-4">

                            <h4 class="section-title">
                                <i class="fas fa-pen text-warning me-2"></i>
                                Update Task Information
                            </h4>

                            <p class="text-muted mb-0">
                                Modify task details and assignment.
                            </p>

                        </div>

                        <form action="/projectHub/controllers/taskController.php?action=update&id=<?php echo $task['id']; ?>"
                            method="POST">

                            <div class="mb-4">

                                <label for="title" class="form-label fw-semibold">
                                    Task Title *
                                </label>

                                <input type="text"
                                    class="form-control"
                                    id="title"
                                    name="title"
                                    required
                                    value="<?php echo htmlspecialchars($task['title']); ?>"
                                    placeholder="Enter task title">

                            </div>

                            <div class="mb-4">

                                <label for="description" class="form-label fw-semibold">
                                    Description
                                </label>

                                <textarea class="form-control"
                                    id="description"
                                    name="description"
                                    rows="5"
                                    placeholder="Enter task description"><?php echo htmlspecialchars($task['description']); ?></textarea>

                            </div>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="mb-4">

                                        <label for="complexity" class="form-label fw-semibold">
                                            Complexity (1-9) *
                                        </label>

                                        <input type="number"
                                            class="form-control"
                                            id="complexity"
                                            name="complexity"
                                            min="1"
                                            max="9"
                                            required
                                            value="<?php echo $task['complexity']; ?>">

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="mb-4">

                                        <label for="status" class="form-label fw-semibold">
                                            Status *
                                        </label>

                                        <select class="form-select"
                                            id="status"
                                            name="status"
                                            required>

                                            <option value="open"
                                                <?php echo $task['status'] === 'open' ? 'selected' : ''; ?>>
                                                Open
                                            </option>

                                            <option value="in_progress"
                                                <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>
                                                In Progress
                                            </option>

                                            <option value="submitted"
                                                <?php echo $task['status'] === 'submitted' ? 'selected' : ''; ?>>
                                                Submitted
                                            </option>

                                            <option value="done"
                                                <?php echo $task['status'] === 'done' ? 'selected' : ''; ?>>
                                                Done
                                            </option>

                                        </select>

                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="mb-4">

                                        <label for="project_id" class="form-label fw-semibold">
                                            Project *
                                        </label>

                                        <select class="form-select"
                                            id="project_id"
                                            name="project_id"
                                            required>

                                            <?php foreach ($projects as $proj): ?>

                                                <option value="<?php echo $proj['id']; ?>"
                                                    <?php echo $proj['id'] == $task['project_id'] ? 'selected' : ''; ?>>

                                                    <?php echo htmlspecialchars($proj['title']); ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="mb-4">

                                        <label for="assigned_to" class="form-label fw-semibold">
                                            Assign To
                                        </label>

                                        <select class="form-select"
                                            id="assigned_to"
                                            name="assigned_to">

                                            <option value="">
                                                Unassigned
                                            </option>

                                            <?php foreach ($users as $user): ?>

                                                <option value="<?php echo $user['id']; ?>"
                                                    <?php echo $user['id'] == $task['assigned_to'] ? 'selected' : ''; ?>>

                                                    <?php echo htmlspecialchars($user['username']); ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </div>

                                </div>

                            </div>

                            <div class="info-box mb-4">

                                <i class="fas fa-info-circle text-primary me-2"></i>

                                Created:
                                <strong>
                                    <?php echo date('M d, Y H:i', strtotime($task['created_at'])); ?>
                                </strong>

                            </div>

                            <div class="d-flex gap-3">

                                <button type="submit"
                                    class="btn btn-warning btn-custom flex-grow-1">

                                    <i class="fas fa-save me-2"></i>
                                    Update Task
                                </button>

                                <a href="taskController.php?action=list"
                                    class="btn btn-secondary btn-custom flex-grow-1">

                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </a>

                            </div>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>