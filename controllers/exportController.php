<?php
session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/Project.php";
require_once __DIR__ . "/../models/ProjectMember.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Task.php";
require_once __DIR__ . "/../models/TaskSubmission.php";

// Admin-only export endpoint.
requireAdmin();

// Keep the controller aligned with the app's action-based routing.
$action = $_GET['action'] ?? 'download';
$exportType = $_POST['export_type'] ?? '';
$format = strtolower(trim($_POST['format'] ?? 'csv'));

$projectModel = new Project($pdo);
$projectMemberModel = new ProjectMember($pdo);
$userModel = new User($pdo);
$taskModel = new Task($pdo);
$submissionModel = new TaskSubmission($pdo);

// Stream a CSV response directly to the browser.
function exportCsvFile(string $filename, array $headers, array $rows): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    // UTF-8 BOM helps Excel open accented characters correctly.
    fwrite($output, "\xEF\xBB\xBF");
    // Use semicolon-delimited output for better Excel compatibility on Windows.
    fputcsv($output, $headers, ';');

    foreach ($rows as $row) {
        fputcsv($output, $row, ';');
    }

    fclose($output);
    exit();
}

$fileStamp = date('Y-m-d_His');

if ($action !== 'download') {
    header('Location: ../views/admin/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/admin/dashboard.php');
    exit();
}

if ($format !== 'csv') {
    die('Unsupported export format');
}

switch ($exportType) {
    case 'projects':
        // Export project records.
        $projects = $projectModel->findAllProjects();
        $rows = [];
        foreach ($projects as $project) {
            $rows[] = [
                $project['id'] ?? '',
                $project['title'] ?? '',
                $project['description'] ?? '',
                $project['created_at'] ?? '',
            ];
        }
        exportCsvFile("projects-export-$fileStamp.csv", ['ID', 'Title', 'Description', 'Created At'], $rows);

    case 'users':
        // Export user accounts.
        $users = $userModel->findAllUsers();
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user['id'] ?? '',
                $user['username'] ?? '',
                $user['email'] ?? '',
                $user['role'] ?? '',
                $user['created_at'] ?? '',
            ];
        }
        exportCsvFile("users-export-$fileStamp.csv", ['ID', 'Username', 'Email', 'Role', 'Created At'], $rows);

    case 'tasks':
        // Export tasks with project and assignee names resolved.
        $tasks = $taskModel->findAllTasks();
        $projects = $projectModel->findAllProjects();
        $users = $userModel->findAllUsers();

        // Build lookup tables so the CSV can show readable names instead of IDs.
        $projectMap = [];
        foreach ($projects as $project) {
            $projectMap[$project['id']] = $project['title'] ?? 'Unknown';
        }

        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user['id']] = $user['username'] ?? 'Unknown';
        }

        $rows = [];
        foreach ($tasks as $task) {
            $rows[] = [
                $task['id'] ?? '',
                $task['title'] ?? '',
                $task['description'] ?? '',
                $task['status'] ?? '',
                $task['complexity'] ?? '',
                $projectMap[$task['project_id']] ?? 'Unknown',
                !empty($task['assigned_to']) ? ($userMap[$task['assigned_to']] ?? 'Unknown') : 'Unassigned',
                $task['created_at'] ?? '',
            ];
        }
        exportCsvFile(
            "tasks-export-$fileStamp.csv",
            ['ID', 'Title', 'Description', 'Status', 'Complexity', 'Project', 'Assigned To', 'Created At'],
            $rows
        );

    case 'submissions':
        // Export task submissions with the related task title.
        $submissions = $submissionModel->findAllTaskSubmissions();
        $tasks = $taskModel->findAllTasks();
        $taskMap = [];
        foreach ($tasks as $task) {
            $taskMap[$task['id']] = $task['title'] ?? 'Unknown';
        }

        $rows = [];
        foreach ($submissions as $submission) {
            $rows[] = [
                $submission['id'] ?? '',
                $submission['task_id'] ?? '',
                $taskMap[$submission['task_id']] ?? 'Unknown',
                $submission['git_link'] ?? '',
                $submission['message'] ?? '',
                $submission['status'] ?? '',
                $submission['created_at'] ?? '',
            ];
        }
        exportCsvFile(
            "submissions-export-$fileStamp.csv",
            ['ID', 'Task ID', 'Task Title', 'Git Link', 'Message', 'Status', 'Created At'],
            $rows
        );

    case 'members':
        // Export project membership with joined project and user details.
        $members = $pdo->query(
            "SELECT pm.project_id, pm.user_id, pm.role, pm.joined_at, p.title AS project_title, u.username AS username, u.email AS email
             FROM project_members pm
             LEFT JOIN projects p ON p.id = pm.project_id
             LEFT JOIN users u ON u.id = pm.user_id
             ORDER BY pm.joined_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $rows = [];
        foreach ($members as $member) {
            $rows[] = [
                $member['project_id'] ?? '',
                $member['project_title'] ?? '',
                $member['user_id'] ?? '',
                $member['username'] ?? '',
                $member['email'] ?? '',
                $member['role'] ?? '',
                $member['joined_at'] ?? '',
            ];
        }
        exportCsvFile(
            "project-members-export-$fileStamp.csv",
            ['Project ID', 'Project Title', 'User ID', 'Username', 'Email', 'Role', 'Joined At'],
            $rows
        );

    default:
        die('Invalid export selection');
}