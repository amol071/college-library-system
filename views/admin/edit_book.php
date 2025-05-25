<?php
session_start();
include '../../includes/header.php';

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
$stmt = $pdo->prepare("SELECT * FROM books WHERE BookID = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    $_SESSION['error'] = "Book not found.";
    header("Location: manage_books.php");
    exit;
}

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $quantity = (int) $_POST['quantity'];
    $modifiedBy = $_SESSION['user']['UserID'];

    if (!$title || !$author || $quantity < 1) {
        $error = "All fields are required and quantity must be valid.";
    } else {
        $stmt = $pdo->prepare("UPDATE books SET Title = ?, Author = ?, Quantity = ?, ModifiedBy = ?, ModifiedOn = NOW() WHERE BookID = ?");
        $stmt->execute([$title, $author, $quantity, $modifiedBy, $bookId]);
        $_SESSION['success'] = "Book updated successfully.";
        header("Location: manage_books.php");
        exit;
    }
}
?>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h3 class="mb-3 text-center">Edit Book</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($book['Title']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Author</label>
                <input type="text" name="author" value="<?= htmlspecialchars($book['Author']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" value="<?= $book['Quantity'] ?>" class="form-control" min="1" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Update Book</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="manage_books.php" class="btn btn-link">← Back to Manage Books</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
