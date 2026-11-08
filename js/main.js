document.addEventListener('DOMContentLoaded', function () {

    console.log("Blue Moon Hotel Management System - Fully Loaded.");


    // ==========================================
    // 1. BOOTSTRAP TOOLTIPS
    // ==========================================

    const tooltipElements = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );

    tooltipElements.forEach(function (element) {
        new bootstrap.Tooltip(element);
    });


    // ==========================================
    // 2. AUTO HIDE ALERT MESSAGES
    // ==========================================

    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(function (alert) {

        if (
            alert.classList.contains('alert-success') ||
            alert.classList.contains('alert-info')
        ) {

            setTimeout(function () {

                if (typeof bootstrap !== 'undefined') {

                    const alertInstance =
                        bootstrap.Alert.getOrCreateInstance(alert);

                    alertInstance.close();

                }

            }, 5000);

        }

    });


    // ==========================================
    // 3. BOOKING DATE VALIDATION
    // ==========================================

    const checkIn = document.querySelector('#check_in');
    const checkOut = document.querySelector('#check_out');

    function validateBookingDates() {

        if (!checkIn || !checkOut) {
            return;
        }

        if (checkIn.value) {

            checkOut.min = checkIn.value;

            if (
                checkOut.value &&
                checkOut.value <= checkIn.value
            ) {
                checkOut.setCustomValidity(
                    'Check-out date must be after check-in date.'
                );
            } else {
                checkOut.setCustomValidity('');
            }

        }

    }

    if (checkIn && checkOut) {

        checkIn.addEventListener('change', validateBookingDates);

        checkOut.addEventListener('change', validateBookingDates);

        validateBookingDates();

    }


    // ==========================================
    // 4. DELETE CONFIRMATION
    // ==========================================

    const deleteButtons = document.querySelectorAll(
        '.delete-confirm'
    );

    deleteButtons.forEach(function (button) {

        button.addEventListener('click', function (event) {

            const confirmed = confirm(
                'Are you sure you want to delete this record?'
            );

            if (!confirmed) {
                event.preventDefault();
            }

        });

    });


    // ==========================================
    // 5. NAVBAR SCROLL EFFECT
    // ==========================================

    const navbar = document.querySelector('.luxury-navbar');

    if (navbar) {

        function updateNavbar() {

            if (window.scrollY > 30) {

                navbar.classList.add('navbar-scrolled');

            } else {

                navbar.classList.remove('navbar-scrolled');

            }

        }

        window.addEventListener('scroll', updateNavbar, {
            passive: true
        });

        updateNavbar();

    }


    // ==========================================
    // 6. FORM SUBMISSION LOADING EFFECT
    // ==========================================

    const forms = document.querySelectorAll(
        'form[data-loading]'
    );

    forms.forEach(function (form) {

        form.addEventListener('submit', function () {

            if (!form.checkValidity()) {
                return;
            }

            const submitButton = form.querySelector(
                'button[type="submit"], input[type="submit"]'
            );

            if (submitButton) {

                submitButton.disabled = true;
                if (submitButton.tagName === 'BUTTON') {
                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                } else {
                    submitButton.value = 'Processing...';
                }

            }

        });

    });

});