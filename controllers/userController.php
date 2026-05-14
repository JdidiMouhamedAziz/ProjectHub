<?php

session_start();
// With:
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/User.php";

requireAdmin();// Just Admin can manage users

$userModel = new User($pdo);

$action = $_GET["action"] ?? 'list';

// --------------------------------------------------
// LIST USERS
// --------------------------------------------------
if ($action === 'list') {
    $users = $userModel->findAllUsers();
require __DIR__ . '/../views/admin/users/list.php';
} elseif ($action === 'show') {
    $id = $_GET['id'] ?? null;
    if (!$id) die("User Id is Required");

    require_once __DIR__ . '/../models/Task.php';
    require_once __DIR__ . '/../models/Project.php';
    require_once __DIR__ . '/../models/ProjectMember.php';
    require_once __DIR__ . '/../models/TaskSubmission.php';

    $taskModel           = new Task($pdo);
    $projectModel        = new Project($pdo);
    $projectMemberModel  = new ProjectMember($pdo);
    $submissionModel     = new TaskSubmission($pdo);

    $user        = $userModel->findUserById($id);
    if (!$user) die("User not found");

    // tasks assigned to this user
    $tasks = $taskModel->findTasksByUser($id);

    // attach submission to each task
    foreach ($tasks as &$t) {
        $subs = $submissionModel->findTaskSubmissionsByTask($t['id']);
        $t['submission'] = !empty($subs) ? $subs[0] : null;
    }
    unset($t);

    // projects where user is a member
    $memberships = $projectMemberModel->findProjectsByMember($id);
    $projects = [];
    foreach ($memberships as $m) {
        $p = $projectModel->findProjectById($m['project_id']);
        if ($p) {
            $p['member_role'] = $m['role'];
            $projects[] = $p;
        }
    }

    require_once __DIR__ . '/../views/admin/users/show.php';

}elseif ($action === 'kanban') {

    require_once __DIR__ . "/../models/ProjectMember.php";
    require_once __DIR__ . "/../models/TaskSubmission.php";

    $projectMemberModel = new ProjectMember($pdo);
    $submissionModel    = new TaskSubmission($pdo);

    // get tasks assigned to this user
    $allTasks = $taskModel->findTasksByUser($sessionId);

    // attach project name and submission to each task
    foreach ($allTasks as &$t) {
        $p = $projectModel->findProjectById($t['project_id']);
        $t['project_title'] = $p['title'] ?? '—';
        $subs = $submissionModel->findTaskSubmissionsByTask($t['id']);
        $t['submission'] = !empty($subs) ? $subs[0] : null;
    }
    unset($t);

    // group by status
    $kanban = [
        'open'        => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'open')),
        'in_progress' => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'in_progress')),
        'submitted'   => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'submitted')),
        'done'        => array_values(array_filter($allTasks, fn($t) => $t['status'] === 'done')),
    ];

    // get user projects
    $memberships  = $projectMemberModel->findProjectsByMember($sessionId);
    $userProjects = [];
    foreach ($memberships as $m) {
        $p = $projectModel->findProjectById($m['project_id']);
        if ($p) $userProjects[] = $p;
    }

    require_once __DIR__ . '/../views/user/kanban.php';
} elseif ($action === 'create') {

    // --------------------------------------------------
    // Create User
    // --------------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === "POST") {

        $username = trim($_POST["username"] ?? '');
        $email = trim($_POST["email"] ?? '');
        $password = trim($_POST["password"] ?? '');
        $role = isset($_POST["role"]) ? trim($_POST["role"]) : 'user';

        if (empty($username) || empty($email) || empty($password)) {
            die("Missing required fields");
        }

        $userModel->createUser(
            $username,
            $password,
            $email,
            $role
        );

        header('Location: userController.php?action=list');
        exit();
    }

    require_once "../views/admin/users/create.php";
} elseif ($action === "update") {

    // --------------------------------------------------
    // Update User
    // --------------------------------------------------

    $id = $_GET["id"] ?? null;
    if (!$id) {
        die("User Id is Required");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);

        // if the password empty send null
        $password = !empty($password) ? $password : null;

        $userModel->updateUser(
            $id,
            $username,
            $email,
            $role,
            $password
        );

        header('Location: userController.php?action=list');
        exit();
    }
    $user = $userModel->findUserById($id);
    require_once '../views/admin/users/edit.php';
} elseif ($action === 'delete') {

    // --------------------------------------------------
    // Delete User
    // --------------------------------------------------

    $id = $_GET["id"] ?? null;

    if (!$id) {
        die("User ID is required");
    }

    $userModel->deleteUser($id);

    header("Location: userController.php?action=list");
    exit();
} elseif ($action === "searchByEmail") {
    // --------------------------------------------------
    // Find User By Email
    // --------------------------------------------------

    $email = $_GET["email"] ?? "";

    $user = $userModel->findUserByEmail($email);

    require_once "../views/admin/users/search.php";
} elseif ($action === "searchByUsername") {
    // --------------------------------------------------
    // Find User By Username
    // --------------------------------------------------

    $username = $_GET["username"] ?? "";

    $user = $userModel->findUserByUsername($username);

    require_once "../views/admin/users/search.php";
} elseif ($action === "findUsersByRole") {

    // --------------------------------------------------
    // FILTER USERS BY ROLE
    // --------------------------------------------------

    $role = $_GET["role"] ?? "";

    // if empty, just load all users instead
    if (empty($role)) {
        $users = $userModel->findAllUsers();
    } else {
        $users = $userModel->findUsersByRole($role);
    }

    require_once "../views/admin/users/list.php";
} else {
    die("Invalid Action");
}

?>