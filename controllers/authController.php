<?php

session_start();

require_once "../config/database.php";
require_once "../models/User.php";

$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Find user by username
    $user = $userModel->findUserByUsernameLogin($username);

    // Verify password
    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        header("Location: ../views/dashboard.php");
        exit();

    } else {

        $_SESSION['error'] = "Invalid credentials";

        header("Location: ../login.php");
        exit();
    }
}
?>