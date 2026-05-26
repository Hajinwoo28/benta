<?php
session_start();
require_once 'connect.php';

if (!isset($_GET['id'])) {
    exit('transaction id is required.');
}

$transaction_id = intval($_GET['id']);

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

$sql = "select t.*, u.username, u.address, u.contact
        from transactions t
        left join users u on u.user_id = t.clientid
        where t.id = $transaction_id";

$result = mysqli_query($con, $sql);
if (!$result) {
    die("query error: " . mysqli_error($con));
}
$transaction = mysqli_fetch_assoc($result);
if (!$transaction) {
    exit('transaction not found.');
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>transaction #<?php echo $transaction['id']; ?></title>

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
<div class="container">

<div class="card p-3">

    <h3>transaction #<?php echo $transaction['id']; ?></h3>

    <p>
        <b>client:</b>
        <?php echo $transaction['username']; ?>
    </p>

    <p>
        <b>contact:</b>
        <?php echo $transaction['contact']; ?>
    </p>

    <p>
        <b>address:</b>
        <?php echo $transaction['address']; ?>
    </p>

    <p>
        <b>fee:</b>
        <?php echo number_format($transaction['fee'],2); ?>
    </p>

    <p>
        <b>subtotal:</b>
        <?php echo number_format($transaction['subtotal'],2); ?>
    </p>

    <h3>
        <b>total:</b>
        <?php echo number_format($transaction['total'],2); ?>
    </h3>

    <p>
        <b>status:</b>
        <?php echo $transaction['status']; ?>
    </p>

    <p>
        <b>order date:</b>
        <?php echo $transaction['orderdate']; ?>
    </p>

    <hr>
    <?php
    if ($role == 'admin' && strtolower($transaction['status']) == 'pending') {
    ?>

    <form method="POST">

        <input type="hidden" name="tid"
        value="<?php echo $transaction['id']; ?>">

        <button type="submit"
        name="approve_btn"
        onclick="return confirm('approve this transaction?')">
            approve
        </button>

        <?php

        if (isset($_POST['approve_btn'])) {

            $tid = intval($_POST['tid']);

            mysqli_query($con,
                "update transactions
                 set status='approved'
                 where id=$tid");

            echo "<script>
            alert('transaction approved');
            window.location='transaction_view.php?id=$tid';
            </script>";
        }

        ?>

    </form>
    <?php } ?>
    <?php
    if ($role == 'admin' && strtolower($transaction['status']) == 'approved') {
    ?>

    <form method="POST">

        <input type="hidden" name="tid"
        value="<?php echo $transaction['id']; ?>">

        <button type="submit"
        name="complete_btn"
        onclick="return confirm('complete this transaction?')">
            complete
        </button>

        <?php

        if (isset($_POST['complete_btn'])) {

            $tid = intval($_POST['tid']);

            mysqli_query($con,
                "update transactions
                 set status='completed'
                 where id=$tid");

            echo "<script>
            alert('transaction completed');
            window.location='transaction_view.php?id=$tid';
            </script>";
        }
        ?>

    </form>

    <?php } ?>
    <?php
    if (
        strtolower($transaction['status']) == 'pending'
        ||
        strtolower($transaction['status']) == 'approved'
    ) {
    ?>

    <form method="POST">

        <input type="hidden" name="tid"
        value="<?php echo $transaction['id']; ?>">

        <button type="submit"
        name="cancel_btn"
        onclick="return confirm('cancel this transaction?')">
            cancel
        </button>

        <?php

        if (isset($_POST['cancel_btn'])) {

            $tid = intval($_POST['tid']);

            mysqli_query($con,
                "update transactions
                 set status='cancelled'
                 where id=$tid");

            echo "<script>
            alert('transaction cancelled');
            window.location='transaction_view.php?id=$tid';
            </script>";
        }

        ?>

    </form>

    <?php } ?>

    <hr>

    <h3>items</h3>

    <?php

  $tid = intval($transaction_id);

$items = mysqli_query($con,
    "select * from transaction_items where transaction_id=$tid"
);

    if (mysqli_num_rows($items) > 0) {

    ?>
    <table class="table table-bordered table-striped" >

        <tr>
            <th>item</th>
            <th>qty</th>
            <th>price</th>
            <th>total</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($items)) {
        ?>

        <tr>

            <td>

                <?php

                if ($row['itemname'] != '') {

                    echo $row['itemname'];

                } else {

                    $p = mysqli_query($con,
                        "select itemname
                         from productbl
                         where id=".$row['productid']);

                    $p = mysqli_fetch_assoc($p);

                    if ($p) {
                        echo $p['itemname'];
                    } else {
                        echo 'product';
                    }
                }

                ?>

            </td>

            <td>
                <?php echo $row['quantity']; ?>
            </td>

            <td>
                <?php echo number_format($row['price'],2); ?>
            </td>

            <td>
                <?php
                echo number_format(
                    $row['quantity'] * $row['price'],
                    2
                );
                ?>
            </td>

        </tr>

        <?php } ?>

    </table>

    <?php
    } else {
        echo "<p>no items found.</p>";
    }
    ?>

    <br>

    <a href="<?php
    echo ($role == 'admin')
        ? 'admin_transactions.php'
        : 'cus_profile.php';
    ?>" class="btn btn-secondary">
        back
    </a>

</div>
</div>

</body>
</html>