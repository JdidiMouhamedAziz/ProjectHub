<?php
session_start();

require_once(__DIR__ . "/../../../config/database.php");
require_once(__DIR__ . "/../../../config/auth.php");
require_once(__DIR__ . "/../../../models/Notification.php");

requireLogin();

$notificationModel = new Notification($pdo);
$notifications = $notificationModel->findNotificationsByUser($_SESSION['user']['id']);
$unreadCount = $notificationModel->countUnread($_SESSION['user']['id']);

// actions
if (isset($_GET['action']) && $_GET['action'] === 'markread' && isset($_GET['id'])) {
    $notificationModel->markRead($_GET['id']);
    header("Location: list.php");
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'markallread') {
    $notificationModel->markAllRead($_SESSION['user']['id']);
    header("Location: list.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $notificationModel->deleteNotification($_GET['id']);
    header("Location: list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body{
    background-color:#f4f7fc;
}

/* SAME DASHBOARD NAVBAR */
.navbar{
    background: linear-gradient(135deg,#667eea,#764ba2);
}

/* SAME DASHBOARD CARD */
.dashboard-card{
    border:none;
    border-radius:20px;
    transition:0.3s;
}
.dashboard-card:hover{
    transform:translateY(-5px);
}
</style>

</head>

<body>

<!-- NAVBAR (EXACT COPY FROM DASHBOARD) -->
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

                <a href="../dashboard.php" class="nav-link text-white me-3">
                    <i class="fas fa-chart-line me-1"></i> Dashboard
                </a>

                <a href="../kanban/kanban.php" class="nav-link text-white me-3">
                    <i class="fas fa-table-columns me-1"></i> Kanban
                </a>

                <a href="list.php" class="nav-link text-white me-3 position-relative">
                    <i class="fas fa-bell me-1"></i> Notifications

                    <?php if($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="dropdown ms-lg-3">
                    <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION["user"]["username"]); ?>
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

<!-- CONTENT -->
<div class="container-fluid py-4">

    <!-- HEADER (same style as dashboard welcome box) -->
    <div class="card dashboard-card shadow-sm mb-4 text-white"
         style="background: linear-gradient(135deg,#667eea,#764ba2);">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </h2>

                    <p class="mb-0">
                        You have <?= $unreadCount ?> unread notification(s)
                    </p>
                </div>

                    <form id="markAllForm" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="markallread">
                        <button id="markAllBtn" class="btn btn-light btn-lg" type="submit" <?php echo $unreadCount <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-check-double me-1"></i> Mark All Read
                        </button>
                    </form>

            </div>

        </div>
    </div>

    <!-- NOTIFICATIONS -->
    <div class="row">

        <div class="col-12">

        <?php if(empty($notifications)): ?>

            <div class="card dashboard-card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5>No notifications yet</h5>
                </div>
            </div>

        <?php else: ?>

            <?php foreach($notifications as $notif): ?>

                <?php
                    $map = [
                        'task_assigned' => ['info','tasks'],
                        'task_updated' => ['warning','edit'],
                        'submission_approved' => ['success','check-circle'],
                        'submission_rejected' => ['danger','times-circle']
                    ];

                    $type = $map[$notif['type']] ?? ['secondary','bell'];
                ?>

                <div class="card dashboard-card shadow-sm mb-3
                    <?= !$notif['is_read'] ? 'border border-primary' : '' ?>">

                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="mb-1">
                                <?php if(!$notif['is_read']): ?>
                                    <span class="badge bg-primary">New</span>
                                <?php endif; ?>

                                <span class="badge bg-<?= $type[0] ?>">
                                    <i class="fas fa-<?= $type[1] ?>"></i>
                                    <?= ucfirst(str_replace('_',' ',$notif['type'])) ?>
                                </span>
                            </h6>

                            <p class="mb-1">
                                <?= htmlspecialchars($notif['message']) ?>
                            </p>

                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                <?= $notif['created_at'] ?>
                            </small>

                        </div>

                        <div class="d-flex gap-2">

                            <?php if(!$notif['is_read']): ?>
                                <a href="?action=markread&id=<?= $notif['id'] ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    Mark Read
                                </a>
                            <?php endif; ?>

                            <a href="?action=delete&id=<?= $notif['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this notification?')">
                                Delete
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.getElementById('markAllForm').addEventListener('submit', function(e){
            e.preventDefault();
            var btn = document.getElementById('markAllBtn');
            if (btn.disabled) return;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Marking...';

            fetch(window.location.pathname, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=markallread'
            }).then(function(){
                // reload to show updated notifications; could be replaced with DOM updates
                window.location.reload();
            }).catch(function(){
                alert('Failed to mark all read.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-double me-1"></i> Mark All Read';
            });
        });
        </script>
</body>
</html>