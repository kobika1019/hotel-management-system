
<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';


// ==========================================
// CSRF TOKEN
// ==========================================

if (empty($_SESSION['login_csrf_token'])) {
    $_SESSION['login_csrf_token'] = bin2hex(random_bytes(32));
}


// ==========================================
// HANDLE LOGIN
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrf_token) ||
        !hash_equals($_SESSION['login_csrf_token'], $csrf_token)
    ) {

        $error = "Invalid request. Please refresh the page and try again.";

    } elseif (empty($email) || $password === '') {

        $error = "Please enter your email and password.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        // Find user by email
        $stmt = $pdo->prepare("
            SELECT user_id, full_name, email, password, role
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch();


        // Verify password
        if ($user && password_verify($password, $user['password'])) {

            // Regenerate session ID after successful login
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['role'] = $user['role'];

            // Generate a fresh CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            unset($_SESSION['login_csrf_token']);


            // Redirect based on user role
            if ($user['role'] === 'Admin') {

                header("Location: admin/index.php");

            } elseif ($user['role'] === 'Staff') {

                header("Location: staff_dashboard.php");

            } else {

                header("Location: my_bookings.php");

            }

            exit;

        } else {

            $error = "Invalid Email Address or Password.";

        }

    }

}

include 'includes/header.php';
?>

<main class="container py-5">

    <div class="row justify-content-center align-items-center">

        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

            <div class="card card-custom border-0 shadow p-4">

                <!-- Heading -->

                <div class="text-center mb-4">

                    <i class="fa-solid fa-moon text-gold fa-3x mb-3"></i>

                    <h3 class="fw-bold text-navy">
                        Welcome Back
                    </h3>

                    <p class="text-muted small mb-0">
                        Sign in to your Blue Moon Hotel account
                    </p>

                </div>


                <!-- Error Message -->

                <?php if ($error): ?>

                    <div class="alert alert-danger small text-center" role="alert">

                        <i class="fa-solid fa-circle-exclamation me-2"></i>

                        <?php echo htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </div>

                <?php endif; ?>


                <!-- Login Form -->

                <form method="POST" data-loading>

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php echo htmlspecialchars(
                            $_SESSION['login_csrf_token'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >


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
                                placeholder="admin@bluemoon.com"
                                value="<?php echo htmlspecialchars(
                                    $_POST['email'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                autocomplete="username"
                                required
                            >

                        </div>

                    </div>


                    <!-- Password -->

                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Password
                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-light">
                                <i class="fa-solid fa-lock text-muted"></i>
                            </span>

                            <input
                                type="password"
                                name="password"
                                id="loginPassword"
                                class="form-control"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="togglePassword"
                                aria-label="Show password"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>

                        </div>

                    </div>


                    <!-- Submit -->

                    <button
                        type="submit"
                        class="btn btn-gold w-100 fw-bold py-2"
                    >

                        <i class="fa-solid fa-right-to-bracket me-2"></i>

                        Sign In

                    </button>

                </form>


                <!-- Register Link -->

                <div class="text-center small mt-4">

                    <span class="text-muted">
                        Don't have an account?
                    </span>

                    <a
                        href="register.php"
                        class="fw-bold text-decoration-none text-navy"
                    >
                        Register Here
                    </a>

                </div>


                <!-- Back to Home -->

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


<script>
document.addEventListener('DOMContentLoaded', function () {

    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('loginPassword');

    togglePassword.addEventListener('click', function () {

        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';

        this.innerHTML = isPassword
            ? '<i class="fa-solid fa-eye-slash"></i>'
            : '<i class="fa-solid fa-eye"></i>';

        this.setAttribute(
            'aria-label',
            isPassword ? 'Hide password' : 'Show password'
        );

    });

});
</script>

<?php include 'includes/footer.php'; ?>
