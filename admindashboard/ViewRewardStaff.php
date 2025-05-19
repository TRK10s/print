<?php

include('../db.php');
include('header.php');
include('sidebar.php');
include('footer.php');

// Check if session variable userRole exists
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'Admin') {
    echo "Access denied. Only admins can view this page.";
    exit;
}

// Fetch all staff with their total sales and calculate bonuses
$query = "SELECT u.userID, u.username, u.userPhone, 
                 IFNULL(SUM(i.Amount), 0) AS TotalSales
          FROM users u
          LEFT JOIN invoice i ON u.userID = i.userID
          WHERE u.userRole = 'Staff'
          GROUP BY u.userID, u.username, u.userPhone
          ORDER BY u.username ASC";
$result = mysqli_query($conn, $query);

?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Staff Rewards</h3>
        </div>

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Username</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Total Sales </th>
                            <th scope="col">Bonus</th>
                            <th scope="col">QR Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $counter = 1;
                            while ($staff = mysqli_fetch_assoc($result)) {
                                $totalSales = $staff['TotalSales'];
                                $bonus = 0;

                                // Calculate bonus based on Total Sales
                                if ($totalSales > 450) {
                                    $bonus = 150;
                                } elseif ($totalSales > 350) {
                                    $bonus = 120;
                                } elseif ($totalSales > 280) {
                                    $bonus = 80;
                                } elseif ($totalSales > 200) {
                                    $bonus = 50;
                                }

                                // Generate QR code data
                                $qr_data = urlencode("StaffID: {$staff['userID']}, Name: {$staff['username']}, Phone: {$staff['userPhone']}, TotalSales: RM{$totalSales}, Bonus: RM{$bonus}");
                                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qr_data}";

                                echo "<tr>";
                                echo "<td>{$counter}</td>";
                                echo "<td>" . htmlspecialchars($staff['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($staff['userPhone']) . "</td>";
                                echo "<td>RM " . number_format($totalSales, 2) . "</td>";
                                echo "<td>RM " . number_format($bonus, 2) . "</td>";
                                echo "<td><img src='{$qr_url}' alt='QR Code'></td>";
                                echo "</tr>";

                                $counter++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No staff records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>