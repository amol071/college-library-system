<?php
session_start();

// ✅ Move access control check BEFORE output
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Librarian') {
    header("Location: /college-library-system/login.php");
    exit;
}

include '../../includes/header.php';
require_once '../../config/db.php';

// Cards
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books WHERE IsActive = 1")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE Role = 'Student' AND IsActive = 1")->fetchColumn();
$currentlyIssued = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE ReturnDate IS NULL AND IsActive = 1")->fetchColumn();
$overdueBooks = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE DueDate < CURDATE() AND ReturnDate IS NULL AND IsActive = 1")->fetchColumn();

// Recent issued books
$stmt = $pdo->query("
    SELECT i.IssueID, b.Title, u.FullName, i.IssueDate, i.DueDate
    FROM issued_books i
    JOIN books b ON b.BookID = i.BookID
    JOIN users u ON u.UserID = i.StudentID
    WHERE i.IsActive = 1
    ORDER BY i.IssueDate DESC
    LIMIT 5
");
$recentIssues = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['FullName']) ?></h2>
    <p class="lead">Librarian Dashboard</p>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h6>Total Books</h6>
                <h2><?= $totalBooks ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h6>Total Students</h6>
                <h2><?= $totalStudents ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h6>Issued Books</h6>
                <h2><?= $currentlyIssued ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h6>Overdue Books</h6>
                <h2><?= $overdueBooks ?></h2>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h5 class="mb-3">Recently Issued Books</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Book</th>
                        <th>Issued To</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentIssues as $index => $issue): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($issue['Title']) ?></td>
                        <td><?= htmlspecialchars($issue['FullName']) ?></td>
                        <td><?= date('d M Y', strtotime($issue['IssueDate'])) ?></td>
                        <td><?= date('d M Y', strtotime($issue['DueDate'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($recentIssues) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center">No recent issues found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>