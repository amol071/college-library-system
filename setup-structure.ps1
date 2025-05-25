# Define root directory in XAMPP's htdocs
$root = ""

# Define all folders to create
$folders = @(
    "$root/assets/css",
    "$root/assets/js",
    "$root/assets/images",
    "$root/assets/uploads",
    "$root/config",
    "$root/includes",
    "$root/controllers",
    "$root/models",
    "$root/views/admin",
    "$root/views/librarian",
    "$root/views/student"
)

# Create all directories
foreach ($folder in $folders) {
    New-Item -ItemType Directory -Force -Path $folder | Out-Null
}

# Define all files to create
$files = @(
    "$root/config/db.php",
    "$root/includes/header.php",
    "$root/includes/footer.php",
    "$root/includes/sidebar.php",
    "$root/controllers/authController.php",
    "$root/controllers/bookController.php",
    "$root/controllers/issueController.php",
    "$root/controllers/paymentController.php",
    "$root/models/User.php",
    "$root/models/Book.php",
    "$root/models/Issue.php",
    "$root/models/Payment.php",
    "$root/views/admin/dashboard.php",
    "$root/views/admin/manage_users.php",
    "$root/views/librarian/dashboard.php",
    "$root/views/librarian/books.php",
    "$root/views/student/dashboard.php",
    "$root/views/student/my_books.php",
    "$root/login.php",
    "$root/logout.php",
    "$root/index.php",
    "$root/.htaccess",
    "$root/README.md"
)

# Create all files
foreach ($file in $files) {
    New-Item -ItemType File -Force -Path $file | Out-Null
}

Write-Host "✅ Project structure created in 'C:\xampp\htdocs\college-library-system'"
