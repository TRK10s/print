<?php
include('../db.php');
include('header.php');
include('phpqrcode/qrlib.php'); 

if (!isset($_SESSION['userRole'])) {
    echo "User role is not set. Please log in.";
    exit;
}

$userID = $_SESSION['userID'];
$userRole = $_SESSION['userRole'];

if (is_Admin() || is_Staff()) {
    $query = "SELECT * FROM `order`";
    $result = mysqli_query($conn, $query);
} else {
    $query = "SELECT * FROM `order` WHERE userID = $userID";
    $result = mysqli_query($conn, $query);
}

?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manage Orders</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Manage Orders</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">View Orders</a>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (Is_Student()) { ?>
                    <a href="order.php">
                        <button style="margin-bottom: 15px;font-size: 17px;" class="btn btn-primary btn-sm">New Order</button>
                    </a>
                <?php } ?>
                <br>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">File</th>
                            <th scope="col">Order Date</th>
                            <th scope="col">QR Code</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $counter = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $orderID = $row['orderID'];

                                $amountQuery = "
                                    SELECT SUM(order_line.Quantity * packages.PriceFloat) AS totalAmount
                                    FROM order_line
                                    INNER JOIN packages ON order_line.PackageID = packages.PackageID
                                    WHERE order_line.orderID = $orderID";
                                $amountResult = mysqli_query($conn, $amountQuery);
                                $amountRow = mysqli_fetch_assoc($amountResult);
                                $totalAmount = $amountRow['totalAmount'];

                                $qrValue = ($row['Status'] && $row['orderID']) ? $row['Status'] . " - Order ID = " . $row['orderID'] : 'N/A';
                                $qrFilePath = 'qrcodes/' . $row['orderID'] . '.png'; 
                                
                                if ($row['QR']) {
                                    QRcode::png($qrValue, $qrFilePath, 'H', 4, 4);
                                }

                                echo "<tr>";
                                echo "<td>" . $counter . "</td>";
                                echo "<td>" . $row['File'] . "</td>";
                                echo "<td>" . $row['OrderDate'] . "</td>";
                                echo "<td>";
                                if ($row['QR']) {
                                    echo "<img src='$qrFilePath' alt='QR Code' width='100'>";
                                } else {
                                    echo "No QR Code";
                                }
                                echo "</td>";
                                echo "<td>RM " . number_format($totalAmount, 2) . "</td>";
                                echo "<td>" . $row['Status'] . "</td>";
                                echo "<td>";
                                if ($row['Status'] === 'Pending') {
                                    if(Is_Student()){
                                        echo "<a href='checkout.php?orderID=" . $row['orderID'] . "' class='btn btn-primary btn-sm'>Pay</a> ";

                                    }
                                    echo "<a href='edit_order.php?orderID=" . $row['orderID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                                    echo "<a href='delete_order.php?orderID=" . $row['orderID'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this order?\")'>Cancel</a>";
                                } else {
                                    echo "No Actions Needed";
                                }
                                echo "</td>";
                                echo "</tr>";
                                $counter++;
                            }
                        } else {
                            echo "<tr><td colspan='7'>No Orders Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
