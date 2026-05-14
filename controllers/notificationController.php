<?php
session_start();
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Notification.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false]);
    exit();
}

$notificationModel = new Notification($pdo);
$action = $_GET['action'] ?? '';
$userId = $_SESSION['user']['id'];

if ($action === 'markAllRead') {
    $notificationModel->markAllRead($userId);
    echo json_encode(['success' => true]);
    exit();
}
?>