<!DOCTYPE html>
<?php
session_start();
include("connect.php");

/* ── Auth guard ─────────────────────────────────────────────── */
if (!isset($_SESSION["userid"])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$userid = (int)$_SESSION["userid"];

$user = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT * FROM users WHERE userid=$userid")
);

if (!$user) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

/* ── Handle PROCEED (checkout form submit) ───────────────────── */
$receipt = null;

if (isset($_POST['proceed'])) {

    /* 1 ── Snapshot cart before touching anything */
    $cart_snap = mysqli_query($con,
        "SELECT c.*, p.itemname, p.quantity AS stock
         FROM carts c
         JOIN productbl p ON p.id = c.itemid
         WHERE c.clientid = $userid"
    );
    $cart_rows_snap = [];
    while ($row = mysqli_fetch_assoc($cart_snap)) {
        $cart_rows_snap[] = $row;
    }

    if (empty($cart_rows_snap)) {
        echo "<script>alert('Your cart is empty.'); window.location='cart.php';</script>";
        exit;
    }

    /* 2 ── Compute totals from snapshot (never trust POST values) */
    $subtotal = 0;
    foreach ($cart_rows_snap as $r) {
        $subtotal += $r['quantity'] * $r['price'];
    }
    $shipping = 100;
    $total    = $subtotal + $shipping;

    /* 3 ── Insert transaction */
    mysqli_query($con,
        "INSERT INTO transactions(clientid, subtotal, fee, total, status, orderdate)
         VALUES($userid, $subtotal, $shipping, $total, 'pending', NOW())"
    );
    $txid = mysqli_insert_id($con);

    /* 4 ── Insert transaction_products + deduct quantities */
    $receipt_items = [];
    foreach ($cart_rows_snap as $r) {
        $pname = mysqli_real_escape_string($con, $r['itemname']);
        $qty   = (int)$r['quantity'];
        $price = (float)$r['price'];
        $pid   = (int)$r['itemid'];

        // insert line into transaction_products
        mysqli_query($con,
            "INSERT INTO transaction_products
                (transaction_id, productid, itemname, quantity, price)
             VALUES($txid, $pid, '$pname', $qty, $price)"
        );

        // deduct stock — cannot go below 0
        mysqli_query($con,
            "UPDATE productbl
             SET quantity = GREATEST(0, quantity - $qty)
             WHERE id = $pid"
        );

        $receipt_items[] = [
            'name'  => $r['itemname'],
            'qty'   => $qty,
            'price' => $price,
            'line'  => $qty * $price,
        ];
    }

    /* 5 ── Clear cart */
    mysqli_query($con, "DELETE FROM carts WHERE clientid=$userid");

    /* 6 ── Build receipt payload */
    $receipt = [
        'txid'     => $txid,
        'items'    => $receipt_items,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total'    => $total,
        'date'     => date('F j, Y  h:i A'),
        'username' => $user['username'],
        'fullname' => $user['fullname'],
        'contact'  => $user['contact'],
        'address'  => $user['address'],
    ];
}

/* ── Load live cart (empty after checkout) ───────────────────── */
$cq = mysqli_query($con,
    "SELECT c.*, p.itemname
     FROM carts c
     JOIN productbl p ON p.id = c.itemid
     WHERE c.clientid = $userid"
);
$cart_rows = [];
$subtotal  = 0;
while ($r = mysqli_fetch_assoc($cq)) {
    $line       = $r['quantity'] * $r['price'];
    $subtotal  += $line;
    $cart_rows[] = $r + ['line' => $line];
}
$shipping = 100;
$total    = $subtotal + $shipping;
?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cart – Unting Pukpok Tiklop Shop</title>
<link href="css/styles.css" rel="stylesheet">
<link rel="icon" type="image/x-icon" href="assets/favicon.ico">
<style>
/* ── Checkout card ─────────────────────────────────── */
.checkout-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 28px 32px;
    margin-top: 28px;
}
.checkout-card h4 {
    font-weight: 700;
    margin-bottom: 18px;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 10px;
}
.delivery-box {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 18px;
    font-size: .92rem;
}
.delivery-box .label {
    font-weight: 600;
    color: #495057;
    min-width: 90px;
    display: inline-block;
}
.totals-table td { padding: 5px 8px; }
.totals-table .grand td {
    font-weight: 700;
    font-size: 1.05rem;
    border-top: 2px solid #343a40;
    padding-top: 9px;
}

/* ── Receipt overlay ───────────────────────────────── */
#receipt-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
#receipt-overlay.show { display: flex; }
#receipt-box {
    background: #fff;
    border-radius: 12px;
    padding: 36px 40px;
    max-width: 540px;
    width: 94%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0,0,0,.3);
    animation: popIn .22s ease;
}
@keyframes popIn {
    from { transform: scale(.88); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.receipt-icon {
    width: 62px; height: 62px;
    background: #198754;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 14px;
}
.receipt-icon svg { width: 32px; fill: #fff; }
#receipt-box h4    { text-align: center; font-size: 1.25rem; margin-bottom: 4px; }
.receipt-sub       { text-align: center; color: #666; font-size: .88rem; margin-bottom: 20px; }
.receipt-meta      { font-size: .875rem; color: #444; margin-bottom: 16px; line-height: 1.75; }
.receipt-meta b    { color: #222; }
.r-table           { width: 100%; border-collapse: collapse; font-size: .875rem; margin-bottom: 12px; }
.r-table th        { background: #f0f0f0; padding: 7px 10px; text-align: left; }
.r-table td        { padding: 7px 10px; border-bottom: 1px solid #eee; }
.r-totals tr td:last-child { text-align: right; }
.r-totals .grand td {
    font-weight: 700; font-size: 1rem;
    border-top: 2px solid #333; padding-top: 8px;
}
.receipt-btns { display: flex; gap: 10px; margin-top: 22px; }
.receipt-btns a {
    flex: 1; text-align: center; padding: 10px;
    border-radius: 7px; font-size: .9rem;
    text-decoration: none; color: #fff;
}
.btn-view  { background: #0d6efd; }
.btn-home  { background: #6c757d; }
.pending-badge {
    display: inline-block;
    background: #fff3cd; color: #856404;
    border: 1px solid #ffecb5;
    border-radius: 20px; padding: 2px 14px;
    font-size: .8rem; font-weight: 600;
}
</style>
</head>
<body>

<!-- ── Navbar ───────────────────────────────────────────── -->
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
                <li class="nav-item"><a class="nav-link active" href="cart.php">Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navDrop" href="#"
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

<!-- ── Main ─────────────────────────────────────────────── -->
<div class="container mt-4">

<?php if (empty($cart_rows) && !$receipt): ?>
<!-- Empty cart state -->
<div class="text-center py-5">
    <h3>🛒 Your cart is empty</h3>
    <p class="text-muted mt-2">Looks like you haven't added anything yet.</p>
    <a href="user_dashboard.php" class="btn btn-primary mt-2">Browse Items</a>
</div>

<?php else: ?>

<!-- ── Section 1: Cart Items ────────────────────────────── -->
<h3 class="mb-3">My Cart</h3>

<table class="table table-striped table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php $n = 1; foreach ($cart_rows as $r): ?>
        <tr>
            <td><?php echo $n++; ?></td>
            <td><?php echo htmlspecialchars($r['itemname']); ?></td>
            <td><?php echo $r['quantity']; ?></td>
            <td>₱<?php echo number_format($r['price'], 2); ?></td>
            <td>₱<?php echo number_format($r['line'], 2); ?></td>
            <td>
                <a href="removecart.php?id=<?php echo $r['id']; ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Remove this item from cart?')">
                   Remove
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- ── Section 2: Checkout Summary ─────────────────────── -->
<div class="checkout-card">
    <h4>🧾 Checkout Summary</h4>

    <!-- Delivery Details -->
    <div class="delivery-box">
        <div class="mb-1">
            <span class="label">Customer:</span>
            <?php echo htmlspecialchars($user['fullname']); ?>
            <small class="text-muted">(<?php echo htmlspecialchars($user['username']); ?>)</small>
        </div>
        <div class="mb-1">
            <span class="label">Contact:</span>
            <?php echo htmlspecialchars($user['contact']); ?>
        </div>
        <div>
            <span class="label">Deliver to:</span>
            <?php echo htmlspecialchars($user['address']); ?>
        </div>
    </div>

    <!-- Order Lines -->
    <table class="table table-bordered table-sm mb-3">
        <thead class="table-secondary">
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cart_rows as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['itemname']); ?></td>
                <td class="text-center"><?php echo $r['quantity']; ?></td>
                <td class="text-end">₱<?php echo number_format($r['price'], 2); ?></td>
                <td class="text-end">₱<?php echo number_format($r['line'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table ms-auto" style="min-width:240px;">
        <tr>
            <td>Subtotal</td>
            <td class="text-end">₱<?php echo number_format($subtotal, 2); ?></td>
        </tr>
        <tr>
            <td>Shipping fee</td>
            <td class="text-end">₱<?php echo number_format($shipping, 2); ?></td>
        </tr>
        <tr class="grand">
            <td>Total</td>
            <td class="text-end">₱<?php echo number_format($total, 2); ?></td>
        </tr>
    </table>

    <!-- Proceed button -->
    <form method="POST" class="mt-4"
          onsubmit="return confirm('Confirm your order for ₱<?php echo number_format($total,2); ?>?')">
        <button type="submit" name="proceed" class="btn btn-success btn-lg px-5">
            ✔ Proceed
        </button>
        <a href="user_dashboard.php" class="btn btn-outline-secondary btn-lg ms-2">
            Continue Shopping
        </a>
    </form>
</div>

<?php endif; ?>
</div><!-- /container -->

<!-- ── Receipt Overlay (shown after successful checkout) ── -->
<?php if ($receipt): ?>
<div id="receipt-overlay" class="show">
    <div id="receipt-box">

        <div class="receipt-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.3 5.3a1 1 0 0 0-1.4 0L9 15.2 5.1 11.3a1 1 0 0 0-1.4
                         1.4l4.6 4.6a1 1 0 0 0 1.4 0l10.6-10.6a1 1 0 0 0 0-1.4z"/>
            </svg>
        </div>

        <h4>Order Placed!</h4>
        <p class="receipt-sub">
            Transaction #<?php echo $receipt['txid']; ?>&nbsp;&nbsp;·&nbsp;&nbsp;
            <?php echo $receipt['date']; ?>
        </p>

        <!-- Delivery info -->
        <div class="receipt-meta">
            <div><b>Customer:</b> <?php echo htmlspecialchars($receipt['fullname']); ?></div>
            <div><b>Contact:</b>  <?php echo htmlspecialchars($receipt['contact']); ?></div>
            <div><b>Deliver to:</b> <?php echo htmlspecialchars($receipt['address']); ?></div>
        </div>

        <!-- Items -->
        <table class="r-table">
            <thead>
                <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
            </thead>
            <tbody>
            <?php foreach ($receipt['items'] as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['qty']; ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td>₱<?php echo number_format($item['line'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <table class="r-table r-totals">
            <tr><td>Subtotal</td><td>₱<?php echo number_format($receipt['subtotal'], 2); ?></td></tr>
            <tr><td>Shipping fee</td><td>₱100.00</td></tr>
            <tr class="grand">
                <td>Total</td>
                <td>₱<?php echo number_format($receipt['total'], 2); ?></td>
            </tr>
        </table>

        <p class="mt-2 mb-0" style="font-size:.82rem; color:#888;">
            Status: <span class="pending-badge">⏳ Pending</span>
            &nbsp;— we'll update you once approved.
        </p>

        <div class="receipt-btns">
            <a href="transaction_view.php?id=<?php echo $receipt['txid']; ?>" class="btn-view">
                View Order
            </a>
            <a href="user_dashboard.php" class="btn-home">
                Continue Shopping
            </a>
        </div>

    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>