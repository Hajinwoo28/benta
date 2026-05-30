<?php
session_start();
include("connect.php");

/* ── Auth guard ─────────────────────────────────────────── */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$q = mysqli_query($con, "SELECT * FROM productbl WHERE id=$id");
$item = mysqli_fetch_array($q);

if (!$item) {
    echo "<script>alert('Item not found.'); window.location='admin_items.php';</script>";
    exit;
}

$shopName = 'Bicol Express';

/* ── Action handlers ─────────────────────────────────────── */
if (isset($_POST["btnupdate"])) {
    $item_name   = mysqli_real_escape_string($con, trim($_POST["title"]));
    $category_id = (int)$_POST["category_id"];
    $description = mysqli_real_escape_string($con, trim($_POST["description"]));
    $quantity    = (int)$_POST["quantity"];
    $price       = (float)$_POST["price"];

    /* Only update image if a new file was actually uploaded */
    $img_field = $item["img"]; // keep existing image by default

    if (!empty($_FILES["image"]["name"])) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES["image"]["type"], $allowed)) {
            $ext    = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $safe   = "img_" . uniqid() . "." . $ext;
            $target = "covers/" . $safe;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $img_field = $target;
            }
        } else {
            echo "<script>alert('Invalid image type. Only JPG, PNG, GIF, WEBP allowed.');</script>";
        }
    }

    $img_escaped = mysqli_real_escape_string($con, $img_field);
    mysqli_query($con,
        "UPDATE productbl
         SET itemname='$item_name',
             categoryid=$category_id,
             description='$description',
             quantity=$quantity,
             price=$price,
             img='$img_escaped'
         WHERE id=$id"
    );
    echo "<script>window.location='admin_items.php';</script>";
    exit;
}

if (isset($_POST["btndelete"])) {
    mysqli_query($con, "DELETE FROM productbl WHERE id=$id");
    echo "<script>window.location='admin_items.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>Edit Item</title>
    <link href="css/styles.css" rel="stylesheet"/>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php"><?php echo htmlspecialchars($shopName); ?> Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link active" href="admin_items.php">Items</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_account.php">My Account</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_transactions.php">Transactions</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3>Edit Item</h3>
    <form method="POST" enctype="multipart/form-data">
        <table class="table table-bordered">
            <tr>
                <td>Item Name</td>
                <td>
                    <input type="text" name="title" class="form-control"
                           value="<?php echo htmlspecialchars($item["itemname"]); ?>" required>
                </td>
            </tr>
            <tr>
                <td>Category</td>
                <td>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php
                        $cats = mysqli_query($con, "SELECT * FROM categories");
                        while ($row = mysqli_fetch_assoc($cats)) {
                            $sel = ($row['id'] == $item['categoryid']) ? 'selected' : '';
                            echo "<option value=\"{$row['id']}\" $sel>"
                               . htmlspecialchars($row['category'])
                               . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Description</td>
                <td>
                    <input type="text" name="description" class="form-control"
                           value="<?php echo htmlspecialchars($item["description"]); ?>" required>
                </td>
            </tr>
            <tr>
                <td>Quantity</td>
                <td>
                    <input type="number" name="quantity" class="form-control"
                           value="<?php echo (int)$item["quantity"]; ?>" min="0" required>
                </td>
            </tr>
            <tr>
                <td>Price</td>
                <td>
                    <input type="number" name="price" class="form-control"
                           value="<?php echo $item["price"]; ?>" step="0.01" min="0" required>
                </td>
            </tr>
            <tr>
                <td>Current Image</td>
                <td>
                    <img src="<?php echo htmlspecialchars($item["img"]); ?>"
                         style="height:80px; border-radius:4px;">
                </td>
            </tr>
            <tr>
                <td>Replace Image</td>
                <td>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Leave blank to keep current image.</small>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="d-flex gap-2 p-3">
                    <input type="submit" name="btnupdate" class="btn btn-primary" value="Update Record">
                    <input type="submit" name="btndelete" class="btn btn-danger"
                           value="Delete Record"
                           onclick="return confirm('Delete this item permanently?')">
                </td>
            </tr>
        </table>
    </form>
    <a href="admin_items.php" class="btn btn-outline-secondary">← Back to Items</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
