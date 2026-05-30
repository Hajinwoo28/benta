<?php
session_start();
include("connect.php");

/* ── Auth guard ─────────────────────────────────────────── */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$q = mysqli_query($con, "SELECT * FROM categories WHERE id=$id");
$category = mysqli_fetch_array($q);

if (!$category) {
    echo "<script>alert('Category not found.'); window.location='admin_categories.php';</script>";
    exit;
}

$shopName = 'Bicol Express';

/* ── Action handlers ─────────────────────────────────────── */
if (isset($_POST["btnupdate"])) {
    $category_name = mysqli_real_escape_string($con, trim($_POST["category_name"]));
    mysqli_query($con, "UPDATE categories SET category='$category_name' WHERE id=$id");
    echo "<script>window.location = 'admin_categories.php';</script>";
    exit;
}

if (isset($_POST["btndelete"])) {
    mysqli_query($con, "DELETE FROM categories WHERE id=$id");
    echo "<script>window.location = 'admin_categories.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>Edit Category</title>
    <link href="css/styles.css" rel="stylesheet"/>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php"><?php echo htmlspecialchars($shopName); ?> Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="admin_categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_items.php">Items</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_account.php">My Account</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_transactions.php">Transactions</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3>Edit Category</h3>
    <form method="POST" class="mb-4">
        <table class="table table-bordered" style="max-width:500px;">
            <tr>
                <td>Category Name</td>
                <td>
                    <input type="text" name="category_name" class="form-control"
                           value="<?php echo htmlspecialchars($category["category"]); ?>" required>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="d-flex gap-2 p-3">
                    <input type="submit" name="btnupdate" class="btn btn-primary" value="Update Record">
                    <input type="submit" name="btndelete" class="btn btn-danger"
                           value="Delete Record"
                           onclick="return confirm('Delete this category?')">
                </td>
            </tr>
        </table>
    </form>
    <a href="admin_categories.php" class="btn btn-outline-secondary">← Back to Categories</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
