<?php
include('../db.php');
include('header.php');

if (!isset($_SESSION['userRole'])) {
    echo "<script>alert('User session is not set. Please log in again.');</script>";
    echo "<script>window.location.href = '../index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID']; 
    $file = $_FILES['file'];
    $orderID = isset($_POST['orderID']) ? $_POST['orderID'] : null;
    $targetDir = "uploads/";
    $fileName = basename($file['name']);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        if (!$orderID) {
            $orderQuery = "INSERT INTO `order` (userID, File, OrderDate, Status) VALUES ('$userID', '$fileName', NOW(), 'Pending')";
            if (mysqli_query($conn, $orderQuery)) {
                $orderID = mysqli_insert_id($conn); 
            } else {
                echo "<script>alert('Database error: Unable to add order.');</script>";
                exit;
            }
        }

        foreach ($_POST['lines'] as $line) {
            $packageID = $line['packageID'];
            $quantity = $line['quantity'];

            $orderLineQuery = "INSERT INTO `order_line` (orderID, PackageID, Quantity) VALUES ('$orderID', '$packageID', '$quantity')";
            if (!mysqli_query($conn, $orderLineQuery)) {
                echo "<script>alert('Database error: Unable to add order line.');</script>";
            }
        }
        echo "<script>alert('Order placed successfully!');</script>";
    } else {
        echo "<script>alert('File upload failed.');</script>";
    }
}

$userRole = $_SESSION['userRole'];
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manage Orders</h3>
        </div>
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data" id="orderForm">
                    <input type="hidden" name="orderID" id="orderID" value="">
                    <div id="order-forms">
                        <div class="form-group col-lg-6 order-form">
                            <label for="Package">Choose Package</label>
                            <select class="form-select form-control" name="lines[0][packageID]">
                                <option value="1">Color</option>
                                <option value="2">Black & White</option>
                            </select>
                            <br>
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" name="lines[0][quantity]" placeholder="Quantity" required>
                            <br>
                            <h2 for="formFileLg" class="form-label">Upload your file here</h2>
                            <input class="form-control form-control-lg" type="file" name="file" required>
                            <br>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <br>
                <button class="btn btn-success" id="add-order-line">Add More Line</button>
            </div>
        </div>
    </div>
</div>

<script>
let lineIndex = 1; 

document.getElementById('add-order-line').addEventListener('click', function(e) {
    e.preventDefault();
    const orderForms = document.getElementById('order-forms');
    const newForm = document.createElement('div');
    newForm.className = 'form-group col-lg-6 order-form';
    newForm.innerHTML = `
        <label for="Package">Choose Package</label>
        <select class="form-select form-control" name="lines[${lineIndex}][packageID]">
            <option value="1">Color</option>
            <option value="2">Black & White</option>
        </select>
        <br>
        <label for="quantity">Quantity</label>
        <input type="number" class="form-control" name="lines[${lineIndex}][quantity]" placeholder="Quantity" required>
        <br>
    `;
    orderForms.appendChild(newForm);
    lineIndex++; 
});
</script>

<?php
include('footer.php');
?>
