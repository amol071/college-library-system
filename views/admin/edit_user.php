<?php
session_start();
include '../../includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
    header("Location: /college-library-system/login.php");
    exit;
}

require_once '../../config/db.php';

$success = "";
$error = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_users.php");
    exit;
}

$userId = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE UserID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $error = "User not found.";
}

// Fetch role-specific detail if needed
$studentDetail = [];
$librarianDetail = [];

if ($user['Role'] === 'Student') {
    $stmt = $pdo->prepare("SELECT * FROM student_details WHERE UserID = ?");
    $stmt->execute([$userId]);
    $studentDetail = $stmt->fetch() ?: [];
} elseif ($user['Role'] === 'Librarian') {
    $stmt = $pdo->prepare("SELECT * FROM librarian_details WHERE UserID = ?");
    $stmt->execute([$userId]);
    $librarianDetail = $stmt->fetch() ?: [];
}

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $modifiedBy = $_SESSION['user']['UserID'];

    if (empty($fullname) || empty($email) || empty($role)) {
        $error = "Full name, email, and role are required.";
    } else {
        // Email duplication check
        $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = ? AND UserID != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            $error = "Email already in use.";
        } else {
            // Update base user table
            $query = "UPDATE users SET FullName = ?, Email = ?, Role = ?, ModifiedBy = ?, ModifiedOn = NOW()";
            $params = [$fullname, $email, $role, $modifiedBy];

            if (!empty($password)) {
                $query .= ", Password = ?";
                $params[] = $password;
            }

            $query .= " WHERE UserID = ?";
            $params[] = $userId;

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            // Clean up old details if role changed
            if ($user['Role'] !== $role) {
                $pdo->prepare("DELETE FROM student_details WHERE UserID = ?")->execute([$userId]);
                $pdo->prepare("DELETE FROM librarian_details WHERE UserID = ?")->execute([$userId]);
            }

            // Update or insert detail based on role
            if ($role === 'Student') {
                $roll = $_POST['roll'] ?? '';
                $dept = $_POST['department'] ?? '';
                $year = $_POST['year'] ?? '';

                $stmt = $pdo->prepare("SELECT * FROM student_details WHERE UserID = ?");
                $stmt->execute([$userId]);
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare("UPDATE student_details SET RollNumber = ?, Department = ?, Year = ?, ModifiedBy = ?, ModifiedOn = NOW() WHERE UserID = ?");
                    $stmt->execute([$roll, $dept, $year, $modifiedBy, $userId]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO student_details (UserID, RollNumber, Department, Year, CreatedBy) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$userId, $roll, $dept, $year, $modifiedBy]);
                }
            } elseif ($role === 'Librarian') {
                $empid = $_POST['employee_id'] ?? '';
                $qualification = $_POST['qualification'] ?? '';
                $experience = $_POST['experience'] ?? 0;

                $stmt = $pdo->prepare("SELECT * FROM librarian_details WHERE UserID = ?");
                $stmt->execute([$userId]);
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare("UPDATE librarian_details SET EmployeeID = ?, Qualification = ?, ExperienceYears = ?, ModifiedBy = ?, ModifiedOn = NOW() WHERE UserID = ?");
                    $stmt->execute([$empid, $qualification, $experience, $modifiedBy, $userId]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO librarian_details (UserID, EmployeeID, Qualification, ExperienceYears, CreatedBy) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$userId, $empid, $qualification, $experience, $modifiedBy]);
                }
            }

            $success = "User updated successfully.";

            // Refresh user + details
            $stmt = $pdo->prepare("SELECT * FROM users WHERE UserID = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        }
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Edit User</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['FullName']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['Email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep unchanged)</label>
                        <input type="text" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="Admin" <?= $user['Role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="Librarian" <?= $user['Role'] === 'Librarian' ? 'selected' : '' ?>>Librarian</option>
                            <option value="Student" <?= $user['Role'] === 'Student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    </div>

                    <!-- Student Fields -->
                    <div id="student-fields" style="display: none;">
                        <hr>
                        <h5>Student Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll" class="form-control" value="<?= $studentDetail['RollNumber'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control" value="<?= $studentDetail['Department'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="<?= $studentDetail['Year'] ?? '' ?>" min="1" max="5">
                        </div>
                    </div>

                    <!-- Librarian Fields -->
                    <div id="librarian-fields" style="display: none;">
                        <hr>
                        <h5>Librarian Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" value="<?= $librarianDetail['EmployeeID'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control" value="<?= $librarianDetail['Qualification'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Years of Experience</label>
                            <input type="number" name="experience" class="form-control" value="<?= $librarianDetail['ExperienceYears'] ?? '' ?>" min="0" max="50">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="manage_users.php" class="btn btn-link">← Back to User Management</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields(role) {
    document.getElementById('student-fields').style.display = (role === 'Student') ? 'block' : 'none';
    document.getElementById('librarian-fields').style.display = (role === 'Librarian') ? 'block' : 'none';
}

const roleSelect = document.getElementById('role');
toggleFields(roleSelect.value);
roleSelect.addEventListener('change', function() {
    toggleFields(this.value);
});
</script>

<?php include '../../includes/footer.php'; ?>
