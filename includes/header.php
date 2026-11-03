<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blue Moon Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-navy-main sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/index.php">
                <i class="fa-solid fa-moon text-gold fa-lg"></i>
                <span class="fs-4">Blue Moon <span class="text-gold fw-light">Hotel</span></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-lg-4">
                    <li class="nav-item"><a class="nav-link" href="/index.php"><i class="fa-solid fa-house me-1"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/rooms.php"><i class="fa-solid fa-bed me-1"></i> Our Rooms</a></li>
                    
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Customer'): ?>
                        <li class="nav-item"><a class="nav-link" href="/book.php"><i class="fa-solid fa-calendar-plus me-1"></i> Book Now</a></li>
                        <li class="nav-item"><a class="nav-link" href="/my_bookings.php"><i class="fa-solid fa-receipt me-1"></i> My Bookings</a></li>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Staff'): ?>
                        <li class="nav-item"><a class="nav-link text-gold fw-bold" href="/staff_dashboard.php"><i class="fa-solid fa-clipboard-user me-1"></i> Staff Portal</a></li>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
                        <li class="nav-item"><a class="nav-link text-gold fw-bold" href="/admin/index.php"><i class="fa-solid fa-gauge me-1"></i> Admin Panel</a></li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <div class="text-light text-end">
                            <span class="fw-bold text-gold"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            <span class="badge bg-secondary ms-1"><?= $_SESSION['user_role'] ?></span>
                        </div>
                        <a href="/logout.php" class="btn btn-outline-danger btn-sm px-3"><i class="fa-solid fa-power-off me-1"></i> Logout</a>
                    <?php else: ?>
                        <a href="/login.php" class="btn btn-outline-light btn-sm px-3 me-2"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</a>
                        <a href="/register.php" class="btn btn-gold btn-sm px-3"><i class="fa-solid fa-user-plus me-1"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>