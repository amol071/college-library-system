<?php
session_start();
include '../../includes/header.php';

// Only Admins can access this page
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

require_once '../../config/db.php';

// Fetch all users
$stmt = $pdo->query("SELECT UserID, FullName, Email, Role, IsActive, CreatedOn FROM users ORDER BY CreatedOn DESC");
$users = $stmt->fetchAll();
?>

<div class="container mt-4">

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>


    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manage Users</h2>
        <a href="add_user.php" class="btn btn-success">Add New User</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Created On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users): ?>
                <?php foreach ($users as $index => $user): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($user['FullName']) ?></td>
                    <td><?= htmlspecialchars($user['Email']) ?></td>
                    <td><span class="badge bg-primary"><?= $user['Role'] ?></span></td>
                    <td>
                        <?php if ($user['IsActive']): ?>
                        <a href="toggle_user_status.php?id=<?= $user['UserID'] ?>"
                            class="badge bg-success text-decoration-none" title="Click to deactivate">Yes</a>
                        <?php else: ?>
                        <a href="toggle_user_status.php?id=<?= $user['UserID'] ?>"
                            class="badge bg-secondary text-decoration-none" title="Click to activate">No</a>
                        <?php endif; ?>
                    </td>

                    <td><?= date('d M Y, h:i A', strtotime($user['CreatedOn'])) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['UserID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_user.php?id=<?= $user['UserID'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No users found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>