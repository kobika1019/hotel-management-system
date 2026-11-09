
<?php
require_once 'config/db.php';
include 'includes/header.php';

$stmt = $pdo->query("
    SELECT
        r.*,
        rt.type_name,
        rt.description,
        rt.price_per_night,
        rt.capacity
    FROM rooms r
    JOIN room_types rt ON r.type_id = rt.type_id
    ORDER BY rt.price_per_night ASC, r.room_number ASC
");

$rooms = $stmt->fetchAll();
?>

<main>

    <!-- ======================================
         ROOMS HERO
    ======================================= -->

    <section class="rooms-hero">

        <div class="container text-center">

            <span class="rooms-hero-subtitle">
                BLUE MOON HOTEL
            </span>

            <h1>Our Luxury Accommodations</h1>

            <p>
                Discover comfort, elegance and the perfect
                room for your next stay.
            </p>

            <div class="rooms-hero-line"></div>

        </div>

    </section>


    <!-- ======================================
         ROOMS LIST
    ======================================= -->

    <section class="rooms-list-section">

        <div class="container">

            <div class="rooms-section-heading">

                <div>
                    <span class="section-subtitle">
                        FIND YOUR PERFECT STAY
                    </span>

                    <h2>Explore Our Rooms</h2>

                    <p>
                        Choose from our available accommodation options.
                    </p>
                </div>

                <div class="rooms-count">

                    <i class="fa-solid fa-bed"></i>

                    <?php echo count($rooms); ?>

                    Room<?php echo count($rooms) !== 1 ? 's' : ''; ?>

                </div>

            </div>


            <?php if (count($rooms) > 0): ?>

                <div class="row g-4">

                    <?php foreach ($rooms as $room): ?>

                        <?php
                        $status = $room['status'];

                        if ($status === 'Available') {
                            $statusClass = 'room-status-available';
                            $statusIcon = 'fa-circle-check';
                        } elseif ($status === 'Booked') {
                            $statusClass = 'room-status-booked';
                            $statusIcon = 'fa-calendar-xmark';
                        } else {
                            $statusClass = 'room-status-maintenance';
                            $statusIcon = 'fa-screwdriver-wrench';
                        }

                        $roomNumber = htmlspecialchars(
                            (string)$room['room_number'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $roomType = htmlspecialchars(
                            (string)$room['type_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $roomDescription = htmlspecialchars(
                            (string)$room['description'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $roomCapacity = (int)$room['capacity'];
                        $roomPrice = (float)$room['price_per_night'];
                        $roomId = (int)$room['room_id'];
                        ?>

                        <div class="col-lg-4 col-md-6">

                            <div class="luxury-room-card h-100">

                                <!-- ROOM IMAGE -->

                                <div class="room-image-wrapper">

                                    <img
                                        src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=85"
                                        alt="Luxury hotel accommodation"
                                        class="room-image"
                                        loading="lazy"
                                    >

                                    <div class="room-status <?php echo $statusClass; ?>">

                                        <i class="fa-solid <?php echo $statusIcon; ?> me-1"></i>

                                        <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>

                                    </div>

                                    <div class="room-number-overlay">
                                        ROOM <?php echo $roomNumber; ?>
                                    </div>

                                </div>


                                <!-- ROOM DETAILS -->

                                <div class="room-card-body">

                                    <div class="room-type-label">
                                        ACCOMMODATION
                                    </div>

                                    <h3 class="room-title">
                                        <?php echo $roomType; ?>
                                    </h3>

                                    <p class="room-description">
                                        <?php echo $roomDescription; ?>
                                    </p>


                                    <!-- ROOM FEATURES -->

                                    <div class="room-features">

                                        <div class="room-feature">

                                            <i class="fa-solid fa-user-group"></i>

                                            <span>
                                                <?php echo $roomCapacity; ?>
                                                Guest<?php echo $roomCapacity !== 1 ? 's' : ''; ?>
                                            </span>

                                        </div>

                                        <div class="room-feature">

                                            <i class="fa-solid fa-door-closed"></i>

                                            <span>
                                                Room <?php echo $roomNumber; ?>
                                            </span>

                                        </div>

                                    </div>


                                    <!-- PRICE AND BOOKING -->

                                    <div class="room-card-footer">

                                        <div class="room-price">

                                            <span class="price-label">
                                                PRICE PER NIGHT
                                            </span>

                                            <div>
                                                <span class="price-amount">
                                                    $<?php echo number_format($roomPrice, 2); ?>
                                                </span>

                                                <span class="price-period">
                                                    / night
                                                </span>
                                            </div>

                                        </div>


                                        <div class="room-action">

                                            <?php if ($user_id): ?>

                                                <?php if ($user_role === 'Customer'): ?>

                                                    <?php if ($status === 'Available'): ?>

                                                        <a
                                                            href="book.php?room_id=<?php echo $roomId; ?>"
                                                            class="btn btn-gold w-100"
                                                        >
                                                            <i class="fa-solid fa-calendar-check me-2"></i>
                                                            Book Now
                                                        </a>

                                                    <?php else: ?>

                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary w-100"
                                                            disabled
                                                        >
                                                            Currently Unavailable
                                                        </button>

                                                    <?php endif; ?>


                                                <?php elseif ($user_role === 'Admin'): ?>

                                                    <a
                                                        href="admin/rooms.php"
                                                        class="btn btn-outline-dark w-100"
                                                    >
                                                        <i class="fa-solid fa-gear me-2"></i>
                                                        Manage Room
                                                    </a>


                                                <?php elseif ($user_role === 'Staff'): ?>

                                                    <a
                                                        href="staff_dashboard.php"
                                                        class="btn btn-outline-warning w-100"
                                                    >
                                                        <i class="fa-solid fa-clipboard-user me-2"></i>
                                                        Staff View
                                                    </a>

                                                <?php endif; ?>


                                            <?php else: ?>

                                                <a
                                                    href="login.php"
                                                    class="btn btn-outline-dark w-100"
                                                >
                                                    <i class="fa-solid fa-right-to-bracket me-2"></i>
                                                    Login to Book
                                                </a>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>


            <?php else: ?>


                <!-- NO ROOMS AVAILABLE -->

                <div class="no-rooms-container">

                    <div class="no-rooms-icon">
                        <i class="fa-solid fa-bed"></i>
                    </div>

                    <h3>No Rooms Found</h3>

                    <p>
                        We currently have no room records to display.
                        Please check again later.
                    </p>

                    <?php if ($user_id && $user_role === 'Admin'): ?>

                        <a href="admin/rooms.php" class="btn btn-gold">
                            <i class="fa-solid fa-plus me-2"></i>
                            Add New Room
                        </a>

                    <?php endif; ?>

                </div>


            <?php endif; ?>

        </div>

    </section>


    <!-- ======================================
         BOOKING CTA
    ======================================= -->

    <section class="rooms-booking-cta">

        <div class="container text-center">

            <i class="fa-solid fa-moon rooms-cta-icon"></i>

            <h2>Your Perfect Stay Awaits</h2>

            <p>
                Find your ideal accommodation and
                make your reservation today.
            </p>

            <a href="<?php echo $user_id ? 'book.php' : 'register.php'; ?>"
               class="btn btn-gold btn-lg">

                <i class="fa-solid fa-calendar-check me-2"></i>

                <?php echo $user_id ? 'Book Your Stay' : 'Get Started'; ?>

            </a>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>
