<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/college-library-system/assets/css/style.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user'])): ?>
                    <?php $role = $_SESSION['user']['Role']; ?>

                    <?php if ($role === 'Admin'): ?>
                    <!-- Admin Menus -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Admin Panel
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item"
                                    href="/college-library-system/views/admin/dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item"
                                    href="/college-library-system/views/admin/manage_users.php">Manage Users</a></li>
                            <li><a class="dropdown-item"
                                    href="/college-library-system/views/admin/manage_books.php">Manage Books</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($role === 'Librarian' || $role === 'Admin'): ?>
                    <!-- Librarian Menus -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="librarianDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Librarian
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="librarianDropdown">
                            <li><a class="dropdown-item"
                                    href="/college-library-system/views/librarian/dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="/college-library-system/views/librarian/books.php">Manage
                                    Books</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($role === 'Student' || $role === 'Admin'): ?>
                    <!-- Student Menus -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="studentDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Student
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="studentDropdown">
                            <li><a class="dropdown-item"
                                    href="/college-library-system/views/student/dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="/college-library-system/views/student/my_books.php">My
                                    Books</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item">
                        <span class="nav-link text-light">Welcome,
                            <?= htmlspecialchars($_SESSION['user']['FullName']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/college-library-system/logout.php">Logout</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/college-library-system/login.php">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>