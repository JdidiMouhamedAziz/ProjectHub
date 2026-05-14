<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Project</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body{
    background-color:#f4f7fc;
}

.navbar{
    background: linear-gradient(135deg,#667eea,#764ba2);
}

.dashboard-card{
    border:none;
    border-radius:20px;
}

.welcome-box{
    background: linear-gradient(135deg,#667eea,#764ba2);
    border-radius:20px;
    color:white;
}

.form-control{
    border-radius:12px;
    padding:12px;
}

.btn-primary{
    border-radius:12px;
}

.btn-light{
    border-radius:12px;
}
</style>
</head>

<body>

<!-- NAVBAR (same dashboard style) -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold">
            <i class="fas fa-project-diagram me-2"></i>ProjectHub
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">

            <span class="text-white">
                <i class="fas fa-user me-1"></i>
                <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?>
            </span>

            <a href="../../views/logout.php" class="btn btn-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i>
            </a>

        </div>

    </div>
</nav>

<div class="container py-4">

<!-- WELCOME BOX -->
<div class="welcome-box p-4 shadow-sm mb-4">
    <div class="row align-items-center">

        <div class="col-md-8">
            <h2 class="fw-bold mb-2">
                Create New Project 🚀
            </h2>

            <p class="mb-0">
                Start building something amazing with your team.
            </p>
        </div>

        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="projectController.php?action=list" class="btn btn-light btn-lg">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Projects
            </a>
        </div>

    </div>
</div>

<!-- FORM CARD -->
<div class="card dashboard-card shadow-sm">

    <div class="card-header bg-white border-0 p-4">
        <h4 class="fw-bold mb-1">
            <i class="fas fa-plus-circle text-primary me-2"></i>
            Project Details
        </h4>
        <p class="text-muted mb-0">Fill in the information below</p>
    </div>

    <div class="card-body p-4">

        <form action="projectController.php?action=create" method="POST">

            <!-- TITLE -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Project Title *</label>
                <input type="text" class="form-control" name="title"
                       placeholder="Enter project title" required>
            </div>

            <!-- DESCRIPTION -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Description</label>
                <textarea class="form-control" name="description" rows="5"
                          placeholder="Describe your project..."></textarea>
            </div>

            <!-- BUTTONS -->
            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-save me-1"></i> Create Project
                </button>

                <a href="projectController.php?action=list"
                   class="btn btn-light btn-lg w-100 border">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>