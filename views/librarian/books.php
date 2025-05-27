<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Librarian') {
    header("Location: /college-library-system/login.php");
    exit;
}

include '../../includes/header.php';
require_once '../../config/db.php';

// Fetch all active books with issued count
$stmt = $pdo->query("
    SELECT 
        b.BookID,
        b.Title,
        b.Author,
        b.Quantity,
        (SELECT COUNT(*) FROM issued_books i WHERE i.BookID = b.BookID AND i.IsActive = 1 AND i.ReturnDate IS NULL) AS Issued
    FROM books b
    WHERE b.IsActive = 1
    ORDER BY b.Title ASC
");
$books = $stmt->fetchAll();

// Fetch issued book details
$stmt = $pdo->query("
    SELECT 
        i.IssueID,
        b.Title AS BookTitle,
        u.FullName AS StudentName,
        i.IssueDate,
        i.DueDate
    FROM issued_books i
    JOIN books b ON b.BookID = i.BookID
    JOIN users u ON u.UserID = i.StudentID
    WHERE i.IsActive = 1 AND i.ReturnDate IS NULL
    ORDER BY i.IssueDate DESC
");
$issued = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>Library Book Inventory</h2>
    <p class="text-muted">List of available books</p>

    <!-- Search box -->
    <div class="mb-3">
        <input type="text" id="bookSearch" class="form-control" placeholder="Search by title or author...">
    </div>

    <div class="table-responsive">
        <table id="bookTable" class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Total Quantity</th>
                    <th>Issued</th>
                    <th>Available</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books): ?>
                <?php foreach ($books as $index => $book): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $book['BookID'] ?></td>
                    <td><?= htmlspecialchars($book['Title']) ?></td>
                    <td><?= htmlspecialchars($book['Author']) ?></td>
                    <td><?= $book['Quantity'] ?></td>
                    <td><?= $book['Issued'] ?></td>
                    <td><?= max(0, $book['Quantity'] - $book['Issued']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No books found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Issued Book Details -->
    <div class="card mt-5 p-4 shadow-sm">
        <h4 class="mb-3">Books Currently Issued</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Book Title</th>
                        <th>Issued To</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($issued): ?>
                    <?php foreach ($issued as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['BookTitle']) ?></td>
                        <td><?= htmlspecialchars($row['StudentName']) ?></td>
                        <td><?= date('d M Y', strtotime($row['IssueDate'])) ?></td>
                        <td><?= date('d M Y', strtotime($row['DueDate'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No active book issues.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Simple JS filter -->
<script>
document.getElementById('bookSearch').addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    const rows = document.querySelectorAll('#bookTable tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>

<?php include '../../includes/footer.php'; ?>