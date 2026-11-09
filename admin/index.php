
<?php

require_once '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session Information
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

// Admin Access Only
if (!$user_id || $user_role !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Dashboard Statistics
$roomCount = $pdo->query("
    SELECT COUNT(*) FROM rooms
")->fetchColumn();

$pendingCount = $pdo->query("
    SELECT COUNT(*) FROM bookings
    WHERE booking_status = 'Pending'
")->fetchColumn();

$confirmedCount = $pdo->query("
    SELECT COUNT(*) FROM bookings
    WHERE booking_status = 'Confirmed'
")->fetchColumn();

$customerCount = $pdo->query("
    SELECT COUNT(*) FROM users
    WHERE role = 'Customer'
")->fetchColumn();

$revenue = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM bookings
    WHERE booking_status = 'Confirmed'
")->fetchColumn();

include '../includes/header.php';

?>

<div class="container my-4 my-lg-5">

    <!-- DASHBOARD HEADING -->

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="fa-solid fa-gauge-high text-warning me-2"></i>
                Admin Dashboard
            </h3>

            <p class="text-muted mb-0">
                Welcome to Blue Moon Hotel Management System.
                Manage your hotel operations from here.
            </p>
        </div>

        <span class="badge bg-dark fs-6">
            <i class="fa-solid fa-user-shield me-1"></i>
            Administrator
        </span>

    </div>


    <!-- DASHBOARD STATISTICS -->

    <div class="row g-3 mb-4">

        <!-- TOTAL ROOMS -->

        <div class="col-6 col-lg-3">

            <div class="card bg-dark text-white border-0 shadow-sm h-100">

                <div class="card-body p-3 p-lg-4">

                    <i class="fa-solid fa-bed text-warning fs-3 mb-3"></i>

                    <p class="small fw-bold mb-1 text-warning">
                        TOTAL ROOMS
                    </p>

                    <h2 class="fw-bold mb-0">
                        <?php echo (int)$roomCount; ?>
                    </h2>

                </div>

            </div>

        </div>


        <!-- PENDING BOOKINGS -->

        <div class="col-6 col-lg-3">

            <div class="card bg-warning text-dark border-0 shadow-sm h-100">

                <div class="card-body p-3 p-lg-4">

                    <i class="fa-solid fa-clock fs-3 mb-3"></i>

                    <p class="small fw-bold mb-1">
                        PENDING APPROVALS
                    </p>

                    <h2 class="fw-bold mb-0">
                        <?php echo (int)$pendingCount; ?>
                    </h2>

                </div>

            </div>

        </div>


        <!-- CONFIRMED BOOKINGS -->

        <div class="col-6 col-lg-3">

            <div class="card bg-success text-white border-0 shadow-sm h-100">

                <div class="card-body p-3 p-lg-4">

                    <i class="fa-solid fa-circle-check fs-3 mb-3"></i>

                    <p class="small fw-bold mb-1">
                        CONFIRMED BOOKINGS
                    </p>

                    <h2 class="fw-bold mb-0">
                        <?php echo (int)$confirmedCount; ?>
                    </h2>

                </div>

            </div>

        </div>


        <!-- TOTAL CUSTOMERS -->

        <div class="col-6 col-lg-3">

            <div class="card bg-primary text-white border-0 shadow-sm h-100">

                <div class="card-body p-3 p-lg-4">

                    <i class="fa-solid fa-users fs-3 mb-3"></i>

                    <p class="small fw-bold mb-1">
                        TOTAL CUSTOMERS
                    </p>

                    <h2 class="fw-bold mb-0">
                        <?php echo (int)$customerCount; ?>
                    </h2>

                </div>

            </div>

        </div>

    </div>


    <!-- REVENUE SUMMARY -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4">

            <div class="d-flex align-items-center gap-3">

                <div class="bg-success-subtle text-success rounded p-3">
                    <i class="fa-solid fa-sack-dollar fs-2"></i>
                </div>

                <div>

                    <p class="text-muted fw-semibold mb-1">
                        TOTAL CONFIRMED BOOKING REVENUE
                    </p>

                    <h2 class="fw-bold text-success mb-0">
                        $<?php echo number_format((float)$revenue, 2); ?>
                    </h2>

                </div>

            </div>

            <p class="small text-muted mt-3 mb-0">
                Calculated from bookings with Confirmed status.
            </p>

        </div>

    </div>


    <!-- MANAGEMENT SHORTCUTS -->

    <h5 class="fw-bold mb-3">
        <i class="fa-solid fa-gear text-warning me-2"></i>
        Hotel Management
    </h5>

    <div class="row g-3">

        <!-- ROOM MANAGEMENT -->

        <div class="col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-4">

                    <i class="fa-solid fa-door-open text-primary fs-2 mb-3"></i>

                    <h5 class="fw-bold">
                        Room Management
                    </h5>

                    <p class="text-muted">
                        Add new rooms, update room details,
                        manage room information and delete rooms.
                    </p>

                    <a href="rooms.php" class="btn btn-primary">
                        <i class="fa-solid fa-bed me-2"></i>
                        Manage Rooms
                    </a>

                </div>

            </div>

        </div>


        <!-- BOOKING MANAGEMENT -->

        <div class="col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-4">

                    <i class="fa-solid fa-calendar-check text-warning fs-2 mb-3"></i>

                    <h5 class="fw-bold">
                        Booking Management
                    </h5>

                    <p class="text-muted">
                        View guest reservations, approve pending bookings
                        and manage booking statuses.
                    </p>

                    <a href="bookings.php" class="btn btn-warning">
                        <i class="fa-solid fa-list-check me-2"></i>
                        Manage Bookings
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>
