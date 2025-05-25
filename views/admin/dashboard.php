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

// Pagination logic
$studentsPerPage = 3;
$booksPerPage = 3;

$studentPage = isset($_GET['sp']) ? (int)$_GET['sp'] : 1;
$bookPage = isset($_GET['bp']) ? (int)$_GET['bp'] : 1;

$studentOffset = ($studentPage - 1) * $studentsPerPage;
$bookOffset = ($bookPage - 1) * $booksPerPage;

// Fetch paginated students
$totalStudentRows = $pdo->query("SELECT COUNT(*) FROM users WHERE Role = 'Student' AND IsActive = 1")->fetchColumn();
$totalStudentPages = ceil($totalStudentRows / $studentsPerPage);

$stmt = $pdo->prepare("
    SELECT u.UserID, u.FullName, u.Email, d.RollNumber, d.Department, d.Year
    FROM users u
    LEFT JOIN student_details d ON u.UserID = d.UserID
    WHERE u.Role = 'Student' AND u.IsActive = 1
    ORDER BY u.FullName
    LIMIT $studentsPerPage OFFSET $studentOffset
");
$stmt->execute();
$students = $stmt->fetchAll();

// Fetch paginated books
$totalBookRows = $pdo->query("SELECT COUNT(*) FROM books WHERE IsActive = 1")->fetchColumn();
$totalBookPages = ceil($totalBookRows / $booksPerPage);

$stmt = $pdo->prepare("
    SELECT b.BookID, b.Title, b.Author, b.Quantity,
    (SELECT COUNT(*) FROM issued_books i WHERE i.BookID = b.BookID AND i.IsActive = 1 AND i.ReturnDate IS NULL) AS Issued
    FROM books b
    WHERE b.IsActive = 1
    ORDER BY b.Title
    LIMIT $booksPerPage OFFSET $bookOffset
");
$stmt->execute();
$books = $stmt->fetchAll();
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

    <!-- STUDENT TABLE -->
    <div class="card p-4 mb-4">
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
                        <td><?= $studentOffset + $index + 1 ?></td>
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
        <!-- Student Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalStudentPages; $i++): ?>
                <li class="page-item <?= $i == $studentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?sp=<?= $i ?>&bp=<?= $bookPage ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- BOOK TABLE -->
    <div class="card p-4">
        <h4 class="mb-3">Book Details</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Quantity</th>
                        <th>Issued</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $index => $book): ?>
                    <tr>
                        <td><?= $bookOffset + $index + 1 ?></td>
                        <td><?= $book['BookID'] ?></td>
                        <td><?= htmlspecialchars($book['Title']) ?></td>
                        <td><?= htmlspecialchars($book['Author']) ?></td>
                        <td><?= $book['Quantity'] ?></td>
                        <td><?= $book['Issued'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($books) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center">No book data found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Book Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalBookPages; $i++): ?>
                <li class="page-item <?= $i == $bookPage ? 'active' : '' ?>">
                    <a class="page-link" href="?sp=<?= $studentPage ?>&bp=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>