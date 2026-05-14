<?php
session_start();
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/TaskSubmission.php";
require_once __DIR__ . "/../models/Task.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Notification.php";


requireLogin();


$submissionModel = new TaskSubmission($pdo);
$notificationModel = new Notification($pdo);
$taskModel       = new Task($pdo);
$userModel       = new User($pdo);

$action      = $_GET["action"] ?? 'list';
$sessionRole = $_SESSION["user"]["role"];
$sessionId   = $_SESSION["user"]["id"];

// --------------------------------------------------
// LIST
// --------------------------------------------------
if ($action === 'list') {

    if ($sessionRole === 'admin' || $sessionRole === 'manager') {
        $submissions = $submissionModel->findAllTaskSubmissions();
    } else {
        // user: only submissions for tasks assigned to them
        $myTasks     = $taskModel->findTasksByUser($sessionId);
        $submissions = [];
        foreach ($myTasks as $t) {
            $subs = $submissionModel->findTaskSubmissionsByTask($t['id']);
            foreach ($subs as $s) {
                $s['task_title'] = $t['title'];
                $submissions[]   = $s;
            }
        }
    }

    // attach task title if not already set
    foreach ($submissions as &$s) {
        if (!isset($s['task_title'])) {
            $task = $taskModel->findTaskById($s['task_id']);
            $s['task_title'] = $task['title'] ?? 'Unknown';
        }
    }
    unset($s);

    require_once __DIR__ . '/../views/admin/submissions/list.php';

// --------------------------------------------------
// CREATE (user submits their task)
// --------------------------------------------------
} elseif ($action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task_id  = intval($_POST['task_id']  ?? 0);
        $git_link = trim($_POST['git_link']   ?? '');
        $message  = trim($_POST['message']    ?? '');
        $isAjax   = isset($_POST['ajax']);

        if (!$task_id) {
            if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Task is required']); exit(); }
            die("Task is required");
        }
        if (empty($git_link)) {
            if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Git link is required']); exit(); }
            die("Git link is required");
        }

        // only allow if task is assigned to the user
        if ($sessionRole === 'user') {
            $task = $taskModel->findTaskById($task_id);
            if ($task['assigned_to'] != $sessionId) {
                if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
                die("Unauthorized");
            }
        }

        $submissionModel->createTaskSubmission($task_id, $git_link, $message, 'pending');
        $taskModel->updateTaskStatus($task_id, 'submitted');

        if ($isAjax) {
            echo json_encode(['success' => true]);
            exit();
        }

        header('Location: /ProjectWeb/controllers/taskController.php?action=kanban');
        exit();
    }


// --------------------------------------------------
// REVIEW (admin/manager approve or reject)
// --------------------------------------------------
} elseif ($action === 'review') {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    $id          = $_GET['id']          ?? null;
    $status      = $_GET['status']      ?? null;
    $project_id  = $_GET['project_id']  ?? null;

    // if project_id is provided, fetch all submissions for that project
    if ($project_id) {
        $submissions = $submissionModel->findTaskSubmissionsByProject($project_id);
        require_once __DIR__ . '/../views/manager/projects/show.php';
        exit();
    }

    // single submission review
    if (!$id || !$status) die("Missing fields");

    $allowed = ['approved', 'rejected', 'pending'];
    if (!in_array($status, $allowed)) die("Invalid status");

    // fetch the submission by id
    $stmt = $pdo->prepare("SELECT * FROM task_submissions WHERE id = ?");
    $stmt->execute([$id]);
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$submission) die("Submission not found");

    // update submission status
    $submissionModel->updateTaskSubmissionStatus($id, $status);

    if ($status === 'approved') {
        // mark task as done
        $taskModel->updateTaskStatus($submission['task_id'], 'done');

        // notify the assigned user
        $task = $taskModel->findTaskById($submission['task_id']);
        if ($task && $task['assigned_to']) {
            $notificationModel->createNotification(
                $task['assigned_to'],
                $task['id'],
                'approved',
                "✅ Your submission for task \"{$task['title']}\" has been approved!"
            );
        }

    } elseif ($status === 'rejected') {
        // mark task back to in_progress
        $taskModel->updateTaskStatus($submission['task_id'], 'in_progress');

        // notify the assigned user
        $task = $taskModel->findTaskById($submission['task_id']);
        if ($task && $task['assigned_to']) {
            $notificationModel->createNotification(
                $task['assigned_to'],
                $task['id'],
                'rejected',
                "❌ Your submission for task \"{$task['title']}\" was rejected. Please review and resubmit."
            );
        }
    }

    // redirect back to where the manager came from
    $ref = $_SERVER['HTTP_REFERER'] ?? '/ProjectWeb/controllers/projectController.php?action=list';
    header("Location: $ref");
    exit();



// --------------------------------------------------
// DELETE
// --------------------------------------------------
} elseif ($action === 'delete') {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    $id = $_GET['id'] ?? null;
    if (!$id) die("Submission ID is required");

    $submissionModel->deleteTaskSubmission($id);
    header('Location: taskSubmissionController.php?action=list');
    exit();

} else {
    die("Invalid action");
}
?>