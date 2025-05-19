<?php
include('../db.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];

    // Check if the user already has a membership card
    $checkQuery = "SELECT * FROM membershipcard WHERE userID = '$userID'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('You have already applied for a membership card.'); window.location.href = 'view_membership_stud.php';</script>";
        exit();
    }

    // Proceed to create a membership card
    $cardID = uniqid('CARD_'); 
    $cardNumber = mt_rand(1000000000, 9999999999); 

    $issueDate = date('Y-m-d'); 
    $expiryDate = date('Y-m-d', strtotime('+1 year')); 

    $balance = 0.00;
    $status = 'Active';

    $query = "INSERT INTO membershipcard (CardID, userID, CardNumber, issueDate, ExpiryDate, Balance, Status) 
              VALUES ('$cardID', '$userID', '$cardNumber', '$issueDate', '$expiryDate', '$balance', '$status')";

    if ($conn->query($query)) {
        echo "<script>alert('Membership card application successful!'); window.location.href = 'view_membership_stud.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to apply for membership. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Membership</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Apply for a Membership Card</h2>

        <?php
        // Check if the user already has a membership card
        $userID = $_SESSION['userID'];
        $checkQuery = "SELECT * FROM membershipcard WHERE userID = '$userID'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            echo "<p class='text-center text-danger'>You have already applied for a membership card.</p>";
        } else {
            echo '<form method="POST" class="text-center mt-4">
                      <button type="submit" class="btn btn-primary btn-lg">Apply For A Card</button>
                  </form>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
