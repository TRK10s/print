<?php
include('../db.php');
include('header.php');
include('sidebar.php');
include('footer.php');

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['userID'])) {
    echo "Error: You are not logged in.";
    exit();
}

// Get combined staff and sales data
$staff_id = $_SESSION['userID'];
$join_query = "
    SELECT 
        u.userID AS Staff_ID,
        u.username AS Username,
        u.userPhone AS Phone,
        u.userRole AS Role,
        IFNULL(SUM(i.Amount), 0) AS Total_Sales,
        IFNULL(COUNT(i.InvoiceID), 0) AS Total_Orders,
        CASE
            WHEN SUM(i.Amount) > 450 THEN 150
            WHEN SUM(i.Amount) > 350 THEN 120
            WHEN SUM(i.Amount) > 280 THEN 80
            WHEN SUM(i.Amount) > 200 THEN 50
            ELSE 0
        END AS Bonus
    FROM users u
    LEFT JOIN invoice i ON u.userID = i.userID
    WHERE u.userID = '$staff_id' AND u.userRole = 'Staff'
    GROUP BY u.userID, u.username, u.userPhone, u.userRole";

$result = $conn->query($join_query);
$data = $result->num_rows > 0 ? $result->fetch_assoc() : null;

// Generate QR data for the logged-in staff member
if ($data) {
    $qr_data = urlencode("Staff ID: {$data['Staff_ID']}, Name: {$data['Username']}, Phone: {$data['Phone']}, Total Sales: RM{$data['Total_Sales']}, Bonus: RM{$data['Bonus']}");
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qr_data}";
}
?>



<body>
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Account Info</h3>
            </div>

            <div class="card">
                <div class="card-body">

                    <?php if ($data): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Field</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Staff ID</th>
                                    <td><?php echo htmlspecialchars($data['Staff_ID']); ?></td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td><?php echo htmlspecialchars($data['Username']); ?></td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><?php echo htmlspecialchars($data['Phone']); ?></td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td><?php echo htmlspecialchars($data['Role']); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Sales </th>
                                    <td>RM <?php echo number_format($data['Total_Sales'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Orders Processed</th>
                                    <td><?php echo $data['Total_Orders']; ?></td>
                                </tr>
                                <tr>
                                    <th>Bonus</th>
                                    <td>RM <?php echo number_format($data['Bonus'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>QR Code</th>
                                    <td><img src="<?php echo $qr_url; ?>" alt="QR Code" /></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">No data found for the logged-in staff member.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>