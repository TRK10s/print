<?php
ob_start(); // Start output buffering
include('../db.php');
include('header.php');
include('sidebar.php');

session_start();
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'Admin') {
    echo "Unauthorized access. Please log in as Admin.";
    exit;
}

// Check if the branch ID is provided
if (!isset($_GET['id'])) {
    echo "Branch ID is missing.";
    exit;
}

$branchID = $_GET['id'];
$sql = "SELECT * FROM koperasbranch WHERE BranchID = $branchID";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    echo "Branch not found.";
    exit;
}

$branch = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['branchName'];
    $location = $_POST['branchLocation'];
    $number = $_POST['branchNumber'];

    $sqlUpdate = "UPDATE koperasbranch SET BranchName='$name', BranchLoc='$location', BranchNumber='$number' WHERE BranchID=$branchID";
    if (mysqli_query($conn, $sqlUpdate)) {
        header('Location: ManageBranches.php'); // Redirect to Manage Branch page
        exit;
    } else {
        $error = "Error updating branch: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Edit Branch</h3>
                <h6 class="op-7 mb-2">RapidPrint</h6>
            </div>
        </div>

        <!-- Form to Edit Branch -->
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
                                <input type="text" class="form-control" id="branchName" name="branchName" value="<?php echo $branch['BranchName']; ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="branchLocation">Branch Location</label>
                                <input type="text" class="form-control" id="branchLocation" name="branchLocation" value="<?php echo $branch['BranchLoc']; ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="branchNumber">Branch Number</label>
                                <input type="text" class="form-control" id="branchNumber" name="branchNumber" value="<?php echo $branch['BranchNumber']; ?>" required>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-round">Update Branch</button>
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
