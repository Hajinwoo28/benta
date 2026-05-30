<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['userid'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$userid = (int)$_SESSION['userid'];
include 'connect.php';

$user = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT * FROM users WHERE userid=$userid")
);

/* ── Cancel transaction (POST from this page) ─────────────── */
if (isset($_POST['cancel_tx'])) {
    $tid = (int)$_POST['tid'];
    mysqli_query($con,
        "UPDATE transactions
         SET status='cancelled'
         WHERE id=$tid
           AND clientid=$userid
           AND status IN ('pending','approved')"
    );

    /* ── Restore product quantities only if the cancel actually succeeded ── */
    if (mysqli_affected_rows($con) > 0) {
        $restore_q = mysqli_query($con,
            "SELECT productid, quantity FROM transaction_products WHERE transaction_id=$tid"
        );
        while ($row = mysqli_fetch_assoc($restore_q)) {
            $pid = (int)$row['productid'];
            $qty = (int)$row['quantity'];
            mysqli_query($con,
                "UPDATE productbl SET quantity = quantity + $qty WHERE id = $pid"
            );
        }
    }

    echo "<script>alert('Transaction cancelled.'); window.location='cus_profile.php';</script>";
    exit;
}

/* ── Transactions (latest first) ─────────────────────────── */
$tx_q = mysqli_query($con,
    "SELECT * FROM transactions
     WHERE clientid=$userid
     ORDER BY orderdate DESC"
);
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My Account</title>
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-pending   { background:#fff3cd; color:#856404; border:1px solid #ffecb5; }
        .status-approved  { background:#cff4fc; color:#055160; border:1px solid #b6effb; }
        .status-completed { background:#d1e7dd; color:#0a3622; border:1px solid #a3cfbb; }
        .status-cancelled { background:#f8d7da; color:#842029; border:1px solid #f1aeb5; }
    </style>
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

    <!-- Personal Information -->
    <h4 class="mb-3">Personal Information</h4>
    <table class="table table-bordered" style="max-width:500px;">
        <tr><th style="width:160px">Username</th>
            <td><?php echo htmlspecialchars($user['username']); ?></td></tr>
        <tr><th>Full Name</th>
            <td><?php echo htmlspecialchars($user['fullname']); ?></td></tr>
        <tr><th>Contact</th>
            <td><?php echo htmlspecialchars($user['contact']); ?></td></tr>
        <tr><th>Delivery Address</th>
            <td><?php echo htmlspecialchars($user['address']); ?></td></tr>
    </table>

    <p class="mb-4">
        <a href="edit_profile.php?id=<?php echo $user['userid']; ?>" class="btn btn-primary">
            Update Account
        </a>
    </p>

    <!-- Transactions -->
    <h4 class="mb-3">My Transactions</h4>

    <?php if (mysqli_num_rows($tx_q) == 0): ?>
        <div class="alert alert-info">No transactions yet.</div>
    <?php else: ?>

    <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-secondary">
            <tr>
                <th>Actions</th>
                <th>ID</th>
                <th>Client Name</th>
                <th>Total Amount</th>
                <th>Delivery Address</th>
                <th>Contact Detail</th>
                <th>Order Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($tx = mysqli_fetch_assoc($tx_q)):
            $st = strtolower($tx['status']);
            $cancellable = ($st === 'pending' || $st === 'approved');
        ?>
            <tr>
                <!-- Actions -->
                <td class="text-nowrap">
                    <a href="transaction_view.php?id=<?php echo $tx['id']; ?>"
                       class="btn btn-sm btn-primary me-1">View</a>

                    <?php if ($cancellable): ?>
                    <form method="POST" class="d-inline"
                          onsubmit="return confirm('Cancel transaction #<?php echo $tx['id']; ?>?')">
                        <input type="hidden" name="tid" value="<?php echo $tx['id']; ?>">
                        <button type="submit" name="cancel_tx" class="btn btn-sm btn-danger">
                            Cancel
                        </button>
                    </form>
                    <?php endif; ?>
                </td>

                <td><?php echo $tx['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td>₱<?php echo number_format($tx['total'], 2); ?></td>
                <td><?php echo htmlspecialchars($user['address']); ?></td>
                <td><?php echo htmlspecialchars($user['contact']); ?></td>
                <td><?php echo date('M j, Y g:i A', strtotime($tx['orderdate'])); ?></td>
                <td>
                    <span class="status-badge status-<?php echo $st; ?>">
                        <?php echo ucfirst($st); ?>
                    </span>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>