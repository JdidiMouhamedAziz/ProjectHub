<?php
session_start();
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/Project.php";
require_once __DIR__ . "/../models/ProjectMember.php";
require_once __DIR__ . "/../models/User.php";

requireLogin();


$projectModel       = new Project($pdo);
$projectMemberModel = new ProjectMember($pdo);
$userModel          = new User($pdo);

$action     = $_GET["action"] ?? 'list';
$sessionRole = $_SESSION["user"]["role"];
$sessionId   = $_SESSION["user"]["id"];

// --------------------------------------------------
// LIST
// --------------------------------------------------
    
if ($action === 'list') {
    if ($sessionRole === 'admin' ) {
        $projects = $projectModel->findAllProjects();
        require_once __DIR__ . '/../views/admin/projects/list.php';
    } elseif ($sessionRole === 'manager') {
        $memberships = $projectMemberModel->findProjectsByMember($sessionId);
        $projects = [];
        foreach ($memberships as $m) {
            $p = $projectModel->findProjectById($m['project_id']);
            if ($p) { $p['member_role'] = $m['role']; $projects[] = $p; }
        }
        require_once __DIR__ . '/../views/manager/projects/list.php';
    } else {
        $memberships = $projectMemberModel->findProjectsByMember($sessionId);
        $projects = [];
        foreach ($memberships as $m) {
            $p = $projectModel->findProjectById($m['project_id']);
            if ($p) { $p['member_role'] = $m['role']; $projects[] = $p; }
        }
        require_once __DIR__ . '/../views/user/projects/list.php';
    }
   


 

// --------------------------------------------------
// SHOW (project detail + members + tasks)
// --------------------------------------------------
} elseif ($action === 'show') {

    $id = $_GET['id'] ?? null;
    if (!$id) die("Project ID is required");

    require_once __DIR__ . "/../models/Task.php";
    require_once __DIR__ . "/../models/TaskSubmission.php";

    $taskModel       = new Task($pdo);
    $submissionModel = new TaskSubmission($pdo);

    $project = $projectModel->findProjectById($id);
    if (!$project) die("Project not found");

    $tasks   = $taskModel->findTasksByProjectId($id);
    $members = $projectMemberModel->findMembersByProject($id);

    // attach username to each member
    foreach ($members as &$m) {
        $u = $userModel->findUserById($m['user_id']);
        $m['username'] = $u['username'] ?? 'Unknown';
        $m['email']    = $u['email']    ?? '';
        $m['role_user']= $u['role']     ?? '';
    }
    unset($m);

    // attach assignee name + submission to each task
    foreach ($tasks as &$t) {
        $u = $t['assigned_to'] ? $userModel->findUserById($t['assigned_to']) : null;
        $t['assignee_name'] = $u['username'] ?? 'Unassigned';
        $subs = $submissionModel->findTaskSubmissionsByTask($t['id']);
        $t['submission'] = !empty($subs) ? $subs[0] : null;
    }
    unset($t);

    // stats
    $totalTasks     = count($tasks);
    $doneTasks      = count(array_filter($tasks, fn($t) => $t['status'] === 'done'));
    $inProgressTasks= count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress'));
    $submittedTasks = count(array_filter($tasks, fn($t) => $t['status'] === 'submitted'));
    $openTasks      = count(array_filter($tasks, fn($t) => $t['status'] === 'open'));
    $progress       = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

    
    if ($sessionRole==='manager'){
        require_once __DIR__ . '/../views/manager/projects/show.php';
    }else {
        require_once __DIR__ . '/../views/admin/projects/show.php';
    }

// --------------------------------------------------
// CREATE
// --------------------------------------------------
} elseif ($action === 'create') {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $title       = trim($_POST["title"]       ?? '');
        $description = trim($_POST["description"] ?? '');

        if (empty($title)) die("Project title is required");

        $projectModel->createProject($title, $description);

        // if manager, auto-add them as project admin
        if ($sessionRole === 'manager') {
            $newId = $pdo->lastInsertId();
            $projectMemberModel->createProjectMember($newId, $sessionId, 'admin');
        }

        header('Location: projectController.php?action=list');
        exit();
    }

    if ($sessionRole==='admin') {
        require_once __DIR__ . "/../views/admin/projects/create.php";
    }
    if ($sessionRole==='manager') {
        require_once __DIR__ . "/../views/manager/projects/create.php";
    }

// --------------------------------------------------
// UPDATE
// --------------------------------------------------
} elseif ($action === "update") {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    $id = $_GET["id"] ?? null;
    if (!$id) die("Project ID is required");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title       = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($title)) die("Project title is required");

        $projectModel->updateProject($id, $title, $description);
        header('Location: projectController.php?action=list');
        exit();
    }

    $project = $projectModel->findProjectById($id);
    require_once __DIR__ . '/../views/admin/projects/edit.php';

// --------------------------------------------------
// DELETE
// --------------------------------------------------
} elseif ($action === 'delete') {

    if ($sessionRole !== 'admin' && $sessionRole!=='manager') die("Unauthorized");

    $id = $_GET["id"] ?? null;
    if (!$id) die("Project ID is required");

    $projectModel->deleteProject($id);
    header('Location: projectController.php?action=list');
    exit();

// --------------------------------------------------
// ADD MEMBER
// --------------------------------------------------
} elseif ($action === 'addMember') {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_id = $_POST['project_id'] ?? null;
        $user_id    = $_POST['user_id']    ?? null;
        $role       = $_POST['role']       ?? 'member';

        if (!$project_id || !$user_id) die("Missing fields");

        $projectMemberModel->createProjectMember($project_id, $user_id, $role);
        header("Location: projectController.php?action=show&id=$project_id");
        exit();
    }

// --------------------------------------------------
// REMOVE MEMBER
// --------------------------------------------------
} elseif ($action === 'removeMember') {

    if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
        die("Unauthorized");
    }

    $project_id = $_GET['project_id'] ?? null;
    $user_id    = $_GET['user_id']    ?? null;

    if (!$project_id || !$user_id) die("Missing fields");

    $projectMemberModel->deleteProjectMember($project_id, $user_id);
    header("Location: projectController.php?action=show&id=$project_id");
    exit();

} else {
    die("Invalid action");
}
?>