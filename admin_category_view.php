<?php
include ("connect.php");
$id = $_GET["id"];
$q = mysqli_query($con, "select * from categories where id = $id");
$category = mysqli_fetch_array($q);
?>
<html>
<body>
<h3>Category Details</h3>
<form method="POST">
<label>Category Name:</label><br/>
<input type="text" name="category_name" value="<?php echo $category["category"]; ?>" required><br/>

<input type="submit" name="btnupdate" value="Update Record">
<input type="submit" name="btndelete" value="Delete Record">
<?php
if(isset($_POST["btnupdate"])){
$category_name = $_POST["category_name"];
mysqli_query($con, "update categories set category='$category_name' where id=$id");
echo "<script>window.location = 'admin_categories.php';</script>";
}


if(isset($_POST["btndelete"])){
mysqli_query($con, "delete from categories where id=$id");
echo "<script>window.location = 'admin_categories.php';</script>";
}
?>
</form><br/>
<a href="admin_categories.php">Back to Homepage</a>
</body>
</html>