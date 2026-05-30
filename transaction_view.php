<!DOCTYPE html>
<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>window.location='cus_profile.php';</script>";
    exit;
}

$transaction_id = (int)$_GET['id'];
$userid         = (int)$_SESSION['userid'];
$role           = $_SESSION['role'] ?? 'user';

/* ── Fetch transaction + client info ────────────────────── */
$result = mysqli_query($con,
    "SELECT t.*, u.username, u.fullname, u.address, u.contact
     FROM transactions t
     LEFT JOIN users u ON u.userid = t.clientid
     WHERE t.id = $transaction_id"
);

if (!$result) {
    die("Query error: " . mysqli_error($con));
}

$transaction = mysqli_fetch_assoc($result);

if (!$transaction) {
    echo "<script>alert('Transaction not found.'); window.location='cus_profile.php';</script>";
    exit;
}

/* Security: non-admin users can only view their own transactions */
if ($role !== 'admin' && (int)$transaction['clientid'] !== $userid) {
    echo "<script>window.location='cus_profile.php';</script>";
    exit;
}

$st = strtolower($transaction['status']);

/* ── Action handlers ──────────────────────────────────── */
if ($role === 'admin' && isset($_POST['approve_btn'])) {
    $tid = (int)$_POST['tid'];
    mysqli_query($con, "UPDATE transactions SET status='approved' WHERE id=$tid");
    echo "<script>alert('Transaction approved.'); window.location='transaction_view.php?id=$tid';</script>";
    exit;
}

if ($role === 'admin' && isset($_POST['complete_btn'])) {
    $tid = (int)$_POST['tid'];
    mysqli_query($con, "UPDATE transactions SET status='completed' WHERE id=$tid");
    echo "<script>alert('Transaction completed.'); window.location='transaction_view.php?id=$tid';</script>";
    exit;
}

if (isset($_POST['cancel_btn'])) {
    $tid = (int)$_POST['tid'];
    /* Users can only cancel their own; admins can cancel any */
    $owner_check = ($role === 'admin') ? "" : "AND clientid=$userid";
    mysqli_query($con,
        "UPDATE transactions
         SET status='cancelled'
         WHERE id=$tid $owner_check
           AND status IN ('pending','approved')"
    );
    echo "<script>alert('Transaction cancelled.'); window.location='transaction_view.php?id=$tid';</script>";
    exit;
}

/* ── Items ──────────────────────────────────────────────── */
$items = mysqli_query($con,
    "SELECT * FROM transaction_products WHERE transaction_id=$transaction_id"
);
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Transaction #<?php echo $transaction['id']; ?></title>
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: .82rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-pending   { background:#fff3cd; color:#856404; border:1px solid #ffecb5; }
        .status-approved  { background:#cff4fc; color:#055160; border:1px solid #b6effb; }
        .status-completed { background:#d1e7dd; color:#0a3622; border:1px solid #a3cfbb; }
        .status-cancelled { background:#f8d7da; color:#842029; border:1px solid #f1aeb5; }
        .info-card { background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; padding:18px 22px; margin-bottom:20px; }
        .info-card .lbl { font-weight:600; color:#495057; min-width:110px; display:inline-block; }
    </style>
</head>
<body>

<!-- ── Navbar ─────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo ($role==='admin') ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>">
            <?php echo ($role === 'admin') ? 'Admin Panel' : 'Unting Pukpok Tiklop Shop'; ?>
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_transactions.php">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="cus_profile.php">My Account</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Content ────────────────────────────────────────────── -->
<div class="container mt-4">
<div class="row justify-content-center">
<div class="col-lg-8">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Transaction #<?php echo $transaction['id']; ?></h4>
        <span class="status-badge status-<?php echo $st; ?>"><?php echo ucfirst($st); ?></span>
    </div>

    <!-- Delivery Details -->
    <div class="info-card">
        <div class="mb-1"><span class="lbl">Customer:</span><?php echo htmlspecialchars($transaction['fullname'] ?: $transaction['username']); ?></div>
        <div class="mb-1"><span class="lbl">Username:</span><?php echo htmlspecialchars($transaction['username']); ?></div>
        <div class="mb-1"><span class="lbl">Contact:</span><?php echo htmlspecialchars($transaction['contact']); ?></div>
        <div class="mb-1"><span class="lbl">Deliver to:</span><?php echo htmlspecialchars($transaction['address']); ?></div>
        <div><span class="lbl">Order Date:</span><?php echo date('F j, Y g:i A', strtotime($transaction['orderdate'])); ?></div>
    </div>

    <!-- Items Table -->
    <h5 class="mb-3">Items Ordered</h5>
    <?php if (mysqli_num_rows($items) > 0): ?>
    <table class="table table-bordered table-striped mb-4">
        <thead class="table-secondary">
            <tr>
                <th>Item</th>
                <th class="text-center">Quantity</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($r = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td>
                <?php
                    if ($r['itemname'] != '') {
                        echo htmlspecialchars($r['itemname']);
                    } else {
                        $p = mysqli_fetch_assoc(
                            mysqli_query($con, "SELECT itemname FROM productbl WHERE id=".(int)$r['productid'])
                        );
                        echo $p ? htmlspecialchars($p['itemname']) : 'Unknown Item';
                    }
                ?>
                </td>
                <td class="text-center"><?php echo $r['quantity']; ?></td>
                <td class="text-end">₱<?php echo number_format($r['price'], 2); ?></td>
                <td class="text-end">₱<?php echo number_format($r['quantity'] * $r['price'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="text-muted">No items found for this transaction.</p>
    <?php endif; ?>

    <!-- Amounts -->
    <div class="card p-3 mb-4" style="max-width:300px; margin-left:auto;">
        <table class="table table-sm mb-0">
            <tr><td>Subtotal</td><td class="text-end">₱<?php echo number_format($transaction['subtotal'], 2); ?></td></tr>
            <tr><td>Shipping fee</td><td class="text-end">₱<?php echo number_format($transaction['fee'], 2); ?></td></tr>
            <tr>
                <td><strong>Total</strong></td>
                <td class="text-end"><strong>₱<?php echo number_format($transaction['total'], 2); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex flex-wrap gap-2 mb-4">

        <?php /* Admin: Approve (pending only) */ ?>
        <?php if ($role === 'admin' && $st === 'pending'): ?>
        <form method="POST" onsubmit="return confirm('Approve this transaction?')">
            <input type="hidden" name="tid" value="<?php echo $transaction['id']; ?>">
            <button type="submit" name="approve_btn" class="btn btn-success">✔ Approve</button>
        </form>
        <?php endif; ?>

        <?php /* Admin: Complete (approved only) */ ?>
        <?php if ($role === 'admin' && $st === 'approved'): ?>
        <form method="POST" onsubmit="return confirm('Mark this transaction as completed?')">
            <input type="hidden" name="tid" value="<?php echo $transaction['id']; ?>">
            <button type="submit" name="complete_btn" class="btn btn-primary">✔ Complete</button>
        </form>
        <?php endif; ?>

        <?php /* Cancel: visible to anyone if pending or approved */ ?>
        <?php if ($st === 'pending' || $st === 'approved'): ?>
        <form method="POST" onsubmit="return confirm('Cancel this transaction? This cannot be undone.')">
            <input type="hidden" name="tid" value="<?php echo $transaction['id']; ?>">
            <button type="submit" name="cancel_btn" class="btn btn-danger">✖ Cancel Transaction</button>
        </form>
        <?php endif; ?>

        <!-- Back -->
        <a href="<?php echo ($role === 'admin') ? 'admin_transactions.php' : 'cus_profile.php'; ?>"
           class="btn btn-outline-secondary">← Back</a>

    </div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>