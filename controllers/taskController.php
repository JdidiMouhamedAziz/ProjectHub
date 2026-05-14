<?php
session_start();
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/Task.php";
require_once __DIR__ . "/../models/Project.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/TaskSubmission.php";
require_once __DIR__ . "/../models/ProjectMember.php";

requireLogin();

$taskModel    = new Task($pdo);
$projectModel = new Project($pdo);
$userModel    = new User($pdo);
$submissionModel = new TaskSubmission($pdo);
$projectMemberModel = new ProjectMember($pdo);

$action      = $_GET["action"] ?? 'list';
$sessionRole = $_SESSION["user"]["role"];
$sessionId   = $_SESSION["user"]["id"];

// --------------------------------------------------
// LIST
// --------------------------------------------------
if ($action === 'list') {

    if ($sessionRole === 'admin') {
        $tasks = $taskModel->findAllTasks();
    } elseif ($sessionRole === 'manager') {
        // manager sees all tasks (they manage projects)
        $tasks = $taskModel->findAllTasks();
    } else {
        // user sees only their assigned tasks
        $tasks = $taskModel->findTasksByUser($sessionId);
    }

    // attach project name and assignee name to each task
    $projects = $projectModel->findAllProjects();
    $users    = $userModel->findAllUsers();

    if ($sessionRole === 'admin') {
        require_once __DIR__ . '/../views/admin/tasks/list.php';
    }else{
        require_once __DIR__ . '/../views/manager/projects/list.php';
    }

// --------------------------------------------------
// SHOW
// --------------------------------------------------
} elseif ($action === 'show') {

    require_once __DIR__ . "/../models/TaskSubmission.php";

    $id = $_GET['id'] ?? null;
    if (!$id) die("Task ID is required");

    $task        = $taskModel->findTaskById($id);
    if (!$task) die("Task not found");

    $project     = $projectModel->findProjectById($task['project_id']);
    $assignee    = $task['assigned_to'] ? $userModel->findUserById($task['assigned_to']) : null;
    $submissions = (new TaskSubmission($pdo))->findTaskSubmissionsByTask($id);

    require_once __DIR__ . '/../views/admin/tasks/show.php';

// --------------------------------------------------
// CREATE
// --------------------------------------------------
} elseif ($action === 'create') {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $title       = trim($_POST["title"]       ?? '');
        $description = trim($_POST["description"] ?? '');
        $status      = trim($_POST["status"]      ?? 'open');
        $complexity  = intval($_POST["complexity"] ?? 1);
        $project_id  = intval($_POST["project_id"] ?? 0);
        $assigned_to = !empty($_POST["assigned_to"]) ? intval($_POST["assigned_to"]) : null;

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Title is required']);
            exit();
        }
        if (!$project_id) {
            echo json_encode(['success' => false, 'message' => 'Project is required']);
            exit();
        }
        if ($complexity < 1 || $complexity > 9) {
            echo json_encode(['success' => false, 'message' => 'Complexity must be between 1 and 9']);
            exit();
        }

        $taskModel->createTask($title, $description, $status, $complexity, $project_id, $assigned_to);

        $assignee_name = 'Unassigned';
        if ($assigned_to) {
            $u = $userModel->findUserById($assigned_to);
            $assignee_name = $u['username'] ?? 'Unassigned';
        }
        header("Location: projectController.php?action=show&id=$project_id");
        exit();
    }

    $projects = $projectModel->findAllProjects();
    $users    = $userModel->findAllUsers();
    require_once __DIR__ . "/../views/admin/tasks/create.php";

// --------------------------------------------------
// UPDATE
// --------------------------------------------------

} elseif ($action === 'kanban') {

    require_once __DIR__ . "/../models/ProjectMember.php";
    require_once __DIR__ . "/../models/TaskSubmission.php";

    $projectMemberModel = new ProjectMember($pdo);
    $submissionModel    = new TaskSubmission($pdo);

    // get user's projects
    $memberships  = $projectMemberModel->findProjectsByMember($sessionId);
    $userProjects = [];
    foreach ($memberships as $m) {
        $p = $projectModel->findProjectById($m['project_id']);
        if ($p) $userProjects[] = $p;
    }

    // filter by selected project or default to first one
    $selectedProjectId = $_GET['project_id'] ?? ($userProjects[0]['id'] ?? null);
    $selectedProject   = null;

    $kanban = [
        'open'        => [],
        'in_progress' => [],
        'submitted'   => [],
        'done'        => [],
    ];

    if ($selectedProjectId) {
        // only load tasks from this project assigned to this user
        $projectTasks = $taskModel->findTasksByProjectId($selectedProjectId);
        $allTasks = array_filter($projectTasks, fn($t) => $t['assigned_to'] == $sessionId);

        foreach ($allTasks as &$t) {
            $p = $projectModel->findProjectById($t['project_id']);
            $t['project_title'] = $p['title'] ?? '—';
            $selectedProject    = $p;
            $subs = $submissionModel->findTaskSubmissionsByTask($t['id']);
            $t['submission'] = !empty($subs) ? $subs[0] : null;
        }
        unset($t);

        $kanban = [
            'open'        => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'open')),
            'in_progress' => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'in_progress')),
            'submitted'   => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'submitted')),
            'done'        => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'done')),
        ];
    }

    require_once __DIR__ . '/../views/user/tasks/kanban.php';

} elseif ($action === "update") {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    $id = $_GET["id"] ?? null;
    if (!$id) die("Task ID is required");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title       = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');
        $status      = trim($_POST['status']      ?? 'open');
        $complexity  = intval($_POST['complexity'] ?? 1);
        $project_id  = intval($_POST['project_id'] ?? 0);
        $assigned_to = !empty($_POST["assigned_to"]) ? intval($_POST["assigned_to"]) : null;
        $returnTo    = $_POST['return_to'] ?? '';

        if (empty($title))      die("Title is required");
        if (empty($project_id)) die("Project is required");

        $taskModel->updateTask($id, $title, $description, $status, $complexity, $project_id, $assigned_to);
        // if status changed to submitted and there is no submission yet, create a pending submission placeholder
        if ($status === 'submitted') {
            $existing = $submissionModel->findTaskSubmissionsByTask($id);
            if (empty($existing)) {
                $submissionModel->createTaskSubmission($id, '', 'Submitted', 'pending');
            }
        }
        if (!empty($returnTo)) {
            header('Location: ' . $returnTo);
        } else {
            header('Location: taskController.php?action=list');
        }
        exit();
    }

    $task     = $taskModel->findTaskById($id);
    $projects = $projectModel->findAllProjects();

    $users = [];
    $seenUsers = [];
    if (!empty($task['project_id'])) {
        $members = $projectMemberModel->findMembersByProject($task['project_id']);
        foreach ($members as $member) {
            $memberUser = $userModel->findUserById($member['user_id']);
            if ($memberUser && $memberUser['role'] === 'user' && !isset($seenUsers[$memberUser['id']])) {
                $users[] = $memberUser;
                $seenUsers[$memberUser['id']] = true;
            }
        }
    }

    if ($sessionRole === 'manager') {
        require_once __DIR__ . '/../views/manager/tasks/update.php';
    } else {
        require_once __DIR__ . '/../views/admin/tasks/edit.php';
    }

// --------------------------------------------------
// UPDATE STATUS ONLY (for user kanban drag or quick update)
// --------------------------------------------------
} elseif ($action === 'updateStatus') {

    $id     = $_GET['id']     ?? null;
    $status = $_GET['status'] ?? null;
    $isAjax = isset($_GET['ajax']);

    if (!$id || !$status) {
        if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Missing fields']); exit(); }
        die("Missing fields");
    }

    $allowed = ['open', 'in_progress', 'submitted', 'done'];
    if (!in_array($status, $allowed)) {
        if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Invalid status']); exit(); }
        die("Invalid status");
    }

    // user can only update their own tasks
    if ($sessionRole === 'user') {
        $task = $taskModel->findTaskById($id);
        if ($task['assigned_to'] != $sessionId) {
            if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
            die("Unauthorized");
        }
    }

    $taskModel->updateTaskStatus($id, $status);

    // if status changed to submitted via quick update, create a pending submission if none exists
    if ($status === 'submitted') {
        $existing = $submissionModel->findTaskSubmissionsByTask($id);
        if (empty($existing)) {
            $submissionModel->createTaskSubmission($id, '', 'Submitted via status change', 'pending');
        }
    }

    if ($isAjax) {
        echo json_encode(['success' => true]);
        exit();
    }

    header('Location: /ProjectWeb/controllers/taskController.php?action=kanban');
    exit();

// --------------------------------------------------
// DELETE
// --------------------------------------------------
} elseif ($action === 'delete') {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    $id = $_GET["id"] ?? null;
    if (!$id) die("Task ID is required");

    $taskModel->deleteTask($id);
    // redirect where the user come from or to task list
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'taskController.php?action=list';
    header("Location: $redirect");
    exit();
    

} else {
    die("Invalid action");
}
?>