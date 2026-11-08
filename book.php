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
// 2. INITIAL VALUES
// ==========================================

$room_id = $_GET['room_id'] ?? '';
$error = '';

$check_in = $_POST['check_in'] ?? date('Y-m-d');

$check_out = $_POST['check_out'] ?? date(
    'Y-m-d',
    strtotime('+2 days')
);

$selected_room_id = $_POST['room_id'] ?? $room_id;


// ==========================================
// 3. CSRF TOKEN
// ==========================================

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// ==========================================
// 4. FETCH ROOMS
// ==========================================

// Maintenance rooms cannot be booked.
// Booked rooms are also listed because they may
// become available for different future dates.

$rooms = $pdo->query("
    SELECT
        r.room_id,
        r.room_number,
        r.status,
        rt.type_name,
        rt.price_per_night
    FROM rooms r
    JOIN room_types rt ON r.type_id = rt.type_id
    WHERE r.status IN ('Available', 'Booked')
    ORDER BY r.room_number ASC
")->fetchAll();


// ==========================================
// 5. HANDLE BOOKING FORM
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $selected_room_id = $_POST['room_id'] ?? '';

    $check_in = trim($_POST['check_in'] ?? '');

    $check_out = trim($_POST['check_out'] ?? '');

    $csrf_token = $_POST['csrf_token'] ?? '';


    // --------------------------------------
    // CSRF VALIDATION
    // --------------------------------------

    if (
        !is_string($csrf_token) ||
        !hash_equals($_SESSION['csrf_token'], $csrf_token)
    ) {

        $error = "Invalid request. Please refresh the page and try again.";

    }


    // --------------------------------------
    // ROOM ID VALIDATION
    // --------------------------------------

    elseif (
        !filter_var(
            $selected_room_id,
            FILTER_VALIDATE_INT
        ) ||
        (int)$selected_room_id <= 0
    ) {

        $error = "Please select a valid room.";

    }


    // --------------------------------------
    // DATE VALIDATION
    // --------------------------------------

    elseif (empty($check_in) || empty($check_out)) {

        $error = "Please select both check-in and check-out dates.";

    } else {

        $checkInDate = DateTime::createFromFormat(
            '!Y-m-d',
            $check_in
        );

        $checkOutDate = DateTime::createFromFormat(
            '!Y-m-d',
            $check_out
        );

        $validCheckIn = $checkInDate &&
            $checkInDate->format('Y-m-d') === $check_in;

        $validCheckOut = $checkOutDate &&
            $checkOutDate->format('Y-m-d') === $check_out;


        if (!$validCheckIn || !$validCheckOut) {

            $error = "Please enter valid booking dates.";

        } elseif ($check_in < date('Y-m-d')) {

            $error = "Check-in date cannot be in the past.";

        } elseif ($check_out <= $check_in) {

            $error = "Check-out date must be after check-in date.";

        } else {


            // ======================================
            // 6. DATABASE BOOKING TRANSACTION
            // ======================================

            try {

                $pdo->beginTransaction();


                // ----------------------------------
                // LOCK SELECTED ROOM
                // ----------------------------------

                $roomStmt = $pdo->prepare("
                    SELECT
                        r.room_id,
                        r.room_number,
                        r.status,
                        rt.price_per_night
                    FROM rooms r
                    JOIN room_types rt
                        ON r.type_id = rt.type_id
                    WHERE r.room_id = ?
                    FOR UPDATE
                ");

                $roomStmt->execute([
                    (int)$selected_room_id
                ]);

                $roomData = $roomStmt->fetch();


                if (!$roomData) {

                    throw new Exception(
                        "Selected room does not exist."
                    );

                }


                // ----------------------------------
                // CHECK ROOM STATUS
                // ----------------------------------

                if ($roomData['status'] === 'Maintenance') {

                    throw new Exception(
                        "This room is currently under maintenance."
                    );

                }


                // ----------------------------------
                // CHECK BOOKING DATE CONFLICT
                // ----------------------------------

                $conflictStmt = $pdo->prepare("
                    SELECT booking_id
                    FROM bookings
                    WHERE room_id = ?
                    AND booking_status IN ('Pending', 'Confirmed')
                    AND check_in < ?
                    AND check_out > ?
                    LIMIT 1
                ");

                $conflictStmt->execute([
                    (int)$selected_room_id,
                    $check_out,
                    $check_in
                ]);

                $conflict = $conflictStmt->fetch();


                if ($conflict) {

                    throw new Exception(
                        "Selected room is already booked for these dates. Please choose different dates."
                    );

                }


                // ----------------------------------
                // CALCULATE TOTAL AMOUNT
                // ----------------------------------

                $days = $checkInDate->diff($checkOutDate)->days;

                $price_per_night = (float)$roomData['price_per_night'];

                $total_amount = $days * $price_per_night;


                if ($days <= 0 || $total_amount <= 0) {

                    throw new Exception(
                        "Invalid booking amount."
                    );

                }


                // ----------------------------------
                // INSERT BOOKING
                // ----------------------------------

                $insert = $pdo->prepare("
                    INSERT INTO bookings (
                        user_id,
                        room_id,
                        check_in,
                        check_out,
                        total_amount,
                        booking_status
                    )
                    VALUES (?, ?, ?, ?, ?, 'Pending')
                ");

                $insert->execute([
                    $user_id,
                    (int)$selected_room_id,
                    $check_in,
                    $check_out,
                    $total_amount
                ]);


                // ----------------------------------
                // COMMIT TRANSACTION
                // ----------------------------------

                $pdo->commit();


                // Regenerate token after successful booking
                $_SESSION['csrf_token'] = bin2hex(
                    random_bytes(32)
                );


                header(
                    "Location: my_bookings.php?booking_success=1"
                );

                exit;


            } catch (Exception $e) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $error = $e->getMessage();


            } catch (PDOException $e) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                error_log(
                    "Booking Database Error: " . $e->getMessage()
                );

                $error = "Unable to complete your booking. Please try again.";

            }

        }

    }

}


// ==========================================
// 7. INCLUDE HEADER
// ==========================================

include 'includes/header.php';

?>

<main>

    <!-- ======================================
         BOOKING HERO
    ======================================= -->

    <section class="booking-page-hero">

        <div class="container text-center">

            <span>BLUE MOON HOTEL</span>

            <h1>Reserve Your Stay</h1>

            <p>
                Complete your booking and get ready
                for a comfortable experience.
            </p>

        </div>

    </section>


    <!-- ======================================
         BOOKING FORM
    ======================================= -->

    <section class="booking-form-section">

        <div class="container">

            <div class="booking-form-wrapper">

                <div class="booking-form-header">

                    <div class="booking-form-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div>
                        <h2>Booking Details</h2>

                        <p>
                            Please provide your reservation details.
                        </p>
                    </div>

                </div>


                <?php if ($error): ?>

                    <div class="alert alert-danger small">

                        <i class="fa-solid fa-circle-exclamation me-2"></i>

                        <?php echo htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </div>

                <?php endif; ?>


                <?php if (count($rooms) > 0): ?>


                    <form method="POST" data-loading>

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php echo htmlspecialchars(
                                $_SESSION['csrf_token'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                        >


                        <!-- SELECT ROOM -->

                        <div class="mb-4">

                            <label class="form-label">
                                <i class="fa-solid fa-bed me-2"></i>
                                Select Your Room
                            </label>

                            <select
                                name="room_id"
                                class="form-select booking-select"
                                required
                            >

                                <option value="">
                                    -- Choose a Room --
                                </option>

                                <?php foreach ($rooms as $r): ?>

                                    <option
                                        value="<?php echo (int)$r['room_id']; ?>"
                                        <?php echo (
                                            (string)$selected_room_id ===
                                            (string)$r['room_id']
                                        ) ? 'selected' : ''; ?>
                                    >

                                        Room <?php echo htmlspecialchars(
                                            (string)$r['room_number'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                        -
                                        <?php echo htmlspecialchars(
                                            (string)$r['type_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                        ($<?php echo number_format(
                                            (float)$r['price_per_night'],
                                            2
                                        ); ?>/night)

                                        <?php if ($r['status'] === 'Booked'): ?>
                                            - Existing Booking
                                        <?php endif; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <small class="booking-help-text">
                                Room availability will be checked
                                for your selected dates.
                            </small>

                        </div>


                        <!-- BOOKING DATES -->

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label class="form-label">

                                    <i class="fa-solid fa-calendar-day me-2"></i>

                                    Check-In Date

                                </label>

                                <input
                                    type="date"
                                    id="check_in"
                                    name="check_in"
                                    class="form-control booking-date"
                                    value="<?php echo htmlspecialchars(
                                        $check_in,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    min="<?php echo date('Y-m-d'); ?>"
                                    required
                                >

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">

                                    <i class="fa-solid fa-calendar-check me-2"></i>

                                    Check-Out Date

                                </label>

                                <input
                                    type="date"
                                    id="check_out"
                                    name="check_out"
                                    class="form-control booking-date"
                                    value="<?php echo htmlspecialchars(
                                        $check_out,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    min="<?php echo date(
                                        'Y-m-d',
                                        strtotime('+1 day')
                                    ); ?>"
                                    required
                                >

                            </div>

                        </div>


                        <!-- BOOKING INFORMATION -->

                        <div class="booking-information">

                            <div class="booking-info-icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <div>
                                <strong>Important Information</strong>

                                <p>
                                    Your reservation will initially
                                    have Pending status. The booking
                                    must be approved by hotel staff
                                    or an administrator.
                                </p>
                            </div>

                        </div>


                        <!-- SUBMIT -->

                        <button
                            type="submit"
                            class="btn btn-gold w-100 booking-submit-btn"
                        >

                            <i class="fa-solid fa-calendar-check me-2"></i>

                            Confirm Booking Request

                        </button>

                    </form>


                <?php else: ?>


                    <div class="text-center py-5">

                        <i class="fa-solid fa-bed fa-3x text-warning mb-3"></i>

                        <h4>No Rooms Available</h4>

                        <p class="text-muted">
                            There are currently no rooms available
                            for booking.
                        </p>

                        <a href="rooms.php" class="btn btn-outline-dark">
                            Back to Rooms
                        </a>

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>