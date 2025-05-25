<?php
session_start();
include '../../includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

require_once '../../config/db.php';

// Fetch all books
$stmt = $pdo->query("
    SELECT b.BookID, b.Title, b.Author, b.Quantity, b.IsActive,
    (SELECT COUNT(*) FROM issued_books i WHERE i.BookID = b.BookID AND i.IsActive = 1 AND i.ReturnDate IS NULL) AS Issued
    FROM books b
    ORDER BY b.CreatedOn DESC
");
$books = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manage Books</h2>
        <a href="add_book.php" class="btn btn-success">Add New Book</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Quantity</th>
                    <th>Issued</th>
                    <th>Active</th>
                    <th>Actions</th>
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
                    <td>
                        <?php if ($book['IsActive']): ?>
                        <a href="toggle_book_status.php?id=<?= $book['BookID'] ?>"
                            class="badge bg-success text-decoration-none">Yes</a>
                        <?php else: ?>
                        <a href="toggle_book_status.php?id=<?= $book['BookID'] ?>"
                            class="badge bg-secondary text-decoration-none">No</a>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="edit_book.php?id=<?= $book['BookID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_book.php?id=<?= $book['BookID'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No books found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>