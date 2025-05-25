<?php
session_start();

// Only Admins allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

require_once '../../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: manage_users.php");
    exit;
}

$userId = intval($_GET['id']);
$adminId = $_SESSION['user']['UserID'];

// Prevent self-delete
if ($userId == $adminId) {
    $_SESSION['error'] = "You cannot delete your own account.";
    header("Location: manage_users.php");
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE UserID = ? AND IsActive = 1");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found or already deleted.";
    header("Location: manage_users.php");
    exit;
}

// Store user name before deletion
$deletedName = $user['FullName'];

// Soft delete
$stmt = $pdo->prepare("UPDATE users SET IsActive = 0, ModifiedBy = ?, ModifiedOn = NOW() WHERE UserID = ?");
$stmt->execute([$adminId, $userId]);

$_SESSION['success'] = "User <strong>" . htmlspecialchars($deletedName) . "</strong> was deleted successfully.";
header("Location: manage_users.php");
exit;