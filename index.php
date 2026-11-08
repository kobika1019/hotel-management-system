<?php
require_once 'config/db.php';
include 'includes/header.php';

// Role-based navigation
if ($user_id && $user_role === 'Admin') {
    $heroLink = 'admin/index.php';
    $heroButton = 'Admin Dashboard';
    $heroIcon = 'fa-gauge';
} elseif ($user_id && $user_role === 'Staff') {
    $heroLink = 'staff_dashboard.php';
    $heroButton = 'Staff Dashboard';
    $heroIcon = 'fa-clipboard-user';
} elseif ($user_id && $user_role === 'Customer') {
    $heroLink = 'book.php';
    $heroButton = 'Book Your Stay';
    $heroIcon = 'fa-calendar-check';
} else {
    $heroLink = 'login.php';
    $heroButton = 'Login to Book';
    $heroIcon = 'fa-right-to-bracket';
}
?>

<main>

    <!-- ======================================
         HERO SECTION
    ======================================= -->

    <section class="hero-banner luxury-hero">

        <div class="container">

            <div class="hero-content">

                <span class="hero-label">
                    <i class="fa-solid fa-star me-2"></i>
                    WELCOME TO BLUE MOON HOTEL
                </span>

                <h1>
                    Experience The Art Of
                    <span class="hero-gold-text">Luxury Living</span>
                </h1>

                <p class="hero-description">
                    Discover exceptional comfort, elegant accommodation,
                    and unforgettable hospitality.
                    Your perfect stay begins here.
                </p>

                <div class="hero-buttons">

                    <a href="rooms.php" class="btn btn-gold btn-lg">
                        <i class="fa-solid fa-bed me-2"></i>
                        Explore Our Rooms
                    </a>

                    <a href="<?php echo htmlspecialchars($heroLink); ?>"
                       class="btn btn-outline-light btn-lg">

                        <i class="fa-solid <?php echo htmlspecialchars($heroIcon); ?> me-2"></i>

                        <?php echo htmlspecialchars($heroButton); ?>

                    </a>

                </div>

                <div class="hero-bottom-text">
                    <span>
                        <i class="fa-solid fa-check-circle me-2"></i>
                        Comfortable Accommodation
                    </span>

                    <span>
                        <i class="fa-solid fa-check-circle me-2"></i>
                        Easy Online Reservations
                    </span>
                </div>

            </div>

        </div>

    </section>


    <!-- ======================================
         HOTEL INTRODUCTION
    ======================================= -->

    <section class="hotel-intro-section">

        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-6">

                    <div class="intro-image-wrapper">

                        <img
                            src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1000&q=85"
                            alt="Luxury hotel swimming pool and accommodation"
                            class="intro-image"
                        >

                        <div class="intro-image-overlay">
                            <i class="fa-solid fa-moon"></i>
                            <span>BLUE MOON HOTEL</span>
                        </div>

                    </div>

                </div>


                <div class="col-lg-6">

                    <div class="section-heading text-start">

                        <span class="section-subtitle">
                            ABOUT OUR HOTEL
                        </span>

                        <h2>
                            A Perfect Place To
                            <span>Relax & Enjoy</span>
                        </h2>

                    </div>

                    <p class="intro-description">
                        Welcome to Blue Moon Hotel, where comfort meets
                        elegance. We are committed to providing guests
                        with a pleasant and memorable accommodation
                        experience.
                    </p>

                    <p class="intro-description">
                        Our hotel management system makes it easier
                        for guests to explore rooms, make reservations,
                        and manage their bookings online.
                    </p>

                    <div class="intro-features">

                        <div class="intro-feature">
                            <i class="fa-solid fa-bed"></i>
                            <span>Comfortable Rooms</span>
                        </div>

                        <div class="intro-feature">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Easy Reservations</span>
                        </div>

                        <div class="intro-feature">
                            <i class="fa-solid fa-concierge-bell"></i>
                            <span>Guest Services</span>
                        </div>

                        <div class="intro-feature">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Reliable Management</span>
                        </div>

                    </div>

                    <a href="rooms.php" class="btn btn-gold mt-4">
                        Discover Our Rooms
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- ======================================
         HOTEL SERVICES
    ======================================= -->

    <section class="services-section">

        <div class="container">

            <div class="section-heading text-center">

                <span class="section-subtitle">
                    WHAT WE OFFER
                </span>

                <h2>
                    Enjoy Our <span>Facilities</span>
                </h2>

                <p>
                    Everything you need for a comfortable and
                    convenient hotel experience.
                </p>

            </div>


            <div class="row g-4 mt-3">

                <div class="col-lg-3 col-md-6">

                    <div class="service-card">

                        <div class="service-icon">
                            <i class="fa-solid fa-bed"></i>
                        </div>

                        <h5>Luxury Accommodation</h5>

                        <p>
                            Explore comfortable room options
                            designed for a relaxing stay.
                        </p>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="service-card">

                        <div class="service-icon">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>

                        <h5>Online Booking</h5>

                        <p>
                            Make your room reservations conveniently
                            through our online booking system.
                        </p>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="service-card">

                        <div class="service-icon">
                            <i class="fa-solid fa-concierge-bell"></i>
                        </div>

                        <h5>Guest Assistance</h5>

                        <p>
                            Manage your reservations and
                            keep track of your booking details.
                        </p>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="service-card">

                        <div class="service-icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>

                        <h5>Reliable Management</h5>

                        <p>
                            Organized hotel operations for
                            room and reservation management.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ======================================
         SYSTEM ACCESS PORTALS
    ======================================= -->

    <section class="portals-section">

        <div class="container">

            <div class="section-heading text-center">

                <span class="section-subtitle">
                    OUR MANAGEMENT SYSTEM
                </span>

                <h2>
                    Explore Our <span>Access Portals</span>
                </h2>

                <p>
                    Dedicated access for guests, hotel staff,
                    and administrators.
                </p>

            </div>


            <div class="row g-4 mt-3">


                <!-- GUEST PORTAL -->

                <div class="col-lg-4 col-md-6">

                    <div class="portal-card guest-portal h-100">

                        <div class="portal-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>

                        <span class="portal-label">
                            FOR GUESTS
                        </span>

                        <h4>Guest Portal</h4>

                        <p>
                            Explore available rooms, select your
                            stay dates, make reservations, and
                            track your booking status.
                        </p>

                        <a href="rooms.php" class="portal-link">
                            Browse Rooms
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>

                    </div>

                </div>


                <!-- STAFF PORTAL -->

                <div class="col-lg-4 col-md-6">

                    <div class="portal-card staff-portal h-100">

                        <div class="portal-icon">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>

                        <span class="portal-label">
                            FOR HOTEL STAFF
                        </span>

                        <h4>Staff Operations</h4>

                        <p>
                            Access staff operations and manage
                            guest-related activities through
                            the staff dashboard.
                        </p>

                        <a href="<?php echo ($user_id && $user_role === 'Staff') ? 'staff_dashboard.php' : 'login.php'; ?>"
                           class="portal-link">

                            Staff Portal
                            <i class="fa-solid fa-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>


                <!-- ADMIN PORTAL -->

                <div class="col-lg-4 col-md-6">

                    <div class="portal-card admin-portal h-100">

                        <div class="portal-icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>

                        <span class="portal-label">
                            FOR ADMINISTRATORS
                        </span>

                        <h4>Admin Management</h4>

                        <p>
                            Manage hotel rooms, booking approvals,
                            reservations, and other hotel
                            management operations.
                        </p>

                        <a href="<?php echo ($user_id && $user_role === 'Admin') ? 'admin/index.php' : 'login.php'; ?>"
                           class="portal-link">

                            Admin Panel
                            <i class="fa-solid fa-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ======================================
         FINAL BOOKING CTA
    ======================================= -->

    <section class="booking-cta-section">

        <div class="container">

            <div class="booking-cta-content">

                <span class="section-subtitle">
                    YOUR JOURNEY STARTS HERE
                </span>

                <h2>
                    Ready For A Wonderful Stay?
                </h2>

                <p>
                    Discover your perfect room and enjoy
                    a comfortable hotel experience.
                </p>

                <a href="<?php echo $user_id ? 'rooms.php' : 'register.php'; ?>"
                   class="btn btn-gold btn-lg">

                    <i class="fa-solid fa-calendar-check me-2"></i>

                    <?php echo $user_id ? 'Explore Rooms' : 'Get Started'; ?>

                </a>

            </div>

        </div>

    </section>

</main>


<?php include 'includes/footer.php'; ?>