
<?php

require_once '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin Authentication
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

if (!$user_id || $user_role !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$allowed_statuses = ['Available', 'Booked', 'Maintenance'];

// Handle Room Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf_token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrf_token) ||
        !hash_equals($_SESSION['csrf_token'], $csrf_token)
    ) {
        $_SESSION['room_message'] = [
            'type' => 'danger',
            'text' => 'Invalid request. Please try again.'
        ];

        header("Location: rooms.php");
        exit;
    }

    $action = $_POST['action'] ?? '';

    try {

        // ADD NEW ROOM
        if ($action === 'add') {

            $room_number = trim($_POST['room_number'] ?? '');
            $type_id = filter_var($_POST['type_id'] ?? '', FILTER_VALIDATE_INT);
            $status = $_POST['status'] ?? '';

            if ($room_number === '' || strlen($room_number) > 10) {
                throw new Exception('Enter a valid room number (maximum 10 characters).');
            }

            if (!$type_id || $type_id <= 0) {
                throw new Exception('Please select a valid room category.');
            }

            if (!in_array($status, $allowed_statuses, true)) {
                throw new Exception('Please select a valid room status.');
            }

            $stmt = $pdo->prepare("
                INSERT INTO rooms (room_number, type_id, status)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([$room_number, $type_id, $status]);

            $_SESSION['room_message'] = [
                'type' => 'success',
                'text' => 'New room added successfully!'
            ];
        }

        // UPDATE ROOM
        elseif ($action === 'update') {

            $room_id = filter_var($_POST['room_id'] ?? '', FILTER_VALIDATE_INT);
            $room_number = trim($_POST['room_number'] ?? '');
            $type_id = filter_var($_POST['type_id'] ?? '', FILTER_VALIDATE_INT);
            $status = $_POST['status'] ?? '';

            if (!$room_id || $room_id <= 0) {
                throw new Exception('Invalid room ID.');
            }

            if ($room_number === '' || strlen($room_number) > 10) {
                throw new Exception('Enter a valid room number (maximum 10 characters).');
            }

            if (!$type_id || $type_id <= 0) {
                throw new Exception('Please select a valid room category.');
            }

            if (!in_array($status, $allowed_statuses, true)) {
                throw new Exception('Please select a valid room status.');
            }

            $stmt = $pdo->prepare("
                UPDATE rooms
                SET room_number = ?, type_id = ?, status = ?
                WHERE room_id = ?
            ");

            $stmt->execute([
                $room_number,
                $type_id,
                $status,
                $room_id
            ]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['room_message'] = [
                    'type' => 'success',
                    'text' => 'Room details updated successfully!'
                ];
            } else {
                $check = $pdo->prepare("SELECT room_id FROM rooms WHERE room_id = ?");
                $check->execute([$room_id]);

                $_SESSION['room_message'] = $check->fetch()
                    ? ['type' => 'info', 'text' => 'No changes were made to the room.']
                    : ['type' => 'warning', 'text' => 'Room not found.'];
            }
        }

        // DELETE ROOM
        elseif ($action === 'delete') {

            $room_id = filter_var($_POST['room_id'] ?? '', FILTER_VALIDATE_INT);

            if (!$room_id || $room_id <= 0) {
                throw new Exception('Invalid room ID.');
            }

            $stmt = $pdo->prepare("
                DELETE FROM rooms
                WHERE room_id = ?
            ");

            $stmt->execute([$room_id]);

            $_SESSION['room_message'] = $stmt->rowCount() > 0
                ? ['type' => 'success', 'text' => 'Room deleted successfully!']
                : ['type' => 'warning', 'text' => 'Room not found.'];
        }

        else {
            throw new Exception('Invalid room action.');
        }

    } catch (PDOException $e) {

        error_log("Room management error: " . $e->getMessage());

        if ($e->getCode() === '23000') {

            $_SESSION['room_message'] = [
                'type' => 'danger',
                'text' => 'Room number already exists, or this room has existing bookings and cannot be deleted.'
            ];

        } else {

            $_SESSION['room_message'] = [
                'type' => 'danger',
                'text' => 'Unable to process the room request. Please try again.'
            ];
        }

    } catch (Exception $e) {

        $_SESSION['room_message'] = [
            'type' => 'warning',
            'text' => $e->getMessage()
        ];
    }

    header("Location: rooms.php");
    exit;
}

// Flash Message
$message = $_SESSION['room_message'] ?? null;
unset($_SESSION['room_message']);

// Fetch Rooms
$rooms = $pdo->query("
    SELECT
        r.*,
        rt.type_name,
        rt.price_per_night
    FROM rooms r
    JOIN room_types rt ON r.type_id = rt.type_id
    ORDER BY r.room_id DESC
")->fetchAll();

// Fetch Room Categories
$types = $pdo->query("
    SELECT * FROM room_types
    ORDER BY type_id ASC
")->fetchAll();

include '../includes/header.php';

?>

<div class="container my-4 my-lg-5">

    <!-- PAGE HEADING -->

    <div class="mb-4">

        <h3 class="fw-bold mb-1">
            <i class="fa-solid fa-door-open text-warning me-2"></i>
            Manage Hotel Rooms
        </h3>

        <p class="text-muted mb-0">
            Add, view, edit and manage hotel room information.
        </p>

    </div>


    <!-- ALERT MESSAGE -->

    <?php if ($message): ?>

        <div class="alert alert-<?php echo htmlspecialchars($message['type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show">

            <?php echo htmlspecialchars($message['text'], ENT_QUOTES, 'UTF-8'); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php endif; ?>


    <div class="row g-4">

        <!-- ROOM LIST -->

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white p-3 border-0">

                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-list me-2 text-warning"></i>
                        Room Details
                        <span class="badge bg-dark ms-2">
                            <?php echo count($rooms); ?>
                        </span>
                    </h5>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-dark">

                                <tr>
                                    <th class="p-3">Room #</th>
                                    <th>Category</th>
                                    <th>Rate</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (!empty($rooms)): ?>

                                    <?php foreach ($rooms as $r): ?>

                                        <?php

                                        $room_status = $r['status'];

                                        if ($room_status === 'Available') {
                                            $status_class = 'bg-success';
                                        } elseif ($room_status === 'Booked') {
                                            $status_class = 'bg-warning text-dark';
                                        } else {
                                            $status_class = 'bg-danger';
                                        }

                                        ?>

                                        <tr>

                                            <!-- ROOM NUMBER -->

                                            <td class="ps-3 fw-bold">
                                                Room <?php echo htmlspecialchars($r['room_number'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>


                                            <!-- CATEGORY -->

                                            <td>
                                                <?php echo htmlspecialchars($r['type_name'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>


                                            <!-- PRICE -->

                                            <td class="fw-semibold">
                                                $<?php echo number_format((float)$r['price_per_night'], 2); ?>
                                            </td>


                                            <!-- STATUS -->

                                            <td>
                                                <span class="badge <?php echo $status_class; ?>">
                                                    <?php echo htmlspecialchars($room_status, ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </td>


                                            <!-- ACTIONS -->

                                            <td>

                                                <div class="d-flex gap-2">

                                                    <!-- EDIT BUTTON -->

                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editRoom<?php echo (int)$r['room_id']; ?>"
                                                        title="Edit Room"
                                                    >
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>


                                                    <!-- DELETE FORM -->

                                                    <form method="POST" action="rooms.php"
                                                          onsubmit="return confirm('Are you sure you want to delete this room?');">

                                                        <input type="hidden" name="csrf_token"
                                                               value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                                                        <input type="hidden" name="room_id"
                                                               value="<?php echo (int)$r['room_id']; ?>">

                                                        <input type="hidden" name="action" value="delete">

                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                title="Delete Room">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>

                                                    </form>

                                                </div>

                                            </td>

                                        </tr>


                                        <!-- EDIT ROOM MODAL -->

                                        <div class="modal fade"
                                             id="editRoom<?php echo (int)$r['room_id']; ?>"
                                             tabindex="-1"
                                             aria-hidden="true">

                                            <div class="modal-dialog modal-dialog-centered">

                                                <div class="modal-content">

                                                    <div class="modal-header">

                                                        <h5 class="modal-title fw-bold">
                                                            <i class="fa-solid fa-pen text-primary me-2"></i>
                                                            Edit Room
                                                        </h5>

                                                        <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>

                                                    </div>

                                                    <form method="POST" action="rooms.php">

                                                        <div class="modal-body">

                                                            <input type="hidden" name="csrf_token"
                                                                   value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                                                            <input type="hidden" name="action" value="update">

                                                            <input type="hidden" name="room_id"
                                                                   value="<?php echo (int)$r['room_id']; ?>">


                                                            <div class="mb-3">

                                                                <label class="form-label fw-semibold">
                                                                    Room Number
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="room_number"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($r['room_number'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    maxlength="10"
                                                                    required
                                                                >

                                                            </div>


                                                            <div class="mb-3">

                                                                <label class="form-label fw-semibold">
                                                                    Room Category
                                                                </label>

                                                                <select name="type_id" class="form-select" required>

                                                                    <?php foreach ($types as $t): ?>

                                                                        <option
                                                                            value="<?php echo (int)$t['type_id']; ?>"
                                                                            <?php echo ((int)$r['type_id'] === (int)$t['type_id']) ? 'selected' : ''; ?>
                                                                        >
                                                                            <?php echo htmlspecialchars($t['type_name'], ENT_QUOTES, 'UTF-8'); ?>
                                                                            ($<?php echo number_format((float)$t['price_per_night'], 2); ?>)
                                                                        </option>

                                                                    <?php endforeach; ?>

                                                                </select>

                                                            </div>


                                                            <div class="mb-3">

                                                                <label class="form-label fw-semibold">
                                                                    Room Status
                                                                </label>

                                                                <select name="status" class="form-select" required>

                                                                    <option value="Available" <?php echo $room_status === 'Available' ? 'selected' : ''; ?>>
                                                                        Available
                                                                    </option>

                                                                    <option value="Booked" <?php echo $room_status === 'Booked' ? 'selected' : ''; ?>>
                                                                        Booked
                                                                    </option>

                                                                    <option value="Maintenance" <?php echo $room_status === 'Maintenance' ? 'selected' : ''; ?>>
                                                                        Maintenance
                                                                    </option>

                                                                </select>

                                                            </div>

                                                        </div>


                                                        <div class="modal-footer">

                                                            <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">
                                                                Close
                                                            </button>

                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="fa-solid fa-save me-1"></i>
                                                                Save Changes
                                                            </button>

                                                        </div>

                                                    </form>

                                                </div>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="5" class="text-center py-5">

                                            <i class="fa-solid fa-bed text-muted fs-1 mb-3"></i>

                                            <h5 class="fw-bold">No Rooms Found</h5>

                                            <p class="text-muted mb-0">
                                                Add your first hotel room using the form.
                                            </p>

                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>


        <!-- ADD NEW ROOM -->

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-dark text-white p-3">

                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-plus-circle text-warning me-2"></i>
                        Add New Room
                    </h5>

                </div>

                <div class="card-body p-4">

                    <form method="POST" action="rooms.php">

                        <input type="hidden" name="csrf_token"
                               value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                        <input type="hidden" name="action" value="add">


                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Room Number
                            </label>

                            <input
                                type="text"
                                name="room_number"
                                class="form-control"
                                placeholder="Example: 105"
                                maxlength="10"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Room Category
                            </label>

                            <select name="type_id" class="form-select" required>

                                <option value="">Select Room Category</option>

                                <?php foreach ($types as $t): ?>

                                    <option value="<?php echo (int)$t['type_id']; ?>">

                                        <?php echo htmlspecialchars($t['type_name'], ENT_QUOTES, 'UTF-8'); ?>

                                        ($<?php echo number_format((float)$t['price_per_night'], 2); ?>)

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Initial Status
                            </label>

                            <select name="status" class="form-select" required>

                                <option value="Available">Available</option>
                                <option value="Maintenance">Maintenance</option>

                            </select>

                        </div>


                        <button type="submit" class="btn btn-dark w-100 fw-bold">

                            <i class="fa-solid fa-plus me-2"></i>
                            Add Room

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>
