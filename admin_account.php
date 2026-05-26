<!DOCTYPE html>
<?php
session_start();
include("connect.php");
?>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Admin Account - Change Password</title>
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
        <?php
        if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php");
            exit;
        }
    
        $id = (int)$_SESSION['userid'];
        $msg = "";
        $result = mysqli_query($con, "SELECT * FROM users WHERE userid=$id");
        $user = mysqli_fetch_assoc($result);
        ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card p-4">
                        <h3 class="mb-4">Change Password</h3>

                        <?php if (!empty($msg)) { ?>
                            <div class="alert alert-info" role="alert">
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php 
                        } 
                        ?>
                            <form method="POST" enctype="multipart/form-data">
                               <table class="table table-bordered m-3">
                               <tr>
                               <td>Admin Account</td>
                               <td><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></input></td>
                               </tr>
                               <tr>
                               <td>Admin Password</td>
                               <td><input type="password" name="password" required></input></td>
                               </tr>
                               <tr>
                               <td colspan="2">
                               <input type="submit" name="btnsubmit" value="Update Admin Account"></input>
                               </tr>
                               <?php
                              
                               $user = mysqli_fetch_assoc(
                                   mysqli_query($con, "SELECT * FROM users WHERE userid=$id")
                               );
                               
                               if(isset($_POST['btnsubmit'])){
                               
                                   $username = $_POST['username'];
                                   $password = $_POST['password'];
                               
                                   $update = mysqli_query($con,
                                       "UPDATE users 
                                        SET username='$username', 
                                           password='$password' 
                                        WHERE userid=$id"
                                   );
                               
                                   if($update){
                                       $msg = "Account updated successfully!";
                                   } else {
                                       $msg = "Error: " . mysqli_error($con);
                                   }
                               }
                               ?>
                               </table>
                               </form>
                      
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
