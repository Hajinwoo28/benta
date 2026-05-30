<!DOCTYPE html>
<?php
session_start();
include("connect.php");

if (!isset($_SESSION["userid"])) {
    die("not logged in");
}

$id = $_GET['id'];
$current_user = $_SESSION["userid"];


$user = mysqli_fetch_array(
    mysqli_query($con, "SELECT * FROM users WHERE userid='$current_user'")
);

$userid = $user['userid'];

$product = mysqli_fetch_array(
    mysqli_query($con, "SELECT * FROM productbl WHERE id='$id'")
);
?>
<html lang="en">
<head>
    <title>Add to Cart</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
<div class="container" style="text-align: center; padding:50px; max-width:500px; margin:auto; border:1px solid #ccc; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); margin-top: 60px;">
    <img src="<?php echo $product["img"]; ?>" style = "height: 280px;" class="card-img-top" alt="...">
    <h3><?php echo $product['itemname']; ?></h3>
    <p><b>Price:</b> <?php echo $product['price']; ?></p>
    <p><b>Stock:</b> <?php echo $product['quantity']; ?></p>

    <form method="POST">
        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">

        <button type="submit" name="add_to_cart">Add</button>

        <?php
        if (isset($_POST['add_to_cart'])) {

            $quantity = (int)$_POST['quantity'];

            if ($quantity < 1) $quantity = 1;
            if ($quantity > $product['quantity']) $quantity = $product['quantity'];

            mysqli_query($con,
                "INSERT INTO carts(
                clientid,itemid,quantity,price)
                 VALUES(
                 '$userid','$id','$quantity','".$product['price']."'
                 )
                 "
            );

            echo "<script>alert('Added to cart'); window.location='cart.php';</script>";
        }
        ?>
    </form>

    <a href="user_dashboard.php">Cancel</a>
</div>

</body>
</html>