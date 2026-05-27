<!DOCTYPE html>
<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
include 'connect.php';

$shopName = 'Bicol Express';

$pendingCount = mysqli_num_rows(
    mysqli_query($con,
    "SELECT * FROM transactions WHERE status='pending'")
);
$approvedCount = mysqli_num_rows(
    mysqli_query($con,
    "SELECT * FROM transactions WHERE status='approved'")
);
$categoryCount = mysqli_num_rows(
    mysqli_query($con,
    "SELECT * FROM categories")
);
$itemCount = mysqli_num_rows(
    mysqli_query($con,
    "SELECT * FROM productbl")
);
?>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Admin Dashboard</title>
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#"><?php echo htmlspecialchars($shopName); ?> Admin</a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_categories.php">Categories</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_items.php">Items</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_account.php">My Account</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_transactions.php">Transactions</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php
            
        ?>
        <div class="container mt-4">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card p-4">
                        <h5>Bicol Express</h5>
                        <p><?php echo htmlspecialchars($shopName); ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4">
                        <h5>Pending Transactions</h5>
                        <p><?php echo (int)$pendingCount; ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4">
                        <h5>Approved Transactions</h5>
                        <p><?php echo (int)$approvedCount; ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4">
                        <h5>Categories</h5>
                        <p><?php echo (int)$categoryCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card p-4">
                        <h5>Items</h5>
                        <p><?php echo (int)$itemCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p><a href="admin_transactions.php" class="btn btn-primary">View All Transactions</a> <a href="admin_categories.php" class="btn btn-secondary">Manage Categories</a> <a href="admin_items.php" class="btn btn-secondary">Manage Items</a></p>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
