<?php
if (!isset($_SESSION["user"])) {
    session_start();
}

require_once __DIR__ . "/../../../config/auth.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../models/Project.php";
require_once __DIR__ . "/../../../models/ProjectMember.php";

requireManager();

$projectModel = new Project($pdo);
$projectMemberModel = new ProjectMember($pdo);

$memberships = $projectMemberModel->findProjectsByMember($_SESSION['user']['id']);

$projects = [];
foreach ($memberships as $m) {
    $p = $projectModel->findProjectById($m['project_id']);
    if ($p)
        $projects[] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects</title>

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

        .project-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
        }

        .project-card:hover {
            transform: translateY(-5px);
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

                    <a href="../views/manager/dashboard.php" class="nav-link-custom">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>

                    <a href="../controllers/projectController.php?action=list" class="nav-link-custom">
                        <i class="fas fa-project-diagram me-1"></i> Projects
                    </a>



                    <div class="dropdown ms-lg-3">

                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?>
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

        <!-- WELCOME BOX  -->
        <div class="welcome-box p-4 shadow-sm mb-4">
            <div class="row align-items-center">

                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">
                        My Projects 📁
                    </h2>

                    <p class="mb-0">
                        Manage and track all your projects.
                    </p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="../controllers/projectController.php?action=create" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        New Project
                    </a>
                </div>

            </div>
        </div>

        <!-- CONTENT CARD -->
        <div class="card dashboard-card shadow-sm">

            <div class="card-header bg-white border-0 p-4">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="fas fa-folder text-primary me-2"></i>
                        Project List
                    </h4>
                    <p class="text-muted mb-0">All projects you manage</p>
                </div>
            </div>

            <div class="card-body">

                <?php if (empty($projects)): ?>

                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5>No projects found</h5>
                        <p class="text-muted">Start by creating your first project.</p>
                    </div>

                <?php else: ?>

                    <div class="row g-4">

                        <?php foreach ($projects as $project): ?>

                            <div class="col-md-6 col-lg-4">

                                <div class="card project-card shadow-sm h-100">

                                    <div class="card-body">

                                        <h5 class="fw-bold mb-2">
                                            <i class="fas fa-folder text-warning me-1"></i>
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </h5>

                                        <p class="text-muted small">
                                            <?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>
                                            <?php if (strlen($project['description']) > 100)
                                                echo "..."; ?>
                                        </p>

                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M d, Y', strtotime($project['created_at'])); ?>
                                        </small>

                                    </div>

                                    <div class="card-footer bg-white border-0 d-flex gap-2">

                                        <a href="../controllers/projectController.php?action=show&id=<?php echo $project['id']; ?>"
                                            class="btn btn-outline-primary btn-sm w-100">
                                            <i class="fas fa-eye"></i> View
                                        </a>

                                        <a href="../controllers/projectController.php?action=update&id=<?php echo $project['id']; ?>"
                                            class="btn btn-outline-warning btn-sm w-100">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="if(confirm('Delete this project? This will remove all project data. Are you sure?')) { window.location.href='../controllers/projectController.php?action=delete&id=<?php echo $project['id']; ?>'; }">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>