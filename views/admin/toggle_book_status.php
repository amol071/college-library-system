<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

require_once '../../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid book ID.";
    header("Location: manage_books.php");
    exit;
}

$bookId = intval($_GET['id']);
$adminId = $_SESSION['user']['UserID'];

// Fetch current status and title
$stmt = $pdo->prepare("SELECT Title, IsActive FROM books WHERE BookID = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    $_SESSION['error'] = "Book not found.";
    header("Location: manage_books.php");
    exit;
}

$newStatus = $book['IsActive'] ? 0 : 1;

$stmt = $pdo->prepare("UPDATE books SET IsActive = ?, ModifiedBy = ?, ModifiedOn = NOW() WHERE BookID = ?");
$stmt->execute([$newStatus, $adminId, $bookId]);

$_SESSION['success'] = "Book <strong>" . htmlspecialchars($book['Title']) . "</strong> has been " . ($newStatus ? "activated" : "deactivated") . ".";

header("Location: manage_books.php");
exit;
