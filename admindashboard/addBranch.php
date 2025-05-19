<?php
ob_start(); // Start output buffering
include('../db.php');
include('header.php'); 
include('sidebar.php'); 

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['branchName'];
    $location = $_POST['branchLocation'];
    $number = $_POST['branchNumber'];

    $sql = "INSERT INTO koperasbranch (BranchName, BranchLoc, BranchNumber) VALUES ('$name', '$location', '$number')";
    if (mysqli_query($conn, $sql)) {
        header('Location: ManageBranches.php'); // Redirect to the manage branches page
        exit;
    } else {
        $error = "Error adding branch: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Add New Branch</h3>
                <h6 class="op-7 mb-2">RapidPrint</h6>
            </div>
        </div>

        <!-- Form to Add Branch -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-round">
                    <div class="card-header">
                        <h4 class="card-title">Branch Information</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php } ?>
                        <form method="POST">
                            <div class="form-group mb-3">
                                <label for="branchName">Branch Name</label>
                                <input type="text" class="form-control" id="branchName" name="branchName" placeholder="Enter branch name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="branchLocation">Branch Location</label>
                                <input type="text" class="form-control" id="branchLocation" name="branchLocation" placeholder="Enter branch location" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="branchNumber">Branch Number</label>
                                <input type="text" class="form-control" id="branchNumber" name="branchNumber" placeholder="Enter branch number" required>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-round">Add Branch</button>
                                <a href="ManageBranches.php" class="btn btn-secondary btn-round ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php');
ob_end_flush(); // Flush output buffer
 ?>
