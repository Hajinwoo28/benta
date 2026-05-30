<!DOCTYPE html>
<?php
session_start(); 
$current_user = $_SESSION["userid"]; 

include ("connect.php");

?>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Unting Pukpok Tiklop Shop</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
         <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
            <div class="container">
                  <a class="navbar-brand" href="user_dashboard.php">Unting Pukpok Tiklop Shop</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="cart.php">cart</a></li>
                        <li class="nav-item">   <a href="about.php" class="nav-link active">about</a></li>
                      <li class="nav-item">   <a href="user_dashboard.php" class="nav-link active">home</a></li> 
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                              
                                <li><a class="dropdown-item" href="cus_profile.php?id=<?php echo $current_user; ?>">Profile</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page content-->
        <div class="container mt-4"> 
            <div class="row">
            <?php 
                $q = mysqli_query($con, "SELECT * FROM productbl ORDER BY itemname");
                while($r = mysqli_fetch_array($q))
                {
            ?>
            <div class= "col-3">
            <div class="card mb-3">
              <img src="<?php echo $r["img"]; ?>"  class="card-img-top" alt="..." style = "height: 200px; !important;">
              <div class="card-body">
                <h5 class="card-title"><?php echo $r['itemname']; ?></h5>
                  
                <p>Description :<br><?php echo $r['description']; ?></p>
                <p class="card-text">QTY: 
                    <?php 
                    if($r['quantity'] == 0){
                 echo "Out of Stock";
                    
                } else {
                    echo $r['quantity'];
                }
                 ?></p>
                <?php
             if($r['quantity'] > 0){
                ?>
                <a href="addcart.php?id=<?php echo $r['id']; ?>" class="btn btn-primary">Add to Cart</a>
                <?php
            }
                ?>
              </div>
            </div>
        </div>
        <?php
        }
        ?>
</div>
</div>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>