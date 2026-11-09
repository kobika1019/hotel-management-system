
<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';


// ==========================================
// CSRF TOKEN
// ==========================================

if (empty($_SESSION['register_csrf_token'])) {
    $_SESSION['register_csrf_token'] = bin2hex(random_bytes(32));
}


// ==========================================
// HANDLE REGISTRATION
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    $role = 'Customer';


    // CSRF VALIDATION

    if (
        !is_string($csrf_token) ||
        !hash_equals($_SESSION['register_csrf_token'], $csrf_token)
    ) {

        $error = "Invalid request. Please refresh the page and try again.";

    }

    // FULL NAME VALIDATION

    elseif (empty($full_name)) {

        $error = "Please enter your full name.";

    } elseif (strlen($full_name) > 100) {

        $error = "Full name must not exceed 100 characters.";

    }

    // EMAIL VALIDATION

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($email) > 150) {

        $error = "Email address is too long.";

    }

    // PHONE VALIDATION

    elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {

        $error = "Please enter a valid phone number.";

    }

    // PASSWORD VALIDATION

    elseif (strlen($password) < 8) {

        $error = "Password must contain at least 8 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    }

    else {

        try {

            // CHECK EXISTING EMAIL

            $check = $pdo->prepare("
                SELECT user_id
                FROM users
                WHERE email = ?
                LIMIT 1
            ");

            $check->execute([$email]);

            if ($check->fetch()) {

                $error = "Email address is already registered!";

            } else {

                // HASH PASSWORD

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                // INSERT CUSTOMER

                $stmt = $pdo->prepare("
                    INSERT INTO users (
                        full_name,
                        email,
                        password,
                        phone,
                        role
                    )
                    VALUES (?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $full_name,
                    $email,
                    $hashed_password,
                    $phone,
                    $role
                ]);


                // SUCCESS

                $success = "Registration successful! You can now log in.";

                // Clear entered form values after success
                $full_name = '';
                $email = '';
                $phone = '';

                // Generate a new token
                $_SESSION['register_csrf_token'] = bin2hex(
                    random_bytes(32)
                );

            }

        } catch (PDOException $e) {

            // Handle duplicate email if another request
            // registered the same email simultaneously.

            if ($e->getCode() === '23000') {

                $error = "Email address is already registered!";

            } else {

                error_log(
                    "Registration Error: " . $e->getMessage()
                );

                $error = "Failed to create your account. Please try again.";

            }

        }

    }

}

include 'includes/header.php';
?>

<main class="container py-5">

    <div class="row justify-content-center">

        <div class="col-12 col-sm-10 col-md-7 col-lg-5">

            <div class="card card-custom border-0 shadow p-4">

                <!-- Heading -->

                <div class="text-center mb-4">

                    <i class="fa-solid fa-user-plus text-gold fa-3x mb-3"></i>

                    <h3 class="fw-bold text-navy">
                        Create Your Account
                    </h3>

                    <p class="text-muted small">
                        Join Blue Moon Hotel and experience luxury hospitality.
                    </p>

                </div>


                <!-- Error Message -->

                <?php if ($error): ?>

                    <div class="alert alert-danger small" role="alert">

                        <i class="fa-solid fa-circle-exclamation me-2"></i>

                        <?php echo htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </div>

                <?php endif; ?>


                <!-- Success Message -->

                <?php if ($success): ?>

                    <div class="alert alert-success text-center" role="alert">

                        <i class="fa-solid fa-circle-check me-2"></i>

                        <?php echo htmlspecialchars(
                            $success,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                        <div class="mt-3">

                            <a href="login.php" class="btn btn-success btn-sm px-4">

                                <i class="fa-solid fa-right-to-bracket me-1"></i>

                                Login Now

                            </a>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- Registration Form -->

                <?php if (!$success): ?>

                    <form method="POST" data-loading>

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php echo htmlspecialchars(
                                $_SESSION['register_csrf_token'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                        >


                        <!-- Full Name -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Full Name
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-user text-muted"></i>
                                </span>

                                <input
                                    type="text"
                                    name="full_name"
                                    class="form-control"
                                    placeholder="Enter your full name"
                                    maxlength="100"
                                    value="<?php echo htmlspecialchars(
                                        $full_name ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    autocomplete="name"
                                    required
                                >

                            </div>

                        </div>


                        <!-- Email -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Email Address
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-envelope text-muted"></i>
                                </span>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="name@example.com"
                                    maxlength="150"
                                    value="<?php echo htmlspecialchars(
                                        $email ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    autocomplete="email"
                                    required
                                >

                            </div>

                        </div>


                        <!-- Phone -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Phone Number
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-phone text-muted"></i>
                                </span>

                                <input
                                    type="tel"
                                    name="phone"
                                    class="form-control"
                                    placeholder="0771234567"
                                    maxlength="20"
                                    value="<?php echo htmlspecialchars(
                                        $phone ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    autocomplete="tel"
                                    required
                                >

                            </div>

                        </div>


                        <!-- Password -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="registerPassword"
                                class="form-control"
                                placeholder="Create a password"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                            <small class="text-muted">
                                Minimum 8 characters.
                            </small>

                        </div>


                        <!-- Confirm Password -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                id="confirmPassword"
                                class="form-control"
                                placeholder="Re-enter your password"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                        </div>


                        <!-- Submit Button -->

                        <button
                            type="submit"
                            class="btn btn-gold w-100 fw-bold py-2"
                        >

                            <i class="fa-solid fa-user-plus me-2"></i>

                            Create Account

                        </button>

                    </form>

                <?php endif; ?>


                <!-- Login Link -->

                <div class="text-center small mt-4">

                    <span class="text-muted">
                        Already have an account?
                    </span>

                    <a
                        href="login.php"
                        class="fw-bold text-decoration-none text-navy"
                    >
                        Login Here
                    </a>

                </div>


                <!-- Home Link -->

                <div class="text-center mt-3">

                    <a
                        href="index.php"
                        class="text-muted small text-decoration-none"
                    >

                        <i class="fa-solid fa-arrow-left me-1"></i>

                        Back to Home

                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include 'includes/footer.php'; ?>
