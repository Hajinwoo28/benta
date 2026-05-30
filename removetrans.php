<?php
session_start();
include("connect.php");

/* ── Auth guard ─────────────────────────────────────────── */
if (!isset($_SESSION['userid'])) {
    header('Location: index.php');
    exit;
}

$userid = (int)$_SESSION['userid'];
$role   = $_SESSION['role'] ?? 'user';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    /* Fetch the transaction to check ownership and status */
    $tx = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT clientid, status FROM transactions WHERE id=$id")
    );

    if ($tx && ($tx['clientid'] === $userid || $role === 'admin')) {

        /* Restore quantities only if it was still pending or approved */
        $st = strtolower($tx['status']);
        if ($st === 'pending' || $st === 'approved') {
            $items = mysqli_query($con,
                "SELECT productid, quantity FROM transaction_products WHERE transaction_id=$id"
            );
            while ($item = mysqli_fetch_assoc($items)) {
                $pid = (int)$item['productid'];
                $qty = (int)$item['quantity'];
                mysqli_query($con,
                    "UPDATE productbl SET quantity = quantity + $qty WHERE id=$pid"
                );
            }
        }

        /* Remove transaction products first, then the transaction */
        mysqli_query($con, "DELETE FROM transaction_products WHERE transaction_id=$id");
        mysqli_query($con, "DELETE FROM transactions WHERE id=$id");
    }
}

header('Location: cus_profile.php');
exit;
