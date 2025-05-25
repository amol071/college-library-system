<?php
session_start();
include '../../includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

require_once '../../config/db.php';
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $quantity = (int) $_POST['quantity'];
    $createdBy = $_SESSION['user']['UserID'];

    if (!$title || !$author || $quantity < 1) {
        $error = "All fields are required and quantity must be at least 1.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO books (Title, Author, Quantity, CreatedBy) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $author, $quantity, $createdBy]);
        $_SESSION['success'] = "Book added successfully.";
        header("Location: manage_books.php");
        exit;
    }
}
?>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h3 class="mb-3 text-center">Add New Book</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Author</label>
                <input type="text" name="author" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success">Add Book</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="manage_books.php" class="btn btn-link">← Back to Manage Books</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
