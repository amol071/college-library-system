<?php
session_start();
include '../../includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

require_once '../../config/db.php';

// Dashboard stats
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books WHERE IsActive = 1")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE Role = 'Student' AND IsActive = 1")->fetchColumn();
$totalLibrarians = $pdo->query("SELECT COUNT(*) FROM users WHERE Role = 'Librarian' AND IsActive = 1")->fetchColumn();
$totalDefaulters = $pdo->query("
    SELECT COUNT(DISTINCT StudentID)
    FROM issued_books
    WHERE DueDate < CURDATE() AND ReturnDate IS NULL AND IsActive = 1
")->fetchColumn();

// Fetch student list with details
$stmt = $pdo->query("
    SELECT u.UserID, u.FullName, u.Email, d.RollNumber, d.Department, d.Year
    FROM users u
    LEFT JOIN student_details d ON u.UserID = d.UserID
    WHERE u.Role = 'Student' AND u.IsActive = 1
    ORDER BY u.FullName
");
$students = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['FullName']) ?></h2>
    <p class="lead">Admin Dashboard</p>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h5>Total Books</h5>
                <h2><?= $totalBooks ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h5>Total Students</h5>
                <h2><?= $totalStudents ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h5>Total Librarians</h5>
                <h2><?= $totalLibrarians ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center p-3 shadow">
                <h5>Defaulters</h5>
                <h2><?= $totalDefaulters ?></h2>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h4 class="mb-3">Student Details</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Roll Number</th>
                        <th>Department</th>
                        <th>Year</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($student['FullName']) ?></td>
                        <td><?= htmlspecialchars($student['Email']) ?></td>
                        <td><?= htmlspecialchars($student['RollNumber'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($student['Department'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($student['Year'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($students) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center">No student data found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>