<?php
include ("connect.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    mysqli_query($con, "delete FROM transactions where id=$id");
}

header('Location: cus_profile.php');
exit;
?>      