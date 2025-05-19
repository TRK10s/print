<?php
include('../db.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderID = $_POST['orderID'];
    $paymentMethod = $_POST['paymentMethod']; 
    $userID = $_SESSION['userID']; 

    $amountQuery = "
        SELECT SUM(order_line.Quantity * packages.PriceFloat) AS totalAmount, SUM(order_line.Quantity) AS totalQuantity
        FROM order_line
        INNER JOIN packages ON order_line.PackageID = packages.PackageID
        WHERE order_line.orderID = $orderID";
    $amountResult = mysqli_query($conn, $amountQuery);
    $amountRow = mysqli_fetch_assoc($amountResult);
    $totalAmount = $amountRow['totalAmount'];
    $totalQuantity = $amountRow['totalQuantity'];

    if (!$totalAmount) {
        echo "<script>alert('Failed to calculate the total amount.'); window.location.href='checkout.php?orderID=$orderID';</script>";
        exit;
    }

    if ($paymentMethod === 'membership') {
        $cardQuery = "SELECT * FROM membershipcard WHERE userID = $userID AND Status = 'active'";
        $cardResult = mysqli_query($conn, $cardQuery);

        if ($cardResult && mysqli_num_rows($cardResult) > 0) {
            $cardRow = mysqli_fetch_assoc($cardResult);
            $cardID = $cardRow['CardID'];
            $cardBalance = $cardRow['Balance'];

            if ($cardBalance >= $totalAmount) {
                $deductQuery = "UPDATE membershipcard SET Balance = Balance - $totalAmount WHERE CardID = $cardID";
                if (mysqli_query($conn, $deductQuery)) {
                    $reward = 0;
                    if ($totalQuantity < 6) {
                        $reward = 2;
                    } elseif ($totalQuantity >= 6 && $totalQuantity < 20) {
                        $reward = 5;
                    } elseif ($totalQuantity >= 20) {
                        $reward = 10;
                    }

                    if ($reward > 0) {
                        $rewardQuery = "UPDATE membershipcard SET Balance = Balance + $reward WHERE CardID = $cardID";
                        mysqli_query($conn, $rewardQuery);
                        echo "<script>alert('Payment successful! You received RM $reward as a reward for your order.');</script>";
                    } else {
                        echo "<script>alert('Payment successful!');</script>";
                    }
                } else {
                    die("Error deducting balance: " . mysqli_error($conn));
                }
            } else {
                echo "<script>alert('Insufficient balance. Card Balance: $cardBalance, Required: $totalAmount'); window.location.href='checkout.php?orderID=$orderID';</script>";
                exit;
            }
        } else {
            echo "<script>alert('No valid membership card found linked to your account or card not active.'); window.location.href='checkout.php?orderID=$orderID';</script>";
            exit;
        }
    }

    $qrCode = 'QR' . uniqid();
    $updateOrderQuery = "UPDATE `order` SET QR = '$qrCode', Status = 'Ordered' WHERE orderID = $orderID";
    mysqli_query($conn, $updateOrderQuery);

    $paymentDescription = $paymentMethod === 'membership' ? "Paid using membership card (Card ID: $cardID)" : "Paid in cash";
    $paymentQuery = "INSERT INTO payment (payment_descr, payment_date, order_id) VALUES ('$paymentDescription', NOW(), $orderID)";
    mysqli_query($conn, $paymentQuery);

    echo "<script>alert('Payment successful! QR Code: $qrCode'); window.location.href='vieworders.php';</script>";
    exit;
}

$orderID = $_GET['orderID'];
$orderQuery = "SELECT * FROM `order` WHERE orderID = $orderID";
$orderResult = mysqli_query($conn, $orderQuery);
$order = mysqli_fetch_assoc($orderResult);

$amountQuery = "
    SELECT SUM(order_line.Quantity * packages.PriceFloat) AS totalAmount
    FROM order_line
    INNER JOIN packages ON order_line.PackageID = packages.PackageID
    WHERE order_line.orderID = $orderID";
$amountResult = mysqli_query($conn, $amountQuery);
$amountRow = mysqli_fetch_assoc($amountResult);
$totalAmount = $amountRow['totalAmount'];
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Check Out</h3>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <input type="hidden" name="orderID" value="<?php echo $orderID; ?>">

                    <h5>Order Details</h5>
                    <p><strong>Order ID:</strong> <?php echo $order['orderID']; ?></p>
                    <p><strong>File:</strong> <?php echo $order['File']; ?></p>
                    <p><strong>Status:</strong> <?php echo $order['Status']; ?></p>
                    <p><strong>Total Amount:</strong> RM <?php echo $totalAmount; ?></p>

                    <h5>Payment</h5>
                    <div class="form-group">
                        <label for="paymentMethod">Select Payment Method</label>
                        <select name="paymentMethod" id="paymentMethod" class="form-control" required>
                            <option value="membership">Membership Card</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>

                    <br>
                    <button type="submit" class="btn btn-primary">Complete Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('paymentMethod').addEventListener('change', function () {
        const membershipDetails = document.getElementById('membershipDetails');
        if (this.value === 'membership') {
            membershipDetails.style.display = 'block';
        } else {
            membershipDetails.style.display = 'none';
        }
    });
</script>

<?php include('footer.php'); ?>
