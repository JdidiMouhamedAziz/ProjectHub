<?php
// session_start();
// require_once __DIR__ . "/../config/database.php";
// require_once __DIR__ . "/../config/auth.php";
// require_once __DIR__ . "/../models/ProjectMember.php";
// require_once __DIR__ . "/../models/Project.php";
// require_once __DIR__ . "/../models/User.php";

// requireLogin();

// $projectMemberModel = new ProjectMember($pdo);
// $projectModel       = new Project($pdo);
// $userModel          = new User($pdo);

// $action      = $_GET["action"] ?? 'list';
// $sessionRole = $_SESSION["user"]["role"];
// $sessionId   = $_SESSION["user"]["id"];

// // --------------------------------------------------
// // LIST members of a project
// // --------------------------------------------------
// if ($action === 'list') {

//     $project_id = $_GET['project_id'] ?? null;
//     if (!$project_id) die("Project ID is required");

//     $project = $projectModel->findProjectById($project_id);
//     $members = $projectMemberModel->findMembersByProject($project_id);

//     // attach user details
//     foreach ($members as &$m) {
//         $u = $userModel->findUserById($m['user_id']);
//         $m['username'] = $u['username'] ?? 'Unknown';
//         $m['email']    = $u['email']    ?? '';
//     }
//     unset($m);

//     // all users not already members (for add member form)
//     $allUsers      = $userModel->findAllUsers();
//     $memberUserIds = array_column($members, 'user_id');
//     $nonMembers    = array_filter($allUsers, fn($u) => !in_array($u['id'], $memberUserIds));

//     require_once __DIR__ . '/../views/admin/members/list.php';

// } elseif ($action === 'remove') {

//     if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
//         echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//         exit();
//     }

//     $project_id = $_GET['project_id'] ?? null;
//     $user_id    = $_GET['user_id']    ?? null;

//     if (!$project_id || !$user_id) {
//         echo json_encode(['success' => false, 'message' => 'Missing fields']);
//         exit();
//     }

//     $u = $userModel->findUserById($user_id);
//     $projectMemberModel->deleteProjectMember($project_id, $user_id);

//     echo json_encode([
//         'success'  => true,
//         'username' => $u['username'],
//         'role'     => $u['role'],
//     ]);
//     exit();

// // --------------------------------------------------
// // UPDATE role
// // --------------------------------------------------
// } elseif ($action === 'update') {

//     if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
//         die("Unauthorized");
//     }

//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         $project_id = intval($_POST['project_id'] ?? 0);
//         $user_id    = intval($_POST['user_id']    ?? 0);
//         $role       = trim($_POST['role']         ?? 'member');

//         if (!$project_id || !$user_id) die("Missing fields");

//         $projectMemberModel->updateProjectMember($project_id, $user_id, $role);
//         header("Location: projectMemberController.php?action=list&project_id=$project_id");
//         exit();
//     }

// // --------------------------------------------------
// // REMOVE member
// // --------------------------------------------------
// } elseif ($action === 'remove') {

//     if ($sessionRole !== 'admin' && $sessionRole !== 'manager') {
//         die("Unauthorized");
//     }

//     $project_id = $_GET['project_id'] ?? null;
//     $user_id    = $_GET['user_id']    ?? null;

//     if (!$project_id || !$user_id) die("Missing fields");

//     $projectMemberModel->deleteProjectMember($project_id, $user_id);
//     header("Location: projectMemberController.php?action=list&project_id=$project_id");
//     exit();

// } else {
//     die("Invalid action");
// }
?>