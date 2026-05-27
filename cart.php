<?php
session_start();
include'connect.php';

if (!isset($_SESSION["userid"])) {
    die("not logged in");
}

$userid = $_SESSION["userid"];

$user_q = mysqli_query($con, "SELECT * FROM users WHERE userid='$userid'");
$user = mysqli_fetch_assoc($user_q);

if (!$user) {
    die("user not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Cart</title>
<link href="css/styles.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
      
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
                              
                                <li><a class="dropdown-item" href="cus_profile.php?id=<?php echo $userid; ?>">profile</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

<div class="container">

<h3>My One and Only Cart</h3>

<table class="table table-striped table-hover">
<tr>
    <th>Client Name</th>
    <th>Item Name</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Subtotal</th>
    <th>Action</th>
</tr>

<?php
$q = mysqli_query($con, "SELECT * FROM carts WHERE clientid='$userid'");

$subtotal = 0;

while ($r = mysqli_fetch_assoc($q)) {

    $line_total = $r['quantity'] * $r['price'];
    $subtotal += $line_total;

    $product = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT * FROM productbl WHERE id='{$r['itemid']}'")
    );
?>

<tr>
    <td><?php echo $user['username']; ?></td>
    <td><?php echo $product['itemname']; ?></td>
    <td><?php echo $r['quantity']; ?></td>
    <td><?php echo $r['price']; ?></td>
    <td><?php echo $line_total; ?></td>
    <td>
        <a href="removecart.php?id=<?php echo $r['id']; ?>" class="btn btn-danger">remove</a>
    </td>
</tr>

<?php 
} 
?>

</table>
<div class="container" style="margin-top: 370px;">
  <?php $total = $subtotal + 100; ?>
  
  <p>subtotal: <?php echo $subtotal; ?></p>
  <p>shipping: 100</p>
  <h3>total: <?php echo $total; ?></h3>
</div>

<form method="POST">
    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
    <input type="hidden" name="total" value="<?php echo $total; ?>">
    <input type="submit" name="checkout" class="btn btn-success" value="checkout">
</form>

<?php
if (isset($_POST['checkout'])) {

    $subtotal = $_POST['subtotal'];
    $total = $_POST['total'];


    $q = mysqli_query($con, 
      "INSERT INTO transactions(
          clientid, 
          subtotal, 
          fee, 
          total, 
          status, 
          orderdate
        )
        VALUES(
          '$userid', 
          '$subtotal', 
          '100', 
          '$total', 
          'pending', 
          NOW()
        )
      "
    );

    $txid = mysqli_insert_id($con);

  
    $cart = mysqli_query($con, "SELECT * FROM carts WHERE clientid='$userid'");

    while ($r = mysqli_fetch_assoc($cart)) {

        $q = mysqli_query($con, 
          "INSERT INTO transaction_items
            (
              transaction_id, 
              productid, 
              itemname, 
              quantity, 
              price
            )
            VALUES
            (
              '$txid', 
              '{$r['itemid']}', 
              '', 
              '{$r['quantity']}', 
              '{$r['price']}'
            )
          "
        );
    }

    $q = mysqli_query($con, 
      "delete from carts where clientid='$userid'"
    );

    echo "<script>
        alert('order placed successfully');
        window.location='user_dashboard.php';
    </script>";
}
?>
</form>
</div>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>