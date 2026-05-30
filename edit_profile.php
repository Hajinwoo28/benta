<!DOCTYPE html>
<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$current_user = (int)$_SESSION['userid'];
$id           = (int)($_GET['id'] ?? 0);

/* Enforce: can only edit your own account */
if ($id !== $current_user) {
    echo "<script>window.location='cus_profile.php';</script>";
    exit;
}

$user = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT * FROM users WHERE userid=$id")
);

if (!$user) {
    echo "<script>window.location='cus_profile.php';</script>";
    exit;
}

/* ── Handle form submit ───────────────────────── */
$msg     = '';
$msgType = 'success';

if (isset($_POST['btnupdate'])) {
    $contact  = mysqli_real_escape_string($con, trim($_POST['contact']));
    $address  = mysqli_real_escape_string($con, trim($_POST['address']));
    $password = mysqli_real_escape_string($con, $_POST['password']);

    /* Require password field not empty */
    if ($password === '') {
        $msg     = 'Password cannot be empty.';
        $msgType = 'danger';
    } else {
        $upd = mysqli_query($con,
            "UPDATE users
             SET contact='$contact',
                 address='$address',
                 password='$password'
             WHERE userid=$id"
        );

        if ($upd) {
            echo "<script>alert('Account updated successfully.'); window.location='cus_profile.php';</script>";
            exit;
        } else {
            $msg     = 'Update failed: ' . mysqli_error($con);
            $msgType = 'danger';
        }
    }
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Update Account</title>
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>

<!-- ── Navbar ─────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
    <div class="container">
        <a class="navbar-brand" href="user_dashboard.php">Unting Pukpok Tiklop Shop</a>
        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#nav"
            aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" id="navDrop" href="#"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">Account</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navDrop">
                        <li><a class="dropdown-item" href="cus_profile.php">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Content ────────────────────────────────────────────── -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h4 class="mb-1">Update Account</h4>
                <p class="text-muted mb-4" style="font-size:.88rem;">
                    You can update your contact, delivery address, and password.
                </p>

                <?php if ($msg): ?>
                <div class="alert alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
                <?php endif; ?>

                <!-- Read-only info (not editable) -->
                <div class="mb-3 p-3 bg-light rounded border">
                    <small class="text-muted d-block mb-1">Username (cannot be changed)</small>
                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                </div>
                <div class="mb-4 p-3 bg-light rounded border">
                    <small class="text-muted d-block mb-1">Full Name (cannot be changed)</small>
                    <strong><?php echo htmlspecialchars($user['fullname']); ?></strong>
                </div>

                <!-- Editable fields -->
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact" class="form-control"
                               value="<?php echo htmlspecialchars($user['contact']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Delivery Address</label>
                        <input type="text" name="address" class="form-control"
                               value="<?php echo htmlspecialchars($user['address']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Enter new password" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="btnupdate" class="btn btn-primary px-4">
                            Save Changes
                        </button>
                        <a href="cus_profile.php" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>