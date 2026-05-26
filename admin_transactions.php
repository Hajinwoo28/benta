<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='index.php';</script>";
    exit;
}
include("connect.php");
$shopName = 'Kabayan Express';
$query = "select t.*, u.username, u.address, u.contact from transactions t left join users u on u.user_id=t.clientid order by t.orderdate desc";
$res = mysqli_query($con, $query);
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Admin Transactions</title>
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="admin_dashboard.php">Admin</a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_categories.php">Categories</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_items.php">Items</a></li>
                        <li class="nav-item"><a class="nav-link active" href="admin_account.php">My Account</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_transactions.php">Transactions</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <h2>Transactions</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Client</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['contact']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['orderdate']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <a href="transaction_view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
                     
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>



