<?php
session_start();
require_once '../../config/db.php';

// Only Admins allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: manage_users.php");
    exit;
}

$userId = intval($_GET['id']);
$adminId = $_SESSION['user']['UserID'];

// Prevent self-toggle
if ($userId == $adminId) {
    $_SESSION['error'] = "You cannot toggle your own status.";
    header("Location: manage_users.php");
    exit;
}

// Get current status
$stmt = $pdo->prepare("SELECT FullName, IsActive FROM users WHERE UserID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: manage_users.php");
    exit;
}

$newStatus = $user['IsActive'] ? 0 : 1;

// Update status
$stmt = $pdo->prepare("UPDATE users SET IsActive = ?, ModifiedBy = ?, ModifiedOn = NOW() WHERE UserID = ?");
$stmt->execute([$newStatus, $adminId, $userId]);

$_SESSION['success'] = "User <strong>" . htmlspecialchars($user['FullName']) . "</strong> has been " . ($newStatus ? "activated" : "deactivated") . ".";
header("Location: manage_users.php");
exit;
