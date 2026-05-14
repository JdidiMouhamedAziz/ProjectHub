<?php
function requireLogin(){
    if(!isset($_SESSION["user"])){
        header("Location: ../login.php");
        exit();
    }
}

function requireAdmin(){
    if(!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin"){
        header("Location: ../views/dashboard.php");
        exit();
    }
}

function requireManager(){
    if(!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "manager"){
        header("Location: ../views/dashboard.php");
        exit();
    }
}
?>