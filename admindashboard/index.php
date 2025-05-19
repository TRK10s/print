<?php
include('../db.php');
include('header.php');
include('sidebar.php');

?>
<!-- End Sidebar -->

<?php

// Fetch sales data grouped by branch and order by highest total orders
$salesByBranchQuery = "
    SELECT b.BranchName, COUNT(o.orderID) AS Totalorders
    FROM koperasbranch b
    JOIN packages p ON b.BranchID = p.BranchID
    JOIN order_line ol ON p.PackageID = ol.PackageID
    JOIN `order` o ON ol.orderID = o.orderID
    GROUP BY b.BranchName
    ORDER BY Totalorders DESC
";
$salesByBranchResult = mysqli_query($conn, $salesByBranchQuery);

$salesData = [];
$salesLabels = [];
if (mysqli_num_rows($salesByBranchResult) > 0) {
    while ($row = mysqli_fetch_assoc($salesByBranchResult)) {
        $salesLabels[] = $row['BranchName'];
        $salesData[] = $row['Totalorders'];
    }
}

// Fetch number of packages grouped by branch and order by the highest count
$packagesByBranchQuery = "
    SELECT 
        b.BranchName, 
        COUNT(p.PackageID) AS TotalPackages 
    FROM 
        koperasbranch b
     JOIN 
        packages p ON b.BranchID = p.BranchID
    GROUP BY 
        b.BranchName
    ORDER BY 
        TotalPackages DESC

        LIMIT 6
";

// Query to fetch total revenue for active packages only
$totalRevenueQuery = "
    SELECT 
        b.BranchName, 
        SUM(i.Amount) AS TotalRevenue
    FROM 
        koperasbranch b
    JOIN 
        packages p ON b.BranchID = p.BranchID
    JOIN 
        order_line ol ON p.PackageID = ol.PackageID
    JOIN 
        `order` o ON ol.orderID = o.orderID
    JOIN 
        invoice i ON o.orderID = i.orderID
    WHERE 
        p.Status = 'Active'
    GROUP BY 
        b.BranchName
    ORDER BY 
        TotalRevenue DESC
";

$totalRevenueResult = mysqli_query($conn, $totalRevenueQuery);

$revenueLabels = [];
$revenueData = [];

if (mysqli_num_rows($totalRevenueResult) > 0) {
    while ($row = mysqli_fetch_assoc($totalRevenueResult)) {
        $revenueLabels[] = $row['BranchName'];
        $revenueData[] = $row['TotalRevenue'];
    }
}


$packagesByBranchResult = mysqli_query($conn, $packagesByBranchQuery);

$packagesLabels = [];
$packagesData = [];
if (mysqli_num_rows($packagesByBranchResult) > 0) {
    while ($row = mysqli_fetch_assoc($packagesByBranchResult)) {
        $packagesLabels[] = $row['BranchName'];
        $packagesData[] = $row['TotalPackages'];
    }
}




$pendingQuery = "SELECT COUNT(orderID) as totalPending FROM `order` WHERE Status = 'Pending'";
$pendingResult = mysqli_query($conn, $pendingQuery);
$pendingRow = mysqli_fetch_assoc($pendingResult);
$totalPending = $pendingRow['totalPending'] ?? 0;

$orderedQuery = "SELECT COUNT(orderID) as totalOrdered FROM `order` WHERE Status = 'Ordered'";
$orderedResult = mysqli_query($conn, $orderedQuery);
$orderedRow = mysqli_fetch_assoc($orderedResult);
$totalOrdered = $orderedRow['totalOrdered'] ?? 0;
$data = [
    'pending' => $totalPending,
    'ordered' => $totalOrdered
];
?>


<div class="container">

    <div class="page-inner">
        <?php
        if (is_Admin()) {
        ?>
            <div
                class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">

                <div>
                    <h3 class="fw-bold mb-3">Dashboard</h3>
                    <h6 class="op-7 mb-2">RapidPrint</h6>
                </div>
                <div class="ms-md-auto py-2 py-md-0">
                    <a href="register.php" class="btn btn-primary btn-round">Add User</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div
                                        class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Visitors</p>
                                        <h4 class="card-title"><?php
                                                                $queryVisitors = "SELECT COUNT(*) AS totalVisitors FROM users WHERE userRole = 'Student'";
                                                                $resultVisitors = mysqli_query($conn, $queryVisitors);
                                                                $rowVisitors = mysqli_fetch_assoc($resultVisitors);
                                                                echo '<h4 class="card-title">' . $rowVisitors['totalVisitors'] . '</h4>';
                                                                ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $salesQuery = "SELECT SUM(Amount) as totalSales FROM invoice";
                $salesResult = mysqli_query($conn, $salesQuery);
                $salesRow = mysqli_fetch_assoc($salesResult);
                $totalSales = $salesRow['totalSales'] ?? 0;
                ?>


                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div
                                        class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-luggage-cart"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Sales</p>
                                        <h4 class="card-title">RM <?php echo number_format($totalSales, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $orderQuery = "SELECT COUNT(orderID) as totalOrders FROM `order`";
                $orderResult = mysqli_query($conn, $orderQuery);
                $orderRow = mysqli_fetch_assoc($orderResult);
                $totalOrders = $orderRow['totalOrders'];
                ?>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div
                                        class="icon-big text-center icon-secondary bubble-shadow-small">
                                        <i class="far fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Orders</p>
                                        <h4 class="card-title"><?php echo $totalOrders; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                // Fetch the total number of branches from the koperasbranch table
                $branchQuery = "SELECT COUNT(BranchID) as totalBranches FROM koperasbranch";
                $branchResult = mysqli_query($conn, $branchQuery);
                $branchRow = mysqli_fetch_assoc($branchResult);
                $totalBranches = $branchRow['totalBranches'] ?? 0; // Default to 0 if no branches

                ?>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div
                                        class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Branches</p>
                                        <h4 class="card-title"><?php echo $totalBranches; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <?php
                // Fetch filter values from the GET request
                $searchBranch = isset($_GET['searchBranch']) ? mysqli_real_escape_string($conn, $_GET['searchBranch']) : '';
                $searchPackage = isset($_GET['searchPackage']) ? mysqli_real_escape_string($conn, $_GET['searchPackage']) : '';
                $searchStatus = isset($_GET['searchStatus']) ? mysqli_real_escape_string($conn, $_GET['searchStatus']) : '';

                // Query to fetch packages and branches with filters
                $packagesBranchesQuery = "
    SELECT 
        p.PackageID, 
        p.packageName, 
        b.BranchName, 
        p.Status 
    FROM 
        packages p
    JOIN 
        koperasbranch b ON p.BranchID = b.BranchID
    WHERE 
        b.BranchName LIKE '%$searchBranch%' AND 
        p.packageName LIKE '%$searchPackage%' AND
        p.Status LIKE '%$searchStatus%'
    ORDER BY 
        p.PackageID ASC 
    LIMIT 5
";

                $packagesBranchesResult = mysqli_query($conn, $packagesBranchesQuery);
                ?>

                <div class="container mt-5">
                    <h4 class="fw-bold mb-4">Packages and Branches</h4>

                    <!-- Filters -->
                    <form method="GET" action="" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="searchPackage" class="form-label">Search by Package Name:</label>
                                <input
                                    type="text"
                                    name="searchPackage"
                                    id="searchPackage"
                                    class="form-control"
                                    placeholder="Enter package name"
                                    value="<?php echo $searchPackage; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="searchBranch" class="form-label">Search by Branch Name:</label>
                                <input
                                    type="text"
                                    name="searchBranch"
                                    id="searchBranch"
                                    class="form-control"
                                    placeholder="Enter branch name"
                                    value="<?php echo $searchBranch; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="searchStatus" class="form-label">Filter by Status:</label>
                                <select name="searchStatus" id="searchStatus" class="form-control">
                                    <option value="">All</option>
                                    <option value="Active" <?php if ($searchStatus === 'Active') echo 'selected'; ?>>Active</option>
                                    <option value="Inactive" <?php if ($searchStatus === 'Inactive') echo 'selected'; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Package ID</th>
                                    <th scope="col">Package Name</th>
                                    <th scope="col">Branch Name</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($packagesBranchesResult && mysqli_num_rows($packagesBranchesResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($packagesBranchesResult)) {
                                        echo "<tr>";
                                        echo "<td>{$row['PackageID']}</td>";
                                        echo "<td>{$row['packageName']}</td>";
                                        echo "<td>{$row['BranchName']}</td>";
                                        echo "<td>{$row['Status']}</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No results found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>








                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-round">
                            <div class="card-header">
                                <h4 class="card-title">Orders by Branch</h4>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="min-height: 375px;">
                                    <canvas id="salesByBranchChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-round">
                            <div class="card-header">
                                <h4 class="card-title">Packages by Branch</h4>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="min-height: 375px;">
                                    <canvas id="packagesByBranchChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>







                <div class="row">
                    <div class="card card-round">
                        <div class="card-header">
                            <h4 class="card-title">Order Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="min-height: 375px">
                                <canvas id="orderChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <?php
                    $studentsQuery = "SELECT userid, username FROM users WHERE userRole = 'Student' ORDER BY userID DESC LIMIT 3";
                    $studentsResult = mysqli_query($conn, $studentsQuery);
                    ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-head-row card-tools-still-right">
                                        <div class="card-title">Recent Students</div>
                                    </div>
                                    <div class="card-list py-4">
                                        <?php
                                        if (mysqli_num_rows($studentsResult) > 0) {
                                            while ($student = mysqli_fetch_assoc($studentsResult)) {
                                                echo '<div class="item-list">';
                                                echo '<div class="info-user ms-3">';
                                                echo '<div class="username">' . $student['username'] . '</div>';
                                                echo '</div>';
                                                echo '<a href="edit_user.php?userID=' . $student['userid'] . '" class="btn btn-icon btn-link op-8 me-1">';
                                                echo '<i class="far fa-edit"></i>';
                                                echo '</a>';
                                                echo '<a href="delete_user.php?userID=' . $student['userid'] . '" class="btn btn-icon btn-link btn-danger op-8" onclick="return confirm(\'Are you sure you want to delete this user?\')">';
                                                echo '<i class="fas fa-trash"></i>';
                                                echo '</a>';
                                                echo '</div>';
                                            }
                                        } else {
                                            echo '<p class="text-muted">No recent students found.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $invoiceQuery = "SELECT InvoiceID, Amount, InvoiceDate FROM invoice ORDER BY InvoiceDate DESC LIMIT 10";
                        $invoiceResult = mysqli_query($conn, $invoiceQuery);
                        ?>

                        <div class="col-md-8">
                            <div class="card card-round">

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-items-center mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th scope="col">Invoice Number</th>
                                                    <th scope="col" class="text-end">Date & Time</th>
                                                    <th scope="col" class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (mysqli_num_rows($invoiceResult) > 0) {
                                                    while ($invoice = mysqli_fetch_assoc($invoiceResult)) {
                                                        echo '<tr>';
                                                        echo '<th scope="row">';
                                                        echo '<button class="btn btn-icon btn-round btn-success btn-sm me-2">';
                                                        echo '<i class="fa fa-check"></i>';
                                                        echo '</button>';
                                                        echo 'Invoice #' . $invoice['InvoiceID'];
                                                        echo '</th>';
                                                        echo '<td class="text-end">' . date("M d, Y, h:i A", strtotime($invoice['InvoiceDate'])) . '</td>';
                                                        echo '<td class="text-end">RM ' . number_format($invoice['Amount'], 2) . '</td>';
                                                        echo '<td class="text-end">';

                                                        echo '</td>';
                                                        echo '</tr>';
                                                    }
                                                } else {
                                                    echo '<tr>';
                                                    echo '<td colspan="4" class="text-center text-muted">No transaction history found.</td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $branchFilter = isset($_GET['branch']) && $_GET['branch'] != '' ? "b.BranchName = '" . mysqli_real_escape_string($conn, $_GET['branch']) . "'" : "1";
                        $statusFilter = isset($_GET['status']) && $_GET['status'] != '' ? "o.Status = '" . mysqli_real_escape_string($conn, $_GET['status']) . "'" : "1";
                        $packageNameFilter = isset($_GET['packageName']) && $_GET['packageName'] != '' ? "p.packageName LIKE '%" . mysqli_real_escape_string($conn, $_GET['packageName']) . "%'" : "1";

                        $query = "
SELECT 
    o.orderID,
    o.Status,
    o.OrderDate,
    ol.Quantity,
    p.packageName,
    p.PriceFloat AS PackagePrice,
    b.BranchName
FROM 
    `order` o
JOIN 
    order_line ol ON o.orderID = ol.orderID
JOIN 
    packages p ON ol.PackageID = p.PackageID
JOIN 
    koperasbranch b ON p.BranchID = b.BranchID
WHERE 
    $branchFilter AND $statusFilter AND $packageNameFilter
";

                        $result = mysqli_query($conn, $query);
                        ?>

                        <form method="GET" action="">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="branchFilter" class="form-label">Filter by Branch:</label>
                                    <select name="branch" id="branchFilter" class="form-select">
                                        <option value="">All Branches</option>
                                        <?php
                                        $branchQuery = "SELECT DISTINCT BranchName FROM koperasbranch";
                                        $branchResult = mysqli_query($conn, $branchQuery);
                                        while ($branchRow = mysqli_fetch_assoc($branchResult)) {
                                            $selected = (isset($_GET['branch']) && $_GET['branch'] == $branchRow['BranchName']) ? 'selected' : '';
                                            echo "<option value='{$branchRow['BranchName']}' $selected>{$branchRow['BranchName']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="statusFilter" class="form-label">Filter by Order Status:</label>
                                    <select name="status" id="statusFilter" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="Pending" <?php if (isset($_GET['status']) && $_GET['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Ordered" <?php if (isset($_GET['status']) && $_GET['status'] == 'Ordered') echo 'selected'; ?>>Ordered</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="packageNameFilter" class="form-label">Search by Package Name:</label>
                                    <input type="text" name="packageName" id="packageNameFilter" class="form-control" placeholder="Enter package name" value="<?php echo isset($_GET['packageName']) ? htmlspecialchars($_GET['packageName']) : ''; ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Order Date</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Package Name</th>
                                    <th scope="col">Package Price (RM)</th>
                                    <th scope="col">Branch Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $counter = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $counter . "</td>";
                                        echo "<td>" . $row['orderID'] . "</td>";
                                        echo "<td>" . $row['Status'] . "</td>";
                                        echo "<td>" . $row['OrderDate'] . "</td>";
                                        echo "<td>" . $row['Quantity'] . "</td>";
                                        echo "<td>" . $row['packageName'] . "</td>";
                                        echo "<td>RM " . number_format($row['PackagePrice'], 2) . "</td>";
                                        echo "<td>" . $row['BranchName'] . "</td>";
                                        echo "</tr>";
                                        $counter++;
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No orders found based on the selected filters</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                <?php
            } else if (is_Student()) {
                $userId = $_SESSION['userID'];

                // Check if the user has a membership card
                $cardQuery = "SELECT * FROM membershipcard WHERE userID = '$userId' LIMIT 1";
                $cardResult = $conn->query($cardQuery);
                $card = $cardResult->fetch_assoc();

                // Fetch the last 5 orders with additional details from the invoice table
                $lastOrdersQuery = "
                  SELECT 
                      o.orderID, 
                      o.OrderDate, 
                      o.File, 
                      i.Amount, 
                      i.InvoiceDate 
                  FROM `order` o
                  JOIN `invoice` i ON o.orderID = i.orderID
                  WHERE o.userID = '$userId'
                  ORDER BY o.OrderDate DESC 
                  LIMIT 5";
                $lastOrdersResult = $conn->query($lastOrdersQuery);
                $lastOrders = $lastOrdersResult ? $lastOrdersResult->fetch_all(MYSQLI_ASSOC) : [];

                // Fetch data for the Daily Orders graph
                $dailyOrdersQuery = "
                  SELECT DATE(OrderDate) AS OrderDate, COUNT(*) AS OrderCount
                  FROM `order`
                  WHERE userID = '$userId'
                  GROUP BY DATE(OrderDate)";
                $dailyOrdersResult = $conn->query($dailyOrdersQuery);

                $dailyOrdersData = [];
                while ($row = $dailyOrdersResult->fetch_assoc()) {
                    $dailyOrdersData[] = $row;
                }

                // Handle date filter for Average Spending
                $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
                $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

                // Fetch data for the Average Spending per Day graph with start and end date filter
                $averageSpendingQuery = "
                  SELECT DATE(o.OrderDate) AS OrderDate, AVG(i.Amount) AS AvgSpending
                  FROM `order` o
                  JOIN `invoice` i ON o.orderID = i.orderID
                  WHERE o.userID = '$userId' 
                    AND DATE(o.OrderDate) BETWEEN '$startDate' AND '$endDate'
                  GROUP BY DATE(o.OrderDate)";
                $averageSpendingResult = $conn->query($averageSpendingQuery);

                $averageSpendingData = [];
                while ($row = $averageSpendingResult->fetch_assoc()) {
                    $averageSpendingData[] = $row;
                }

                // Fetch the current balance from the membership card
                $balanceQuery = "SELECT Balance FROM membershipcard WHERE userID = '$userId'";
                $balanceResult = $conn->query($balanceQuery);
                $currentBalance = $balanceResult->fetch_assoc()['Balance'] ?? 0;
                ?>
                    <!DOCTYPE html>
                    <html lang="en">

                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Student Dashboard</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    </head>

                    <body>
                        <div class="container mt-5">
                            <div class="page-inner">
                                <div class="dashboard-heading text-center">
                                    <h3 class="fw-bold">Student Dashboard</h3>
                                    <h6 class="text-muted">RapidPrint</h6>
                                </div>

                                <!-- Membership Info -->
                                <div class="row justify-content-center mt-4">
                                    <div class="col-md-5">
                                        <div class="card text-center">
                                            <h4 class="fw-bold">Membership Card</h4>
                                            <?php if (!empty($card)) { ?>
                                                <p><strong>Card ID:</strong> <?= htmlspecialchars($card['CardID']); ?></p>
                                                <p><strong>Expiry Date:</strong> <?= htmlspecialchars($card['ExpiryDate']); ?></p>
                                                <p><strong>Balance:</strong> RM <?= htmlspecialchars($card['Balance']); ?></p>
                                                <div class="qr-code">
                                                    <p><strong>QR Code</strong></p>
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode('Card ID: ' . $card['CardID'] . ', Balance: ' . $card['Balance']); ?>" alt="QR Code">
                                                </div>
                                            <?php } else { ?>
                                                <p>No membership card found.</p>
                                                <a href="apply_membership.php" class="btn btn-primary">Apply For A Card</a>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Last 5 Orders -->
                                    <div class="col-md-6">
                                        <div class="card">
                                            <h4 class="fw-bold text-center">Last 5 Orders</h4>
                                            <?php if (!empty($lastOrders)) { ?>
                                                <div class="list-group mt-3">
                                                    <?php foreach ($lastOrders as $order) { ?>
                                                        <div class="list-group-item text-center">
                                                            <h5 class="mb-3"><strong>Order ID:</strong> <?= htmlspecialchars($order['orderID']); ?></h5>
                                                            <p class="text-start">
                                                                <strong><br>Date:</strong> <?= htmlspecialchars($order['OrderDate']); ?><br>
                                                                <strong>File:</strong> <?= htmlspecialchars($order['File']); ?><br>
                                                                <strong>Invoice Date:</strong> <?= htmlspecialchars($order['InvoiceDate'] ?? 'N/A'); ?><br>
                                                                <strong>Amount:</strong> RM <?= htmlspecialchars($order['Amount'] ?? 'N/A'); ?>
                                                            </p>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <p class="text-center mt-3">No recent orders found.</p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Graphs -->
                                <div class="row mt-4">
                                    <!-- Average Spending Chart Section -->
                                    <div class="col-md-12">
                                        <!-- Filter Form for Average Spending -->
                                        <div class="mb-4">
                                            <form method="GET" class="d-flex justify-content-center align-items-center">
                                                <label for="start_date" class="form-label me-3">Start Date:</label>
                                                <input type="date" class="form-control me-2" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate); ?>">
                                                <label for="end_date" class="form-label me-3">End Date:</label>
                                                <input type="date" class="form-control me-2" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate); ?>">
                                                <button type="submit" class="btn btn-primary">Apply</button>
                                            </form>
                                        </div>
                                        <!-- Average Spending Graph -->
                                        <div class="card mb-4">
                                            <h4 class="fw-bold text-center">Average Spending Between Selected Dates</h4>
                                            <canvas id="averageSpendingChart"></canvas>
                                        </div>
                                    </div>

                                    <!-- Daily Orders Graph -->
                                    <div class="col-md-12">
                                        <div class="card">
                                            <h4 class="fw-bold text-center">Daily Orders</h4>
                                            <canvas id="ordersChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            // Daily Orders Chart
                            const ordersCtx = document.getElementById('ordersChart').getContext('2d');
                            const ordersData = {
                                labels: <?= json_encode(array_column($dailyOrdersData, 'OrderDate')); ?>,
                                datasets: [{
                                    label: 'Orders',
                                    data: <?= json_encode(array_column($dailyOrdersData, 'OrderCount')); ?>,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            };

                            new Chart(ordersCtx, {
                                type: 'bar',
                                data: ordersData,
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });

                            // Average Spending per Day Chart
                            const avgSpendingCtx = document.getElementById('averageSpendingChart').getContext('2d');
                            const avgSpendingData = {
                                labels: <?= json_encode(array_column($averageSpendingData, 'OrderDate')); ?>,
                                datasets: [{
                                    label: 'Average Spending (RM)',
                                    data: <?= json_encode(array_column($averageSpendingData, 'AvgSpending')); ?>,
                                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    borderWidth: 1
                                }]
                            };

                            new Chart(avgSpendingCtx, {
                                type: 'line',
                                data: avgSpendingData,
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
                    </body>

                    </html>



                <?php
            } elseif (is_Staff()) {
                ?>
                    <div
                        class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">

                        <div>
                            <h3 class="fw-bold mb-3">Dashboard</h3>
                            <h6 class="op-7 mb-2">RapidPrint</h6>
                        </div>

                    </div>
                    <?php

                    // Query for Staff Count
                    $staffQuery = "SELECT COUNT(*) AS totalStaff FROM users WHERE userRole = 'Staff'";
                    $staffResult = mysqli_query($conn, $staffQuery);
                    $staffCount = mysqli_fetch_assoc($staffResult)['totalStaff'];

                    // Query for Invoice Count
                    $invoiceQuery = "SELECT COUNT(*) AS totalInvoices FROM invoice";
                    $invoiceResult = mysqli_query($conn, $invoiceQuery);
                    $invoiceCount = mysqli_fetch_assoc($invoiceResult)['totalInvoices'];

                    // Query for Total Sales
                    $salesQuery = "SELECT SUM(Amount) AS totalSales FROM invoice";
                    $salesResult = mysqli_query($conn, $salesQuery);
                    $totalSales = mysqli_fetch_assoc($salesResult)['totalSales'];

                    // Query for Completed Orders
                    $completedOrdersQuery = "SELECT COUNT(*) AS completedOrders FROM invoice WHERE Status = 'Completed'";
                    $completedOrdersResult = mysqli_query($conn, $completedOrdersQuery);
                    $completedOrdersCount = mysqli_fetch_assoc($completedOrdersResult)['completedOrders'];
                    ?>

                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Staff</p>
                                                <h4 class="card-title"><?php echo $staffCount; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-user-check"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Invoices</p>
                                                <h4 class="card-title"><?php echo $invoiceCount; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-luggage-cart"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Sales</p>
                                                <h4 class="card-title">$<?php echo number_format($totalSales, 2); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                <i class="far fa-check-circle"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Order Completed</p>
                                                <h4 class="card-title"><?php echo $completedOrdersCount; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--statistics-->
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="startDate">Start Date</label>
                                <input type="date" name="startDate" id="startDate" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="endDate">End Date</label>
                                <input type="date" name="endDate" id="endDate" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Collected">Collected</option>

                                </select>
                            </div>
                            <div class="col-md-12 mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>



                    <?php
                    // Query to fetch sales data grouped by date
                    // Handle search filter
                    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
                    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;
                    $status = isset($_POST['status']) ? $_POST['status'] : null;

                    // Base query to fetch sales data
                    $salesQuery = "SELECT DATE(InvoiceDate) AS SaleDate, SUM(Amount) AS TotalSales FROM invoice";

                    // Add WHERE clauses based on filters
                    $conditions = [];
                    if ($startDate && $endDate) {
                        $conditions[] = "DATE(InvoiceDate) BETWEEN '$startDate' AND '$endDate'";
                    }
                    if ($status) {
                        $conditions[] = "Status = '$status'";
                    }

                    // Combine conditions into the query
                    if (count($conditions) > 0) {
                        $salesQuery .= " WHERE " . implode(" AND ", $conditions);
                    }

                    $salesQuery .= " GROUP BY DATE(InvoiceDate)";

                    // Execute the query
                    $salesResult = mysqli_query($conn, $salesQuery);

                    // Prepare sales data for the chart
                    $salesData = [];
                    while ($row = mysqli_fetch_assoc($salesResult)) {
                        $salesData[] = [
                            'date' => $row['SaleDate'],
                            'total' => (float)$row['TotalSales']
                        ];
                    }

                    // Encode sales data as JSON for JavaScript
                    $salesDataJson = json_encode($salesData);


                    $salesResult = mysqli_query($conn, $salesQuery);

                    // Prepare sales data for the chart
                    $salesData = [];
                    while ($row = mysqli_fetch_assoc($salesResult)) {
                        $salesData[] = [
                            'date' => $row['SaleDate'],
                            'total' => (float)$row['TotalSales']
                        ];
                    }

                    // Encode sales data as JSON for JavaScript
                    $salesDataJson = json_encode($salesData);
                    ?>

                    <!-- Sales Chart -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-round">
                                <div class="card-header">
                                    <h4 class="card-title">Sales Chart</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const ctx = document.getElementById('salesChart').getContext('2d');
                                const salesData = <?php echo $salesDataJson; ?>;

                                // Extract labels (dates) and data (totals) for the chart
                                const labels = salesData.map(item => item.date);
                                const data = salesData.map(item => item.total);

                                // Render the chart
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Total Sales (USD)',
                                            data: data,
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: true,
                                                position: 'top'
                                            }
                                        },
                                        scales: {
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Date'
                                                }
                                            },
                                            y: {
                                                title: {
                                                    display: true,
                                                    text: 'Total Sales (USD)'
                                                }
                                            }
                                        }
                                    }
                                });
                            });
                        </script>

                        <?php
                        // Get the logged-in user's username from the session
                        $loggedInUser = $_SESSION['username'];

                        // Query to fetch staff members excluding the logged-in user
                        $staffQuery = "SELECT username, userRole FROM users WHERE userRole = 'Staff' AND username != '$loggedInUser'";
                        $staffResult = mysqli_query($conn, $staffQuery);
                        ?>

                        <div class="col-md-4">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-head-row card-tools-still-right">
                                        <div class="card-title">Staff</div>
                                        <div class="card-tools">
                                            <div class="dropdown">

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#">Action</a>
                                                    <a class="dropdown-item" href="#">Another action</a>
                                                    <a class="dropdown-item" href="#">Something else here</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-list py-4">
                                        <?php
                                        if (mysqli_num_rows($staffResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($staffResult)) {
                                                // Display each staff member
                                        ?>
                                                <div class="item-list">
                                                    <div class="avatar">
                                                        <span class="avatar-title rounded-circle border border-white bg-primary">
                                                            <?php echo strtoupper(substr($row['username'], 0, 2)); // Initials 
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="info-user ms-3">
                                                        <div class="username"><?php echo $row['username']; ?></div>
                                                        <div class="status"><?php echo $row['userRole']; ?></div>
                                                    </div>

                                                </div>
                                        <?php
                                            }
                                        } else {
                                            echo "<div class='text-center'>No staff members found</div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Transactions Pie Chart-->
                        <div class="row">
                            <?php
                            // Query to fetch transaction data grouped by status
                            $transactionsQuery = "
    SELECT Status, SUM(Amount) AS TotalAmount 
    FROM invoice 
    GROUP BY Status";
                            $transactionsResult = mysqli_query($conn, $transactionsQuery);

                            // Prepare transaction data for the chart
                            $transactionData = [];
                            while ($row = mysqli_fetch_assoc($transactionsResult)) {
                                $transactionData[] = [
                                    'status' => $row['Status'],
                                    'total' => (float)$row['TotalAmount']
                                ];
                            }

                            // Encode transaction data as JSON for JavaScript
                            $transactionDataJson = json_encode($transactionData);
                            ?>

                            <!-- Transactions Pie Chart -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card card-round">
                                        <div class="card-header">
                                            <h4 class="card-title">Transactions Pie Chart</h4>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="transactionsPieChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const ctx = document.getElementById('transactionsPieChart').getContext('2d');
                                    const transactionData = <?php echo $transactionDataJson; ?>;

                                    // Extract labels (statuses) and data (totals) for the chart
                                    const labels = transactionData.map(item => item.status);
                                    const data = transactionData.map(item => item.total);

                                    // Render the pie chart
                                    new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: labels, // Transaction statuses
                                            datasets: [{
                                                label: 'Transaction Amounts (USD)',
                                                data: data, // Total amounts for each status
                                                backgroundColor: [
                                                    'rgba(75, 192, 192, 0.2)',
                                                    'rgba(153, 102, 255, 0.2)',
                                                    'rgba(255, 159, 64, 0.2)',
                                                    'rgba(255, 99, 132, 0.2)',
                                                    'rgba(54, 162, 235, 0.2)'
                                                ],
                                                borderColor: [
                                                    'rgba(75, 192, 192, 1)',
                                                    'rgba(153, 102, 255, 1)',
                                                    'rgba(255, 159, 64, 1)',
                                                    'rgba(255, 99, 132, 1)',
                                                    'rgba(54, 162, 235, 1)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            const label = tooltipItem.label || '';
                                                            const value = tooltipItem.raw || 0;
                                                            return `${label}: $${value.toFixed(2)}`;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        <?php
                    }
                        ?>
                        </div>
                    </div>




                </div>

                <?php
                include('footer.php');
                ?>
                <script>
                    const orderData = <?php echo json_encode($data); ?>;


                    var ctx = document.getElementById('orderChart').getContext('2d');

                    var orderChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ["Pending Orders", "Ordered"],
                            datasets: [{
                                label: "Orders",
                                backgroundColor: ['#8D0B41', '#213555'],
                                data: [orderData.pending, orderData.ordered]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            },
                            tooltips: {
                                enabled: true
                            }
                        }
                    });
                </script>

                <script>
                    // Pass PHP data to JavaScript
                    const branchLabels = <?php echo json_encode($salesLabels); ?>;
                    const branchSales = <?php echo json_encode($salesData); ?>;

                    // Create the sales by branch chart
                    var ctxBranch = document.getElementById('salesByBranchChart').getContext('2d');
                    new Chart(ctxBranch, {
                        type: 'bar',
                        data: {
                            labels: branchLabels, // Branch names as labels
                            datasets: [{
                                label: 'Total Orders',
                                data: branchSales, // Total sales for each branch
                                backgroundColor: 'rgba(54, 162, 235, 0.6)', // Light blue bars
                                borderColor: 'rgba(54, 162, 235, 1)', // Blue borders
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true // Start Y-axis at 0
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    enabled: true
                                }
                            }
                        }
                    });
                </script>




                <script>
                    // Pass PHP data for packages by branch to JavaScript
                    const packagesLabels = <?php echo json_encode($packagesLabels); ?>;
                    const packagesData = <?php echo json_encode($packagesData); ?>;

                    // Create a doughnut chart for packages by branch
                    const ctxPackages = document.getElementById('packagesByBranchChart').getContext('2d');
                    new Chart(ctxPackages, {
                        type: 'doughnut',
                        data: {
                            labels: packagesLabels, // Branch names as labels
                            datasets: [{
                                label: 'Total Packages',
                                data: packagesData, // Total packages for each branch
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.6)',
                                    'rgba(153, 102, 255, 0.6)',
                                    'rgba(255, 159, 64, 0.6)',
                                    'rgba(255, 99, 132, 0.6)',
                                    'rgba(54, 162, 235, 0.6)',
                                    'rgba(136, 4, 48, 0.6)',
                                ],
                                borderColor: [
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(136, 4, 48, 0.6)',
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    enabled: true,
                                },
                            },
                        },
                    });
                </script>