<?php
session_start();
include ("connect.php");
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;

}
$shopName = 'Bicol Express';
?>
<!DOCTYPE html>
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
        <div class="container mt-4">
           
<h3>Item Management</h3>
<form method="POST" enctype="multipart/form-data" class="mb-4 " >
<table class="table table-bordered">
<tr>
<td>item name</td>
<td><input type="text" name="title" required></input></td>
</tr>

<tr>
    <td><label>Select Category:</label></td>
    <td>
        <select name="category_id" required>
            <option value="">Select Category</option>

        <?php
        
        $categories = mysqli_query($con,
            "SELECT * FROM categories");

        while($row = mysqli_fetch_assoc($categories)){
        ?>

            <option value="<?php echo $row['id']; ?>">
                <?php echo $row['category']; ?>
            </option>

        <?php } ?>

    </select>
    </td>
</tr>
<tr>
<td>description</td>
<td><input type="text" name="description" required></input></td>
</tr>
<tr>
<td>Quantity</td>
<td><input type="number" name="quantity" required></input></td>
</tr>
<tr>
<td>price</td>
<td><input type="number" name="price" step="0.01" required></input></td>
</tr>
<tr>
<td>Image</td>
<td><input type="file" name="image" required></input></td>
</tr>
<tr>
<td colspan="2">
<input type="submit" name="btnsubmit" value="Add Book"></input>
<?php
if(isset($_POST["btnsubmit"])){
$title = $_POST["title"];
$category_id = $_POST["category_id"];
$description = $_POST["description"];
$quantity = $_POST["quantity"];
$price = $_POST["price"];
$image = "covers/".basename($_FILES["image"]["name"]);
if(move_uploaded_file($_FILES["image"]["tmp_name"],$image)){
mysqli_query($con, "insert into productbl(itemname,categoryid,description,quantity,price,img) values('$title',$category_id,'$description',$quantity,$price,'$image')");
echo "<script>window.location='admin_items.php';</script>";
}
}
?>
</td>
</tr>
</table>
</form>
<table class="table table-bordered m-2">
<thead>
<tr>
<th>Image</th>
<th>Title</th>
<th>Category</th>
<th>Description</th>
<th>Quantity</th>
<th>Price</th>
<th>Actions</th>


</tr>
</thead>
<tbody>
<?php
$q = mysqli_query($con,

    "SELECT
            productbl.id, 
            productbl.itemname,
            productbl.description,
            productbl.quantity,
            productbl.price,
            productbl.img,
            categories.category
     
      FROM productbl

     INNER JOIN categories
     ON productbl.categoryid = categories.id"
);
while($r = mysqli_fetch_array($q)){
?>
<tr>
<td><img src="<?php echo $r["img"]; ?>" style="width:50px;"/></td>
<td><?php echo $r["itemname"]; ?></td>
<td><?php echo $r["category"]; ?></td>
<td><?php echo $r["description"]; ?></td>
<td><?php echo number_format($r["quantity"],0); ?></td>
<td><?php echo number_format($r["price"],2); ?></td>
<td><a href="admin_item_view.php?id=<?php echo $r["id"]; ?>">View</a></td>
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


