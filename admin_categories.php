<?php
session_start();
include ("connect.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;

}
$shopName = 'Kabayan Express';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Admin Dashboard</title>
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body >
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
       
           
<h3>Category Management</h3>
<form method="POST" enctype="multipart/form-data">
<table class="table table-bordered m-3">
<tr>
<td>Category Name</td>
<td><input type="text" name="category_name" required></input></td>
</tr>
<tr>
<td colspan="2">
<input type="submit" name="btnsubmit" value="Add Category"></input>
</tr>
<?php
if(isset($_POST["btnsubmit"])){

$category_name = $_POST["category_name"];

mysqli_query($con, "insert into categories(category) values('$category_name')");
echo "<script>window.location='admin_categories.php';</script>";
}

?>

</table>
</form>
<table  class="table table-bordered m-2">
<thead>
<tr>
<th>Category ID</th>
<th>Category Name</th>
<th>View</th>

</tr>
</thead>
<tbody>
<?php
$q = mysqli_query($con, "select * from categories order by id desc");
while($r = mysqli_fetch_array($q)){
?>
<tr>
<td><?php echo $r["id"]; ?></td>
<td><?php echo $r["category"]; ?></td>
<td><a href="admin_category_view.php?id=<?php echo $r["id"]; ?>">View</a></td>
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


