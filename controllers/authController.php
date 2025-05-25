<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = ? AND IsActive = 1 LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $password === $user['Password']) { // NOTE: Replace with password_verify() if using hashing
        $_SESSION['user'] = $user;

        // Redirect based on role
        switch ($user['Role']) {
            case 'Admin':
                header("Location: ../views/admin/dashboard.php");
                break;
            case 'Librarian':
                header("Location: ../views/librarian/dashboard.php");
                break;
            case 'Student':
                header("Location: ../views/student/dashboard.php");
                break;
        }
        exit;
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: ../login.php");
        exit;
    }
}