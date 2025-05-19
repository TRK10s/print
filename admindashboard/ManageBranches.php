<?php
ob_start(); // Start output buffering
include('../db.php');
include('header.php');
include('sidebar.php');

// Handle deletion
if (isset($_GET['delete'])) {
    $branchID = $_GET['delete'];
    $sqlDelete = "DELETE FROM koperasbranch WHERE BranchID = $branchID";
    if (!mysqli_query($conn, $sqlDelete)) {
        echo "Error deleting branch: " . mysqli_error($conn);
    } else {
        header('Location: ManageBranches.php'); // Redirect back to the same page
        exit;
    }
}

// Fetch all branches
$sql = "SELECT * FROM koperasbranch";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching branches: " . mysqli_error($conn));
}
?>
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Manage Branches</h3>
                <h6 class="op-7 mb-2">RapidPrint</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <a href="addBranch.php" class="btn btn-primary btn-round">Add New Branch</a>
            </div>
        </div>
        <div class="row">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($branch = mysqli_fetch_assoc($result)) {
                    echo '
                    <div class="col-md-4">
                        <div class="card card-round">
                            <div class="                            card-header">
                                <h4 class="card-title">' . $branch['BranchName'] . '</h4>
                            </div>
                            <div class="card-body">
                                <p><strong>Location:</strong> ' . $branch['BranchLoc'] . '</p>
                                <p><strong>Number:</strong> ' . $branch['BranchNumber'] . '</p>
                                <div class="d-flex justify-content-between">
                                    <a href="editBranch.php?id=' . $branch['BranchID'] . '" class="btn btn-info btn-sm">Edit</a>
                                    <a href="ManageBranches.php?delete=' . $branch['BranchID'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this branch?\')">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>No branches available. Add one now!</p>';
            }
            ?>
        </div>
    </div>
</div>
<?php
include('footer.php');
ob_end_flush(); // Flush output buffer
?>

