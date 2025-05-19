<?php
ob_start(); // Start output buffering
include('../db.php');
include('header.php');
include('sidebar.php');

// Get the package details
$id = $_GET['id'];
$sql = "SELECT * FROM packages WHERE PackageID = $id";
$result = mysqli_query($conn, $sql);
$package = mysqli_fetch_assoc($result);

// Fetch branches for the dropdown
$branchQuery = "SELECT BranchID, BranchName FROM koperasbranch";
$branchResult = mysqli_query($conn, $branchQuery);

// Handle package update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['packageName']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $branchID = mysqli_real_escape_string($conn, $_POST['branch']);

    $update = "UPDATE packages SET packageName='$name', PriceFloat='$price', Status='$status', BranchID='$branchID' WHERE PackageID=$id";
    if (mysqli_query($conn, $update)) {
        header('Location: managePackages.php');
        exit;
    } else {
        $error = "Error updating package.";
    }
}

// Handle package deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete = "DELETE FROM packages WHERE PackageID=$id";
    if (mysqli_query($conn, $delete)) {
        header('Location: managePackages.php'); // Redirect to Manage Packages after deletion
        exit;
    } else {
        $error = "Error deleting package.";
    }
}
?>

<div class="container">
    <div class="page-inner">
        <h3 class="fw-bold mb-3">Edit Package</h3>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST" id="editPackageForm">
            <div class="form-group mb-3">
                <label for="packageName">Package Name</label>
                <input type="text" id="packageName" name="packageName" class="form-control" value="<?php echo $package['packageName']; ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" class="form-control" value="<?php echo $package['PriceFloat']; ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="Active" <?php if ($package['Status'] === 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if ($package['Status'] === 'Inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="branch">Assign Branch</label>
                <select id="branch" name="branch" class="form-control" required>
                    <option value="">Select a branch</option>
                    <?php
                    while ($branch = mysqli_fetch_assoc($branchResult)) {
                        $selected = ($branch['BranchID'] == $package['BranchID']) ? 'selected' : '';
                        echo "<option value='{$branch['BranchID']}' $selected>{$branch['BranchName']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-danger" id="deleteButton">Delete Package</button>
                <a href="managePackages.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Confirmation dialog for delete button
    document.getElementById('deleteButton').addEventListener('click', function (e) {
        if (confirm('Are you sure you want to delete this package?')) {
            const form = document.getElementById('editPackageForm');
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete';
            deleteInput.value = '1';
            form.appendChild(deleteInput);

            // Submit the form
            form.submit();
        }
    });
</script>

<?php
include('footer.php');
ob_end_flush(); // Flush output buffer
?>
