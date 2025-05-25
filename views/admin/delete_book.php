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

$bookId = $_GET['id'];
$modifiedBy = $_SESSION['user']['UserID'];

$stmt = $pdo->prepare("SELECT Title FROM books WHERE BookID = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    $_SESSION['error'] = "Book not found.";
} else {
    $stmt = $pdo->prepare("UPDATE books SET IsActive = 0, ModifiedBy = ?, ModifiedOn = NOW() WHERE BookID = ?");
    $stmt->execute([$modifiedBy, $bookId]);
    $_SESSION['success'] = "Book <strong>" . htmlspecialchars($book['Title']) . "</strong> deleted successfully.";
}

header("Location: manage_books.php");
exit;
