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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $createdBy = $_SESSION['user']['UserID'];

    // Validate required fields
    if (empty($fullname) || empty($email) || empty($password) || empty($role)) {
        $error = "All required fields must be filled.";
    } else {
        // Check for email duplication
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email already exists.";
        } else {
            // Insert into users
            $stmt = $pdo->prepare("INSERT INTO users (FullName, Email, Password, Role, CreatedBy) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$fullname, $email, $password, $role, $createdBy]);
            $userID = $pdo->lastInsertId();

            // Insert role-specific details
            if ($role === 'Student') {
                $roll = $_POST['roll'] ?? null;
                $dept = $_POST['department'] ?? null;
                $year = $_POST['year'] ?? null;

                $stmt = $pdo->prepare("INSERT INTO student_details (UserID, RollNumber, Department, Year, CreatedBy) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$userID, $roll, $dept, $year, $createdBy]);
            }

            if ($role === 'Librarian') {
                $empid = $_POST['employee_id'] ?? null;
                $qualification = $_POST['qualification'] ?? null;
                $exp = $_POST['experience'] ?? null;

                $stmt = $pdo->prepare("INSERT INTO librarian_details (UserID, EmployeeID, Qualification, ExperienceYears, CreatedBy) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$userID, $empid, $qualification, $exp, $createdBy]);
            }

            $success = "User created successfully.";
        }
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Add New User</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="" disabled selected>-- Select Role --</option>
                            <option value="Admin">Admin</option>
                            <option value="Librarian">Librarian</option>
                            <option value="Student">Student</option>
                        </select>
                    </div>

                    <!-- Student Details -->
                    <div id="student-fields" style="display:none;">
                        <hr>
                        <h5>Student Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" min="1" max="5">
                        </div>
                    </div>

                    <!-- Librarian Details -->
                    <div id="librarian-fields" style="display:none;">
                        <hr>
                        <h5>Librarian Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Years of Experience</label>
                            <input type="number" name="experience" class="form-control" min="0" max="50">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create User</button>
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
document.getElementById('role').addEventListener('change', function () {
    let role = this.value;
    document.getElementById('student-fields').style.display = (role === 'Student') ? 'block' : 'none';
    document.getElementById('librarian-fields').style.display = (role === 'Librarian') ? 'block' : 'none';
});
</script>

<?php include '../../includes/footer.php'; ?>
