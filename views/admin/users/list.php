<?php
// Note: session_start() is already called by the controller
// Variables passed from controller: $users (array of all users)

// read filters from query string
$filterUsername = trim($_GET['username'] ?? '');
$filterEmail = trim($_GET['email'] ?? '');
$filterRole = trim($_GET['role'] ?? '');

// apply filters to $users if provided
if (!empty($users) && ( $filterUsername !== '' || $filterEmail !== '' || $filterRole !== '')) {
    $users = array_values(array_filter($users, function($u) use ($filterUsername, $filterEmail, $filterRole) {
        if ($filterUsername !== '' && stripos($u['username'] ?? '', $filterUsername) === false) return false;
        if ($filterEmail !== '' && stripos($u['email'] ?? '', $filterEmail) === false) return false;
        if ($filterRole !== '' && $filterRole !== 'all' && ($u['role'] ?? '') !== $filterRole) return false;
        return true;
    }));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fc;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .soft-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body>


<!-- NAVBAR -->
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

                    <a href="../views/admin/dashboard.php" class="nav-link text-white me-3 active">
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
    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0"><i class="fas fa-users me-2"></i>Users Management</h1>
            <p class="text-muted">Manage all users in the system</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="userController.php?action=create" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus me-1"></i> Add New User
            </a>
        </div>
    </div>

    <!-- USERS TABLE -->
    <div class="card soft-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Users</h5>
                </div>
                <form class="row g-2 align-items-center" method="GET" action="userController.php">
                    <input type="hidden" name="action" value="list">
                    <div class="col-auto">
                        <input type="text" name="username" class="form-control form-control-sm" placeholder="Username" value="<?php echo htmlspecialchars($filterUsername); ?>">
                    </div>
                    <div class="col-auto">
                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Email" value="<?php echo htmlspecialchars($filterEmail); ?>">
                    </div>
                    <div class="col-auto">
                        <select name="role" class="form-select form-select-sm">
                            <option value="all">All roles</option>
                            <option value="admin" <?php echo $filterRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="manager" <?php echo $filterRole === 'manager' ? 'selected' : ''; ?>>Manager</option>
                            <option value="user" <?php echo $filterRole === 'user' ? 'selected' : ''; ?>>User</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="userController.php?action=list" class="btn btn-sm btn-light ms-1">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> No users found.
                    <a href="userController.php?action=create">Create one now</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'manager' ? 'warning' : 'info'); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo date('M d, Y', strtotime($user['created_at'])); ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="userController.php?action=show&id=<?php echo $user['id']; ?>" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="userController.php?action=update&id=<?php echo $user['id']; ?>" class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="userController.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>