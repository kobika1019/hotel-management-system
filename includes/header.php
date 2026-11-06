<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect Admin Directory
$is_admin = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;

$base_path = $is_admin ? '../' : './';

// Current Page
$current_page = basename($_SERVER['SCRIPT_NAME'] ?? '');

// Session Information
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? $_SESSION['full_name'] ?? null;
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

// Safe Output
$safe_user_name = htmlspecialchars((string)($user_name ?? ''), ENT_QUOTES, 'UTF-8');
$safe_user_role = htmlspecialchars((string)($user_role ?? ''), ENT_QUOTES, 'UTF-8');

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#081426">

    <meta name="description" content="Blue Moon Hotel - Experience exceptional luxury, comfort and unforgettable hospitality.">

    <title>Blue Moon Hotel | Luxury & Comfort</title>

    <!-- Bootstrap 5 CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/style.css">

</head>

<body>

<!-- =====================================
     LUXURY HOTEL NAVIGATION
===================================== -->

<nav class="navbar navbar-expand-xl navbar-dark bg-navy-main sticky-top shadow-sm luxury-navbar">

    <div class="container-fluid px-lg-5 px-3">

        <!-- HOTEL BRAND -->

        <a
            class="navbar-brand luxury-brand d-flex align-items-center gap-3"
            href="<?php echo $base_path; ?>index.php"
        >

            <span class="brand-icon">
                <i class="fa-solid fa-moon"></i>
            </span>

            <span class="brand-text">

                <span class="brand-name">
                    BLUE MOON
                </span>

                <span class="brand-subtitle">
                    LUXURY HOTEL & RESORT
                </span>

            </span>

        </a>


        <!-- MOBILE TOGGLE -->

        <button
            class="navbar-toggler luxury-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <!-- NAVIGATION CONTENT -->

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- MAIN MENU -->

            <ul class="navbar-nav mx-auto luxury-nav">

                <!-- HOME -->

                <li class="nav-item">

                    <a
                        class="nav-link <?php echo (!$is_admin && $current_page === 'index.php') ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>index.php"
                    >
                        <i class="fa-solid fa-house"></i>
                        <span>Home</span>
                    </a>

                </li>


                <!-- ROOMS -->

                <li class="nav-item">

                    <a
                        class="nav-link <?php echo (!$is_admin && $current_page === 'rooms.php') ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>rooms.php"
                    >
                        <i class="fa-solid fa-bed"></i>
                        <span>Our Rooms</span>
                    </a>

                </li>


                <!-- CUSTOMER MENU -->

                <?php if ($user_id && $user_role === 'Customer'): ?>

                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo ($current_page === 'book.php') ? 'active' : ''; ?>"
                            href="<?php echo $base_path; ?>book.php"
                        >
                            <i class="fa-regular fa-calendar-plus"></i>
                            <span>Book Now</span>
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo ($current_page === 'my_bookings.php') ? 'active' : ''; ?>"
                            href="<?php echo $base_path; ?>my_bookings.php"
                        >
                            <i class="fa-solid fa-receipt"></i>
                            <span>My Bookings</span>
                        </a>

                    </li>

                <?php endif; ?>


                <!-- STAFF MENU -->

                <?php if ($user_id && $user_role === 'Staff'): ?>

                    <li class="nav-item">

                        <a
                            class="nav-link staff-link <?php echo ($current_page === 'staff_dashboard.php') ? 'active' : ''; ?>"
                            href="<?php echo $base_path; ?>staff_dashboard.php"
                        >
                            <i class="fa-solid fa-clipboard-user"></i>
                            <span>Staff Portal</span>
                        </a>

                    </li>

                <?php endif; ?>


                <!-- ADMIN MENU -->

                <?php if ($user_id && $user_role === 'Admin'): ?>

                    <li class="nav-item">

                        <a
                            class="nav-link admin-link <?php echo ($is_admin && $current_page === 'index.php') ? 'active' : ''; ?>"
                            href="<?php echo $base_path; ?>admin/index.php"
                        >
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Dashboard</span>
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo ($is_admin && $current_page === 'rooms.php') ? 'active' : ''; ?>"
                            href="<?php echo $base_path; ?>admin/rooms.php"
                        >
                            <i class="fa-solid fa-door-open"></i>
                            <span>Manage Rooms</span>
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo ($is_admin && $current_page === 'bookings.php') ? 'active' : ''; ?>"
                            href="<?php echo $base_path; ?>admin/bookings.php"
                        >
                            <i class="fa-solid fa-list-check"></i>
                            <span>Manage Bookings</span>
                        </a>

                    </li>

                <?php endif; ?>

            </ul>


            <!-- RIGHT SIDE USER ACTIONS -->

            <div class="navbar-actions d-flex align-items-center gap-3">

                <?php if ($user_id): ?>

                    <!-- USER PROFILE -->

                    <div class="nav-user-profile">

                        <div class="user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="user-details">

                            <span class="user-name">
                                <?php echo $safe_user_name; ?>
                            </span>

                            <span class="user-role">
                                <?php echo $safe_user_role; ?>
                            </span>

                        </div>

                    </div>


                    <!-- LOGOUT -->

                    <a
                        href="<?php echo $base_path; ?>logout.php"
                        class="btn btn-logout"
                    >

                        <i class="fa-solid fa-arrow-right-from-bracket"></i>

                        <span>Logout</span>

                    </a>


                <?php else: ?>

                    <!-- LOGIN -->

                    <a
                        href="<?php echo $base_path; ?>login.php"
                        class="btn btn-login"
                    >

                        <i class="fa-solid fa-right-to-bracket"></i>

                        <span>Login</span>

                    </a>


                    <!-- REGISTER -->

                    <a
                        href="<?php echo $base_path; ?>register.php"
                        class="btn btn-register"
                    >

                        <i class="fa-solid fa-user-plus"></i>

                        <span>Register</span>

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</nav>

<!-- END OF HEADER NAVIGATION -->