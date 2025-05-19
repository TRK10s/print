<?php
include('../db.php');
include('header.php');

if (!isset($_SESSION['username']) || $_SESSION['userRole'] !== 'Admin') {
    echo "<script>alert('Access denied. Please log in as an admin to view this page.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

$query = "SELECT membershipcard.*, users.username AS studentName 
          FROM membershipcard 
          JOIN users ON membershipcard.userID = users.userID";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Memberships</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">All Membership Details</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Card ID</th>
                    <th>Student Name</th>
                    <th>Card Number</th>
                    <th>Balance</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . ($row['CardID']) . "</td>";
                        echo "<td>" . ($row['studentName']) . "</td>";
                        echo "<td>" . ($row['CardNumber']) . "</td>";
                        echo "<td>RM " . (($row['Balance'])) . "</td>";
                        echo "<td>" . ($row['ExpiryDate']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No memberships found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
