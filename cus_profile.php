<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['userid'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$userid = $_SESSION['userid'];

include("connect.php");

$user = mysqli_fetch_assoc(
    mysqli_query($con, 
    "SELECT * FROM users WHERE userid=$userid")
);

$tx_q = mysqli_query($con,
"SELECT * FROM 
   transactions 
 WHERE 
   clientid='$userid' 
 ORDER BY 
   orderdate 
 DESC
"
);


if (isset($_POST['cancel_tx'])) {
    $tid = intval($_POST['tid']);

    $upd = mysqli_query($con,
        "UPDATE transactions 
         SET status='cancelled'
         WHERE id=$tid 
         AND clientid=$userid 
         AND status IN ('pending','approved')"
    );

    if ($upd) {
        echo "<script>alert('transaction cancelled'); window.location='cus_profile.php';</script>";
        exit;
    } else {
        die(mysqli_error($con));
    }
}
?>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>My Account</title>
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
         <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="user_dashboard.php">Unting Pukpok Tiklop Shop</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="cart.php">Cart</a></li>
                        <li class="nav-item">   <a href="about.php" class="nav-link active">About</a></li>
                         <li class="nav-item">   <a href="user_dashboard.php" class="nav-link active">Home</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                              
                                <li><a class="dropdown-item" href="cus_profile.php?id=<?php echo $userid; ?>">Profile</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
               <div class="container mt-4">
            <h2>Personal Information</h2>
            <table class="table table-bordered">
                <tr><th>Username</th><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
                <tr><th>Full Name</th><td><?php echo htmlspecialchars($user['fullname']); ?></td></tr>
                <tr><th>Contact</th><td><?php echo htmlspecialchars($user['contact']); ?></td></tr>
                <tr><th>Delivery Address</th><td><?php echo htmlspecialchars($user['address']); ?></td></tr>
            </table>
            <p><a href="edit_profile.php?id=<?php echo $user['userid']; ?>" class="btn btn-primary">Update Account</a></p>

            <h2>Transactions</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Total Amount</th>
                        <th>Delivery Address</th>
                        <th>Contact Detail</th>
                        <th>Order Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                       while ($transaction = mysqli_fetch_assoc($tx_q)){    
                    ?>
                        <tr>
                        
                            <td>
                                <a href="transaction_view.php?id=<?php echo $transaction['id']; ?>">view</a>
                            </td>
                        
                            <td><?php echo $transaction['id']; ?></td>
                        
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                        
                            <td><?php echo number_format($transaction['total'], 2); ?></td>
                        
                            <td><?php echo htmlspecialchars($user['address']); ?></td>
                        
                            <td><?php echo htmlspecialchars($user['contact']); ?></td>
                        
                            <td><?php echo $transaction['orderdate']; ?></td>
                        
                            <td><?php echo $transaction['status']; ?></td>
                        
                        </tr>
                    <?php 
                    } 
                    ?>
                </tbody>
            </table>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
 