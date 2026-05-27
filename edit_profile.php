<!DOCTYPE html>
<?php
session_start();
include'connect.php';
if (!isset($_SESSION['userid'])) {
                echo "<script>window.location='index.php';</script>";
                exit;
            }
            $id = intval($_GET["id"]);
            $current_user = $_SESSION['userid'];
            $current = mysqli_fetch_array(
                mysqli_query($con, "SELECT * FROM users WHERE userid=$current_user")
            );
            if (!$current || $current['userid'] != $id) {
                echo "<script>window.location='cus_profile.php';</script>";
                exit;
            }
            $user = $current;
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>
<body>

<h3>Update Account</h3>

<form method="POST">
    <label>Username:</label><br/>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>
    <label>Full Name:</label><br/>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>
    <label>Contact:</label><br/>
    <input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required><br><br>
    <label>Delivery Address:</label><br/>
    <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required><br><br>
    <label>Password:</label><br/>
    <input type="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required><br><br>
    <input type="submit" name="btnupdate" value="Update Account">
        <?php
            if (isset($_POST["btnupdate"])) {
                $username = mysqli_real_escape_string($con, $_POST["username"]);
                $name = mysqli_real_escape_string($con, $_POST["name"]);
                $address = mysqli_real_escape_string($con, $_POST["address"]);
                $contact = mysqli_real_escape_string($con, $_POST["contact"]);
                $password = mysqli_real_escape_string($con, $_POST["password"]);
            
                $upd = mysqli_query($con, 
                "UPDATE users SET
                    username='$username',
                    name='$name',
                    address='$address',
                    contact='$contact',
                    password='$password'
                WHERE 
                    userid=$id");
            
                if ($upd) {
                    echo "<script>alert('Account updated successfully'); window.location='cus_profile.php';</script>";
                    exit;
                } else {
                    die("update error: " . mysqli_error($con));
                }
            }
        ?>
</form>
<br/>
<a href="cus_profile.php">back to profile</a>
</body>
</html>