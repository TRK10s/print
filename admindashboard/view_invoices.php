<?php
// Include necessary files
include('../db.php');
include('header.php');
include('sidebar.php');
include('footer.php');


// Fetch all invoices 
$query = "SELECT * FROM invoice ORDER BY InvoiceDate DESC";
$result = $conn->query($query);

?>

<body>
    <div class="container-fluid">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <br><br><br>
            <div class="card shadow">
                <div class="card-header">
                    <h4>Invoice List</h4>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Invoice ID</th>
                                <th scope="col">Order ID</th>
                                <th scope="col">Invoice Date</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="<?php echo ($row['Status'] === 'Completed' || $row['Status'] === 'Collected') ? 'completed-row' : ''; ?>">
                                    <td><?php echo $row['InvoiceID']; ?></td>
                                    <td><?php echo $row['orderID']; ?></td>
                                    <td><?php echo $row['InvoiceDate']; ?></td>
                                    <td><?php echo $row['Amount']; ?></td>
                                    <td><?php echo $row['Status']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>


</body>

</html>