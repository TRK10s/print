<?php

include('../db.php');

if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];

    $deleteOrderLineQuery = "DELETE FROM `order_line` WHERE orderID = $orderID";
    
    if (mysqli_query($conn, $deleteOrderLineQuery)) {
        $deleteOrderQuery = "DELETE FROM `order` WHERE orderID = $orderID";
        if (mysqli_query($conn, $deleteOrderQuery)) {
            echo "<script>alert('Order deleted successfully!'); window.location.href='vieworders.php';</script>";
        } else {
            echo "<script>alert('Database error: Unable to delete order.'); window.location.href='vieworders.php';</script>";
        }
    } else {
        echo "<script>alert('Database error: Unable to delete order lines.'); window.location.href='vieworders.php';</script>";
    }
}
?>
