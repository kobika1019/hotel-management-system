
<?php

require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get Session Information
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

// Staff Access Only
if (!$user_id || $user_role !== 'Staff') {
    header("Location: login.php");
    exit;
}

// Fetch All Guest Bookings
$stmt = $pdo->query("
    SELECT 
        b.*,
        u.full_name,
        r.room_number,
        rt.type_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN rooms r ON b.room_id = r.room_id
    JOIN room_types rt ON r.type_id = rt.type_id
    ORDER BY b.booking_id DESC
");

$bookings = $stmt->fetchAll();

// Booking Summary
$total_bookings = count($bookings);
$pending_bookings = 0;
$confirmed_bookings = 0;
$checked_out_bookings = 0;
$cancelled_bookings = 0;

foreach ($bookings as $b) {
    switch ($b['booking_status']) {
        case 'Pending':
            $pending_bookings++;
            break;

        case 'Confirmed':
            $confirmed_bookings++;
            break;

        case 'Checked-Out':
            $checked_out_bookings++;
            break;

        case 'Cancelled':
            $cancelled_bookings++;
            break;
    }
}

include 'includes/header.php';

?>

<div class="container my-4 my-lg-5">

    <!-- PAGE HEADING -->

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="fa-solid fa-clipboard-user text-warning me-2"></i>
                Front Desk & Staff Portal
            </h3>

            <p class="text-muted mb-0">
                View and monitor all guest bookings.
            </p>
        </div>

        <span class="badge bg-primary fs-6">
            Staff Dashboard
        </span>

    </div>


    <!-- BOOKING SUMMARY -->

    <div class="row g-3 mb-4">

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-calendar-check text-primary fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0"><?php echo $total_bookings; ?></h3>
                    <p class="text-muted mb-0">Total Bookings</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-clock text-warning fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0"><?php echo $pending_bookings; ?></h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-circle-check text-success fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0"><?php echo $confirmed_bookings; ?></h3>
                    <p class="text-muted mb-0">Confirmed</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-right-from-bracket text-secondary fs-4 mb-2"></i>
                    <h3 class="fw-bold mb-0"><?php echo $checked_out_bookings; ?></h3>
                    <p class="text-muted mb-0">Checked-Out</p>
                </div>
            </div>
        </div>

    </div>


    <!-- GUEST BOOKINGS TABLE -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 p-3">

            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-list-check text-warning me-2"></i>
                Guest Bookings Overview
            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-dark">

                        <tr>
                            <th class="p-3">Booking ID</th>
                            <th>Guest Name</th>
                            <th>Room</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($bookings)): ?>

                            <?php foreach ($bookings as $b): ?>

                                <?php
                                $status = $b['booking_status'];

                                $badge_class = match ($status) {
                                    'Confirmed' => 'bg-success',
                                    'Pending' => 'bg-warning text-dark',
                                    'Cancelled' => 'bg-danger',
                                    'Checked-Out' => 'bg-secondary',
                                    default => 'bg-dark'
                                };
                                ?>

                                <tr>

                                    <td class="ps-3 fw-semibold">
                                        #<?php echo (int)$b['booking_id']; ?>
                                    </td>

                                    <td class="fw-semibold">
                                        <?php echo htmlspecialchars($b['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        Room <?php echo htmlspecialchars($b['room_number'], ENT_QUOTES, 'UTF-8'); ?>

                                        <div class="small text-muted">
                                            <?php echo htmlspecialchars($b['type_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($b['check_in'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($b['check_out'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="text-center py-5">

                                    <i class="fa-solid fa-calendar-xmark text-muted fs-1 mb-3"></i>

                                    <h5 class="fw-bold">No Bookings Found</h5>

                                    <p class="text-muted mb-0">
                                        There are currently no guest bookings.
                                    </p>

                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer bg-white text-muted small">
            Total Records: <?php echo $total_bookings; ?>
        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
