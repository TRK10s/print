<?php
include('../db.php');
include('header.php');

if (!isset($_SESSION['username']) || $_SESSION['userRole'] !== 'Student') {
    echo "<script>alert('Access denied. Please log in as a student to view this page.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

$userId = $_SESSION['userID'];

// Handle form submissions for adding balance or canceling a card
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['card_id']) && isset($_POST['balance'])) {
        $cardId = $conn->real_escape_string($_POST['card_id']);
        $amountToAdd = (float)$_POST['balance'];

        if ($amountToAdd > 0) {
            $fetchQuery = "SELECT Balance FROM membershipcard WHERE CardID = '$cardId' AND userID = '$userId'";
            $result = $conn->query($fetchQuery);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentBalance = (float)$row['Balance'];
                $newBalance = $currentBalance + $amountToAdd;

                $updateQuery = "UPDATE membershipcard SET Balance = '$newBalance' WHERE CardID = '$cardId' AND userID = '$userId'";
                if ($conn->query($updateQuery)) {
                    echo "<script>alert('Money added successfully!'); window.location.href = 'view_membership_stud.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Error adding money. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Card not found or unauthorized access.');</script>";
            }
        } else {
            echo "<script>alert('Invalid amount. Please enter a positive value.');</script>";
        }
    } elseif (isset($_POST['cancel_card_id'])) {
        $cancelCardId = $conn->real_escape_string($_POST['cancel_card_id']);

        $cancelQuery = "DELETE FROM membershipcard WHERE CardID = '$cancelCardId' AND userID = '$userId'";
        if ($conn->query($cancelQuery)) {
            echo "<script>alert('Card canceled successfully!'); window.location.href = 'view_membership_stud.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error canceling card. Please try again.');</script>";
        }
    }
}

// Query to fetch memberships for the logged-in student
$query = "SELECT * FROM membershipcard WHERE userID = '$userId'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Membership</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">My Membership Details</h3>
        </div>
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>Card ID</th>
                            <th>Card Number</th>
                            <th>Balance</th>
                            <th>Expiry Date</th>
                            <th>QR Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Generate QR code content directly with Card ID and Balance
                                $QRCode = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode("Card ID: " . $row['CardID'] . ", Balance: " . $row['Balance']);

                                echo "<tr>";
                                echo "<td>" . ($row['CardID']) . "</td>";
                                echo "<td>" . ($row['CardNumber']) . "</td>";
                                echo "<td>RM " . ($row['Balance']) . "</td>";
                                echo "<td>" . ($row['ExpiryDate']) . "</td>";
                                echo "<td><img src='$QRCode' alt='QR Code' class='img-fluid'></td>";
                                echo "<td>
                                    <form method='POST' class='d-inline'>
                                        <input type='hidden' name='card_id' value='" . ($row['CardID']) . "'>
                                        <input type='number' name='balance' class='form-control form-control-sm d-inline' placeholder='Amount' required style='width: 100px;'>
                                        <button type='submit' class='btn btn-success btn-sm'>Add Balance</button>
                                    </form>
                                    <form method='POST' class='d-inline'>
                                        <input type='hidden' name='cancel_card_id' value='" . ($row['CardID']) . "'>
                                        <button type='submit' class='btn btn-danger btn-sm'>Cancel Card</button>
                                    </form>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No memberships found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
