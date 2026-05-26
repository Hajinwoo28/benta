<?php
include ("connect.php");
$id = $_GET["id"];
$q = mysqli_query($con, "select * from productbl where id = $id");
$item = mysqli_fetch_array($q);
?>
<html>
<body>
<h3>Item Management</h3>
<form method="POST" enctype="multipart/form-data" class="mb-4 " >
<table class="table table-bordered">
<tr>
<td>item name</td>
<td><input type="text" name="title" value="<?php echo $item["itemname"]; ?>" required></input></td>
</tr>

<tr>
    <td><label>Select Category:</label></td>
    <td>
        <select name="category_id" required>
            <option value="">Select Category</option>

        <?php
        
        $categories = mysqli_query($con,
            "select * from categories");

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
<td><input type="text" name="description" value="<?php echo $item["description"]; ?>" required></input></td>
</tr>
<tr>
<td>Quantity</td>
<td><input type="number" name="quantity" value="<?php echo $item["quantity"]; ?>" required></input></td>
</tr>
<tr>
<td>price</td>
<td><input type="number" name="price" value="<?php echo $item["price"]; ?>" step="0.01" required></input></td>
</tr>
<tr>
<td>Image</td>
<td><input type="file" name="image" required <?php echo $item["price"]; ?> ></input></td>
</tr>
<tr>
<td colspan="2">
<input type="submit" name="btnupdate" value="Update Record">
<input type="submit" name="btndelete" value="Delete Record">

<?php

if(isset($_POST["btnupdate"])){
$item_name = $_POST["title"];
$description = $_POST["description"];
$quantity = $_POST["quantity"];
$price = $_POST["price"];
$image = $_FILES["image"]["name"];
$target = "covers/".basename($image);
move_uploaded_file($_FILES['image']['tmp_name'], $target);
mysqli_query($con, "update productbl set itemname='$item_name',description='$description',quantity=$quantity,price=$price,img='$target' where id=$id");
echo "<script>window.location = 'admin_items.php';</script>";
}


if(isset($_POST["btndelete"])){
mysqli_query($con, "delete from productbl where id=$id");
echo "<script>window.location = 'admin_items.php';</script>";
}
?>

</td>
</tr>

</table>
</form>


<a href="admin_items.php">Back to Homepage</a>
</body>
</html>