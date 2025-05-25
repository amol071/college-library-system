<?php
session_start();
session_unset();
session_destroy();

// Use a redirect delay
header("Refresh: 3; url=login.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Logged Out</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f8f9fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .card {
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>
    <div class="card text-center">
        <h2 class="text-success mb-3">Logout Successful</h2>
        <p class="mb-0">You have been logged out.</p>
        <p class="text-muted">Redirecting to login page in 3 seconds...</p>
        <a href="login.php" class="btn btn-primary mt-3">Go to Login Now</a>
    </div>
</body>

</html>