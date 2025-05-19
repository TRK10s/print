<?php
include('../db.php');

// Check if 'userId' is provided in the URL
if (isset($_GET['userId']) && !empty($_GET['userId'])) {
    // Retrieve userId from the URL and escape it for security
    $userId = mysqli_real_escape_string($conn, $_GET['userId']);

    // Delete related rows in the `order_line` table
    $deleteOrderLinesQuery = "DELETE FROM `order_line` WHERE orderID IN (SELECT orderID FROM `order` WHERE userID = ?)";
    $stmtOrderLines = $conn->prepare($deleteOrderLinesQuery);
    $stmtOrderLines->bind_param("s", $userId);
    if (!$stmtOrderLines->execute()) {
        echo "Error deleting order lines: " . $stmtOrderLines->error;
        exit;
    }
    $stmtOrderLines->close();

    // Delete related rows in the `order` table
    $deleteOrdersQuery = "DELETE FROM `order` WHERE userID = ?";
    $stmtOrders = $conn->prepare($deleteOrdersQuery);
    $stmtOrders->bind_param("s", $userId);
    if (!$stmtOrders->execute()) {
        echo "Error deleting orders: " . $stmtOrders->error;
        exit;
    }
    $stmtOrders->close();

    // Delete the user's memberships
    $deleteMembershipsQuery = "DELETE FROM membershipcard WHERE userID = ?";
    $stmtMemberships = $conn->prepare($deleteMembershipsQuery);
    $stmtMemberships->bind_param("s", $userId);
    if (!$stmtMemberships->execute()) {
        echo "Error deleting memberships: " . $stmtMemberships->error;
        exit;
    }
    $stmtMemberships->close();

    // Now delete the user
    $deleteUserQuery = "DELETE FROM users WHERE userId = ?";
    $stmtUser = $conn->prepare($deleteUserQuery);
    $stmtUser->bind_param("s", $userId);
    if ($stmtUser->execute()) {
        // Redirect back to the user management page after successful deletion
        header("Location: viewusers.php");
        exit;
    } else {
        // Show an error message if the query fails
        echo "Error deleting user: " . $stmtUser->error;
    }
    $stmtUser->close();
} else {
    // Show a message if no userId is provided
    echo "No user ID provided.";
}
?>
