
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

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

// Handle Booking Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Validate CSRF Token
    if (
        !is_string($csrf_token) ||
        !hash_equals($_SESSION['csrf_token'], $csrf_token)
    ) {
        $_SESSION['booking_admin_message'] = [
            'type' => 'danger',
            'text' => 'Invalid request. Please try again.'
        ];

        header("Location: bookings.php");
        exit;
    }

    // Validate Booking ID
    if (!$booking_id || $booking_id <= 0) {

        $_SESSION['booking_admin_message'] = [
            'type' => 'danger',
            'text' => 'Invalid booking ID.'
        ];

        header("Location: bookings.php");
        exit;
    }

    // Approve Booking
    if ($action === 'approve') {

        $new_status = 'Confirmed';

    // Reject Booking
    } elseif ($action === 'reject') {

        $new_status = 'Cancelled';

    } else {

        $_SESSION['booking_admin_message'] = [
            'type' => 'danger',
            'text' => 'Invalid booking action.'
        ];

        header("Location: bookings.php");
        exit;
    }

    try {

        // Update Only Pending Bookings
        $stmt = $pdo->prepare("
            UPDATE bookings
            SET booking_status = ?
            WHERE booking_id = ?
            AND booking_status = 'Pending'
        ");

        $stmt->execute([$new_status, $booking_id]);

        if ($stmt->rowCount() > 0) {

            $_SESSION['booking_admin_message'] = [
                'type' => 'success',
                'text' => $new_status === 'Confirmed'
                    ? 'Booking approved successfully!'
                    : 'Booking rejected successfully.'
            ];

        } else {

            $_SESSION['booking_admin_message'] = [
                'type' => 'warning',
                'text' => 'Booking not found or it is no longer pending.'
            ];

        }

    } catch (PDOException $e) {

        error_log("Booking update error: " . $e->getMessage());

        $_SESSION['booking_admin_message'] = [
            'type' => 'danger',
            'text' => 'Unable to update booking. Please try again.'
        ];
    }

    // Redirect After Action
    header("Location: bookings.php");
    exit;
}

// Get Flash Message
$message = $_SESSION['booking_admin_message'] ?? null;
unset($_SESSION['booking_admin_message']);

// Fetch All Bookings
$stmt = $pdo->query("
    SELECT
        b.*,
        u.full_name,
        u.email,
        r.room_number,
        rt.type_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN rooms r ON b.room_id = r.room_id
    JOIN room_types rt ON r.type_id = rt.type_id
    ORDER BY b.booking_id DESC
");

$bookings = $stmt->fetchAll();

include '../includes/header.php';

?>

<div class="container my-4 my-lg-5">

    <!-- PAGE HEADING -->

    <div class="mb-4">

        <h3 class="fw-bold mb-1">
            <i class="fa-solid fa-calendar-check text-warning me-2"></i>
            Manage Guest Bookings
        </h3>

        <p class="text-muted mb-0">
            Review guest reservations and approve or reject pending bookings.
        </p>

    </div>


    <!-- ALERT MESSAGE -->

    <?php if ($message): ?>

        <div class="alert alert-<?php echo htmlspecialchars($message['type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">

            <?php echo htmlspecialchars($message['text'], ENT_QUOTES, 'UTF-8'); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        </div>

    <?php endif; ?>


    <!-- BOOKINGS TABLE -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 p-3">

            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-list-check text-warning me-2"></i>
                Guest Bookings Overview

            </h5>

            <span class="small text-muted">
                Total Bookings: <?php echo count($bookings); ?>
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-dark">

                        <tr>
                            <th class="p-3">ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Dates</th>
                            <th>Total Cost</th>
                            <th>Status</th>
                            <th>Admin Approval</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($bookings)): ?>

                            <?php foreach ($bookings as $b): ?>

                                <?php

                                $status = $b['booking_status'];

                                if ($status === 'Confirmed') {
                                    $badge_class = 'bg-success';
                                } elseif ($status === 'Pending') {
                                    $badge_class = 'bg-warning text-dark';
                                } elseif ($status === 'Cancelled') {
                                    $badge_class = 'bg-danger';
                                } elseif ($status === 'Checked-Out') {
                                    $badge_class = 'bg-secondary';
                                } else {
                                    $badge_class = 'bg-dark';
                                }

                                ?>

                                <tr>

                                    <!-- BOOKING ID -->

                                    <td class="ps-3 fw-bold">
                                        #<?php echo (int)$b['booking_id']; ?>
                                    </td>


                                    <!-- GUEST DETAILS -->

                                    <td>

                                        <div class="fw-semibold">
                                            <?php echo htmlspecialchars($b['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>

                                        <div class="small text-muted">
                                            <?php echo htmlspecialchars($b['email'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>

                                    </td>


                                    <!-- ROOM DETAILS -->

                                    <td>

                                        <div class="fw-semibold">
                                            Room <?php echo htmlspecialchars($b['room_number'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>

                                        <div class="small text-muted">
                                            <?php echo htmlspecialchars($b['type_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>

                                    </td>


                                    <!-- BOOKING DATES -->

                                    <td>

                                        <div class="small">
                                            <strong>In:</strong>
                                            <?php echo htmlspecialchars($b['check_in'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>

                                        <div class="small">
                                            <strong>Out:</strong>
                                            <?php echo htmlspecialchars($b['check_out'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>

                                    </td>


                                    <!-- TOTAL COST -->

                                    <td class="fw-bold text-success">
                                        $<?php echo number_format((float)$b['total_amount'], 2); ?>
                                    </td>


                                    <!-- BOOKING STATUS -->

                                    <td>

                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                    </td>


                                    <!-- ADMIN ACTIONS -->

                                    <td>

                                        <?php if ($status === 'Pending'): ?>

                                            <div class="d-flex flex-wrap gap-2">

                                                <!-- APPROVE FORM -->

                                                <form method="POST" action="bookings.php">

                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="booking_id"
                                                        value="<?php echo (int)$b['booking_id']; ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="approve"
                                                    >

                                                    <button type="submit" class="btn btn-success btn-sm">

                                                        <i class="fa-solid fa-check me-1"></i>
                                                        Approve

                                                    </button>

                                                </form>


                                                <!-- REJECT FORM -->

                                                <form method="POST" action="bookings.php"
                                                      onsubmit="return confirm('Are you sure you want to reject this booking?');">

                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="booking_id"
                                                        value="<?php echo (int)$b['booking_id']; ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="reject"
                                                    >

                                                    <button type="submit" class="btn btn-outline-danger btn-sm">

                                                        <i class="fa-solid fa-xmark me-1"></i>
                                                        Reject

                                                    </button>

                                                </form>

                                            </div>

                                        <?php else: ?>

                                            <span class="text-muted small">
                                                No action needed
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="7" class="text-center py-5">

                                    <i class="fa-solid fa-calendar-xmark text-muted fs-1 mb-3"></i>

                                    <h5 class="fw-bold">
                                        No Bookings Found
                                    </h5>

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
            Showing <?php echo count($bookings); ?> booking records.
        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>
