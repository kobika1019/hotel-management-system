
<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ==========================================
// 1. CUSTOMER AUTHENTICATION
// ==========================================

$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

if (!$user_id || $user_role !== 'Customer') {
    header("Location: login.php");
    exit;
}

$user_id = (int)$user_id;


// ==========================================
// 2. CSRF TOKEN
// ==========================================

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// ==========================================
// 3. HANDLE BOOKING CANCELLATION
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {

    $cancel_id = filter_var(
        $_POST['cancel_id'],
        FILTER_VALIDATE_INT
    );

    $csrf_token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrf_token) ||
        !hash_equals($_SESSION['csrf_token'], $csrf_token)
    ) {

        $_SESSION['booking_message'] = [
            'type' => 'danger',
            'text' => 'Invalid request. Please refresh the page and try again.'
        ];

    } elseif (!$cancel_id || $cancel_id <= 0) {

        $_SESSION['booking_message'] = [
            'type' => 'danger',
            'text' => 'Invalid booking selected.'
        ];

    } else {

        try {

            // Only the logged-in customer's Pending booking can be cancelled.
            $update = $pdo->prepare("
                UPDATE bookings
                SET booking_status = 'Cancelled'
                WHERE booking_id = ?
                AND user_id = ?
                AND booking_status = 'Pending'
            ");

            $update->execute([
                $cancel_id,
                $user_id
            ]);

            if ($update->rowCount() > 0) {

                $_SESSION['booking_message'] = [
                    'type' => 'success',
                    'text' => 'Your booking has been cancelled successfully.'
                ];

            } else {

                $_SESSION['booking_message'] = [
                    'type' => 'warning',
                    'text' => 'This booking cannot be cancelled. It may already be processed or does not belong to your account.'
                ];

            }

        } catch (PDOException $e) {

            error_log(
                "Booking Cancellation Error: " . $e->getMessage()
            );

            $_SESSION['booking_message'] = [
                'type' => 'danger',
                'text' => 'Unable to cancel your booking. Please try again.'
            ];

        }

    }

    header("Location: my_bookings.php");
    exit;
}


// ==========================================
// 4. FETCH CUSTOMER BOOKINGS
// ==========================================

$stmt = $pdo->prepare("
    SELECT
        b.*,
        r.room_number,
        rt.type_name
    FROM bookings b
    JOIN rooms r
        ON b.room_id = r.room_id
    JOIN room_types rt
        ON r.type_id = rt.type_id
    WHERE b.user_id = ?
    ORDER BY b.booking_id DESC
");

$stmt->execute([$user_id]);

$bookings = $stmt->fetchAll();


// ==========================================
// 5. BOOKING SUMMARY
// ==========================================

$total_bookings = count($bookings);

$pending_count = 0;
$confirmed_count = 0;
$cancelled_count = 0;
$completed_count = 0;

foreach ($bookings as $booking) {

    if ($booking['booking_status'] === 'Pending') {
        $pending_count++;
    } elseif ($booking['booking_status'] === 'Confirmed') {
        $confirmed_count++;
    } elseif ($booking['booking_status'] === 'Cancelled') {
        $cancelled_count++;
    } elseif ($booking['booking_status'] === 'Checked-Out') {
        $completed_count++;
    }

}


// ==========================================
// 6. INCLUDE HEADER
// ==========================================

include 'includes/header.php';

?>

<main>

    <!-- ======================================
         PAGE HERO
    ======================================= -->

    <section class="my-bookings-hero">

        <div class="container text-center">

            <span>BLUE MOON HOTEL</span>

            <h1>My Reservations</h1>

            <p>
                View and manage all your hotel reservations
                in one place.
            </p>

        </div>

    </section>


    <!-- ======================================
         BOOKING CONTENT
    ======================================= -->

    <section class="my-bookings-section">

        <div class="container">


            <!-- SUCCESS MESSAGE AFTER BOOKING -->

            <?php if (isset($_GET['booking_success'])): ?>

                <div class="alert alert-success alert-dismissible fade show mb-4">

                    <i class="fa-solid fa-circle-check me-2"></i>

                    <strong>Success!</strong>

                    Your booking request has been submitted.
                    It is now Pending admin approval.

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            <?php endif; ?>


            <!-- CANCELLATION MESSAGE -->

            <?php if (isset($_SESSION['booking_message'])): ?>

                <?php
                $message = $_SESSION['booking_message'];
                unset($_SESSION['booking_message']);
                ?>

                <div class="alert alert-<?php echo htmlspecialchars(
                    $message['type'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?> alert-dismissible fade show mb-4">

                    <?php echo htmlspecialchars(
                        $message['text'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            <?php endif; ?>


            <!-- PAGE HEADING -->

            <div class="bookings-page-heading">

                <div>
                    <span class="booking-section-label">
                        CUSTOMER ACCOUNT
                    </span>

                    <h2>My Hotel Bookings</h2>

                    <p>
                        Track your reservations and booking status.
                    </p>
                </div>

                <a href="rooms.php" class="btn btn-gold">

                    <i class="fa-solid fa-plus me-2"></i>

                    Book Another Room

                </a>

            </div>


            <!-- ==================================
                 BOOKING SUMMARY CARDS
            =================================== -->

            <div class="row g-3 mb-4">

                <div class="col-6 col-lg-3">

                    <div class="booking-summary-card">

                        <div class="summary-icon summary-total">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>

                        <div>
                            <span>Total Bookings</span>
                            <h3><?php echo $total_bookings; ?></h3>
                        </div>

                    </div>

                </div>


                <div class="col-6 col-lg-3">

                    <div class="booking-summary-card">

                        <div class="summary-icon summary-pending">
                            <i class="fa-solid fa-clock"></i>
                        </div>

                        <div>
                            <span>Pending</span>
                            <h3><?php echo $pending_count; ?></h3>
                        </div>

                    </div>

                </div>


                <div class="col-6 col-lg-3">

                    <div class="booking-summary-card">

                        <div class="summary-icon summary-confirmed">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <div>
                            <span>Confirmed</span>
                            <h3><?php echo $confirmed_count; ?></h3>
                        </div>

                    </div>

                </div>


                <div class="col-6 col-lg-3">

                    <div class="booking-summary-card">

                        <div class="summary-icon summary-completed">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>

                        <div>
                            <span>Completed</span>
                            <h3><?php echo $completed_count; ?></h3>
                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================
                 BOOKINGS TABLE
            =================================== -->

            <div class="bookings-table-card">

                <div class="bookings-table-heading">

                    <div>
                        <h3>Reservation History</h3>

                        <p>
                            Your recent and previous reservations.
                        </p>
                    </div>

                    <span class="booking-count">
                        <?php echo $total_bookings; ?>
                        Reservation(s)
                    </span>

                </div>


                <div class="table-responsive">

                    <table class="table bookings-table align-middle mb-0">

                        <thead>

                            <tr>
                                <th>Booking ID</th>
                                <th>Room Details</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Total Cost</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>

                        </thead>


                        <tbody>

                            <?php if ($total_bookings > 0): ?>

                                <?php foreach ($bookings as $b): ?>

                                    <?php

                                    $status = $b['booking_status'];

                                    $status_class = 'status-default';
                                    $status_icon = 'fa-circle-info';

                                    if ($status === 'Confirmed') {
                                        $status_class = 'status-confirmed';
                                        $status_icon = 'fa-circle-check';
                                    } elseif ($status === 'Pending') {
                                        $status_class = 'status-pending';
                                        $status_icon = 'fa-clock';
                                    } elseif ($status === 'Cancelled') {
                                        $status_class = 'status-cancelled';
                                        $status_icon = 'fa-circle-xmark';
                                    } elseif ($status === 'Checked-Out') {
                                        $status_class = 'status-completed';
                                        $status_icon = 'fa-flag-checkered';
                                    }

                                    ?>

                                    <tr>

                                        <!-- BOOKING ID -->

                                        <td>

                                            <strong class="booking-id">
                                                #<?php echo (int)$b['booking_id']; ?>
                                            </strong>

                                        </td>


                                        <!-- ROOM DETAILS -->

                                        <td>

                                            <div class="booking-room-details">

                                                <span class="booking-room-number">

                                                    <i class="fa-solid fa-bed me-1"></i>

                                                    Room <?php echo htmlspecialchars(
                                                        (string)$b['room_number'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>

                                                </span>

                                                <small>
                                                    <?php echo htmlspecialchars(
                                                        (string)$b['type_name'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>
                                                </small>

                                            </div>

                                        </td>


                                        <!-- CHECK IN -->

                                        <td>

                                            <?php echo htmlspecialchars(
                                                (string)$b['check_in'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>

                                        </td>


                                        <!-- CHECK OUT -->

                                        <td>

                                            <?php echo htmlspecialchars(
                                                (string)$b['check_out'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>

                                        </td>


                                        <!-- TOTAL COST -->

                                        <td>

                                            <strong class="booking-price">

                                                $<?php echo number_format(
                                                    (float)$b['total_amount'],
                                                    2
                                                ); ?>

                                            </strong>

                                        </td>


                                        <!-- STATUS -->

                                        <td>

                                            <span class="booking-status <?php echo $status_class; ?>">

                                                <i class="fa-solid <?php echo $status_icon; ?> me-1"></i>

                                                <?php echo htmlspecialchars(
                                                    $status,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- ACTION -->

                                        <td>

                                            <?php if ($status === 'Pending'): ?>

                                                <form
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to cancel this pending booking?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="cancel_id"
                                                        value="<?php echo (int)$b['booking_id']; ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?php echo htmlspecialchars(
                                                            $_SESSION['csrf_token'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ); ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-cancel-booking"
                                                    >

                                                        <i class="fa-solid fa-xmark me-1"></i>

                                                        Cancel

                                                    </button>

                                                </form>

                                            <?php else: ?>

                                                <span class="text-muted small">
                                                    No actions
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>


                            <?php else: ?>

                                <tr>

                                    <td colspan="7">

                                        <div class="no-bookings-state">

                                            <div class="no-bookings-icon">
                                                <i class="fa-solid fa-calendar-xmark"></i>
                                            </div>

                                            <h4>No Reservations Yet</h4>

                                            <p>
                                                You have not made any hotel
                                                reservations yet.
                                            </p>

                                            <a
                                                href="rooms.php"
                                                class="btn btn-gold"
                                            >

                                                <i class="fa-solid fa-bed me-2"></i>

                                                Explore Our Rooms

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>
