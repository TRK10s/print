<?php
ob_start(); // Start output buffering
include('../db.php');
include('header.php');
include('sidebar.php');

// Fetch available branches
$branchQuery = "SELECT BranchID, BranchName FROM koperasbranch";
$branchResult = mysqli_query($conn, $branchQuery);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['packageName']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $branchID = mysqli_real_escape_string($conn, $_POST['branchID']);

    $sql = "INSERT INTO packages (packageName, PriceFloat, Status, BranchID) VALUES ('$name', '$price', '$status', '$branchID')";
    if (mysqli_query($conn, $sql)) {
        header('Location: managePackages.php');
        exit;
    } else {
        $error = "Error adding package.";
    }
}
?>
<div class="container">
    <div class="page-inner">
        <h3 class="fw-bold mb-3">Add New Package</h3>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="form-group mb-3">
                <label for="packageName">Package Name</label>
                <input type="text" id="packageName" name="packageName" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="branchID">Select Branch</label>
                <select id="branchID" name="branchID" class="form-control" required>
                    <option value="">Select a branch</option>
                    <?php
                    if (mysqli_num_rows($branchResult) > 0) {
                        while ($branch = mysqli_fetch_assoc($branchResult)) {
                            echo "<option value='{$branch['BranchID']}'>{$branch['BranchName']}</option>";
                        }
                    } else {
                        echo "<option value=''>No branches available</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Package</button>
            <a href="managePackages.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?php include('footer.php');
ob_end_flush(); // Flush output buffer
?>
