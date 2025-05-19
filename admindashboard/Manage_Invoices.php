<?php
// Include database connection file
include('../db.php');
include('header.php');
include('sidebar.php');
include('footer.php');

// Handle form submission for updating invoices
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $invoiceID = intval($_POST['invoiceID']); // Ensure $invoiceID is sanitized

    if ($action === 'update') {
        $orderID = intval($_POST['orderID']);
        $invoiceDate = $conn->real_escape_string($_POST['invoiceDate']);
        $amount = floatval($_POST['amount']);
        $status = $conn->real_escape_string($_POST['status']);

        // Only allow updates for non-completed and non-collected invoices
        $query = "SELECT Status FROM invoice WHERE InvoiceID = $invoiceID";
        $result = $conn->query($query);
        $currentStatus = $result->fetch_assoc()['Status'];

        if ($currentStatus === 'Completed' || $currentStatus === 'Collected') {
            echo "<script>alert('Invoices marked as Completed or Collected cannot be edited.');</script>";
        } else {
            $query = "UPDATE invoice SET orderID = $orderID, InvoiceDate = '$invoiceDate', Amount = $amount, Status = '$status' WHERE InvoiceID = $invoiceID";
            if ($conn->query($query)) { //if the database successfully update the invoice it will move to the next
                echo "<script>alert('Invoice updated successfully.');</script>";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }
        }
    } elseif ($action === 'collected') {
        $query = "UPDATE invoice SET Status = 'Collected' WHERE InvoiceID = $invoiceID";
        if ($conn->query($query)) {
            echo "<script>alert('Invoice marked as collected.');</script>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $invoiceID = intval($_GET['delete']);

    $query = "DELETE FROM invoice WHERE InvoiceID = $invoiceID";
    if ($conn->query($query)) {
        echo "<script>alert('Invoice deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch all invoices, excluding "Cancelled" status
$query = "SELECT * FROM invoice WHERE Status != 'Cancelled' ORDER BY InvoiceDate DESC";
$result = $conn->query($query);
?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Invoices</h1>
            </div>

            <div class="card shadow">
                <div class="card-header">
                    <h5>Invoice List</h5>
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
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="<?php echo $row['Status'] === 'Completed' || $row['Status'] === 'Collected' ? 'completed-row' : ''; ?>">
                                    <td><?php echo $row['InvoiceID']; ?></td>
                                    <td><?php echo $row['orderID']; ?></td>
                                    <td><?php echo $row['InvoiceDate']; ?></td>
                                    <td><?php echo $row['Amount']; ?></td>
                                    <td><?php echo $row['Status']; ?></td>
                                    <td>
                                        <?php if (is_Staff()): ?>
                                            <?php if ($row['Status'] === 'Completed' || $row['Status'] === 'Collected'): ?>
                                                <span class="text-muted"><!----> </span>
                                            <?php else: ?>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="invoiceID" value="<?php echo $row['InvoiceID']; ?>">
                                                    <input type="number" name="orderID" value="<?php echo $row['orderID']; ?>" class="form-control d-inline w-15" required>
                                                    <input type="date" name="invoiceDate" value="<?php echo $row['InvoiceDate']; ?>" class="form-control d-inline w-20" required>
                                                    <input type="number" step="0.01" name="amount" value="<?php echo $row['Amount']; ?>" class="form-control d-inline w-20" required>
                                                    <select name="status" class="form-control d-inline w-15" required>
                                                        <option value="Pending" <?php echo $row['Status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Completed" <?php echo $row['Status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-warning">Update</button>
                                                </form>
                                                <a href="?delete=<?php echo $row['InvoiceID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this invoice?');">Delete</a>
                                            <?php endif; ?>
                                            <?php if ($row['Status'] === 'Completed'): ?>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="action" value="collected">
                                                    <input type="hidden" name="invoiceID" value="<?php echo $row['InvoiceID']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success">Change to order Collected</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>


</body>

</html>