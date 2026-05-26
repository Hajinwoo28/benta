<?php
session_start();
include ("connect.php");

if(!isset($_SESSION["userid"])){
    $current_user = $_SESSION["userid"];
    $user = mysqli_fetch_array(
        $q = mysqli_query($con, "SELECT * FROM users WHERE userid='$current_user'")
    );
    if($user['role'] == 'admin'){
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
}
else{
echo "Hello, $_SESSION[userid]!";
echo "<br/><a href='logout.php'>Logout</a>";
}
?>

<form method="POST">
    <div class="container" style="text-align: center; margin-top: 100px; border: 1px solid #ccc; border-radius: 5px; padding: 30px; height: 400px; width: 300px; margin-left: auto; margin-right: auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <h1 style="margin-top: 60px;">Login</h1>
        <table style="margin-top: 120px; margin-left: auto; margin-right: auto;">
            <tr>
                <td>
                    <label>Username:</label> 
                </td>
                    <td><input type="text" name="username" required></td>
            </tr>
            <tr>
                <td><label>Password:</label></td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr style="text-align:center; margin-top: 50px;">
                <td colspan="2"><input type="submit" name="btnlogin" value="Login"></td>
            </tr>
        </table> 
        <?php
        if(isset($_POST["btnlogin"])){
        
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $result = mysqli_query($con,
        "SELECT * FROM users 
         WHERE username='$username' 
         AND password='$password'");
        
        $r = mysqli_fetch_assoc($result);
        
        if ($r) {
            $_SESSION["userid"] = $r["userid"];
            $_SESSION["role"] = $r["role"];
        
           
        
            if($r['role'] == 'admin'){
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
        
        } else {
            echo "Invalid login";
        }
        }
        
        ?>
        <a href="register.php" style="display: block; text-align: center; margin-top: 60px;">Register kana Pogi</a>
    </div>
</form>        