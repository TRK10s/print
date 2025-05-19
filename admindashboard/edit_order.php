<?php
include('../db.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderID = intval($_POST['orderID']);
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($file['name']);
        $targetFilePath = $targetDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            $updateFileQuery = "UPDATE `order` SET File = '$fileName' WHERE orderID = $orderID";
            if (mysqli_query($conn, $updateFileQuery)) {
                echo "<script>alert('Order updated successfully!'); window.location.href='vieworders.php';</script>";
            } else {
                die('Error updating file: ' . mysqli_error($conn));
            }
        } else {
            echo "<script>alert('Failed to upload the new file.');</script>";
        }
    } else {
        echo "<script>alert('No file uploaded. Please try again.');</script>";
    }
} else {
    $orderID = intval($_GET['orderID']);
    $orderQuery = "SELECT * FROM `order` WHERE orderID = $orderID";
    $orderResult = mysqli_query($conn, $orderQuery);
    $order = mysqli_fetch_assoc($orderResult);
}
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Order</h3>
        </div>
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="orderID" value="<?php echo $orderID; ?>">

                    <div class="form-group">
                        <label for="file">Upload a new file (optional)</label>
                        <input type="file" class="form-control" name="file">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>
