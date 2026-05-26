<!DOCTYPE html>
<?php
include ("connect.php");
?>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
</head>
</html>
<body>
      <form method="POST">
        <div class="container" style="text-align: center; margin-top: 100px; border: 1px solid #ccc; border-radius: 5px; padding: 20px; height:400px; width: 300px; margin-left: auto; margin-right: auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
          <h1 style="margin-top: 60px;">New Record</h1>
            <label>Username:</label><br/>
            <input type="text" name="username" required></input><br/>
            <label>Full Name:</label><br/>
            <input type="text" name="fullname" required></input><br/>
            <label>Contact:</label><br/>
            <input type="text" name="contact" required></input><br/>
            <label>Address:</label><br/>
            <input type="text" name="address" required></input><br/>
            <label>Password:</label><br/>
            <input type="password" name="password" required></input><br/>
            <input type="hidden" name="role" value="user">
            <form action="user_dashboard.php" method="POST">
              <input type="submit" name="btnsubmit" value="Save Record" style="margin-top: 20px;">
            </form>
      
        <?php
          if(isset($_POST["btnsubmit"]))
          {
              $username = $_POST["username"];
              $fullname = $_POST["fullname"];
              $contact = $_POST["contact"];
              $address = $_POST["address"];
              $password = $_POST["password"];
              $role = $_POST["role"];
          
              $q = mysqli_query($con, 
                  "INSERT INTO users(
                      username, 
                      name, 
                      contact, 
                      address, 
                      password, 
                      role
                  )
                  VALUES(
                      '$username', 
                      '$fullname', 
                      '$contact', 
                      '$address', 
                      '$password', 
                      '$role'
                  )"
              );
            echo "<script>alert('Record saved'); window.location='user_dashboard.php';</script>";
          }
        ?>
        <a href="index.php" style="display: block; margin-top: 10px;">login</a>
        </div>
      </form>
</body>
</html>