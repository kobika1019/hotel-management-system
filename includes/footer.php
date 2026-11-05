
<footer class="luxury-footer">

    <!-- FOOTER MAIN CONTENT -->

    <div class="container py-5">

        <div class="row g-5">

            <!-- HOTEL BRAND -->

            <div class="col-lg-4 col-md-6">

                <a href="<?php echo isset($base_path) ? $base_path : './'; ?>index.php"
                   class="footer-brand text-decoration-none">

                    <i class="fa-solid fa-moon footer-moon"></i>

                    <span>BLUE MOON</span>

                </a>

                <p class="footer-tagline">
                    LUXURY HOTEL & RESORT
                </p>

                <p class="footer-description">
                    Experience exceptional comfort, elegant accommodation
                    and unforgettable hospitality at Blue Moon Hotel.
                    Your comfort is our priority.
                </p>

                <div class="footer-socials">

                    <a href="#" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="#" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#" aria-label="Twitter">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>

                </div>

            </div>


            <!-- QUICK LINKS -->

            <div class="col-lg-2 col-md-6">

                <h5 class="footer-heading">
                    Quick Links
                </h5>

                <ul class="footer-links">

                    <li>
                        <a href="<?php echo $base_path; ?>index.php">
                            <i class="fa-solid fa-angle-right"></i>
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo $base_path; ?>rooms.php">
                            <i class="fa-solid fa-angle-right"></i>
                            Our Rooms
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo $base_path; ?>login.php">
                            <i class="fa-solid fa-angle-right"></i>
                            Login
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo $base_path; ?>register.php">
                            <i class="fa-solid fa-angle-right"></i>
                            Register
                        </a>
                    </li>

                </ul>

            </div>


            <!-- HOTEL SERVICES -->

            <div class="col-lg-2 col-md-6">

                <h5 class="footer-heading">
                    Our Services
                </h5>

                <ul class="footer-links">

                    <li>
                        <span>
                            <i class="fa-solid fa-angle-right"></i>
                            Luxury Rooms
                        </span>
                    </li>

                    <li>
                        <span>
                            <i class="fa-solid fa-angle-right"></i>
                            Online Booking
                        </span>
                    </li>

                    <li>
                        <span>
                            <i class="fa-solid fa-angle-right"></i>
                            Guest Services
                        </span>
                    </li>

                    <li>
                        <span>
                            <i class="fa-solid fa-angle-right"></i>
                            Comfortable Stay
                        </span>
                    </li>

                </ul>

            </div>


            <!-- CONTACT INFORMATION -->

            <div class="col-lg-4 col-md-6">

                <h5 class="footer-heading">
                    Contact Information
                </h5>

                <div class="footer-contact">

                    <div class="contact-item">

                        <i class="fa-solid fa-location-dot"></i>

                        <span>
                            Blue Moon Hotel
                        </span>

                    </div>

                    <div class="contact-item">

                        <i class="fa-solid fa-envelope"></i>

                        <span>
                            Contact us through our hotel management team
                        </span>

                    </div>

                    <div class="contact-item">

                        <i class="fa-solid fa-clock"></i>

                        <span>
                            Online Booking Available
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- FOOTER BOTTOM -->

    <div class="footer-bottom">

        <div class="container">

            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-2 py-3">

                <p class="mb-0">
                    &copy; <?php echo date('Y'); ?>
                    <span class="footer-gold">Blue Moon Hotel</span>.
                    All Rights Reserved.
                </p>

                <p class="mb-0">
                    Designed with
                    <i class="fa-solid fa-heart footer-heart"></i>
                    for a better hospitality experience.
                </p>

            </div>

        </div>

    </div>

</footer>


<!-- BOOTSTRAP JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- CUSTOM JAVASCRIPT -->

<script src="<?php echo isset($base_path) ? $base_path : './'; ?>js/main.js"></script>

</body>
</html>
