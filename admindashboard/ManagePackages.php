<?php
include('../db.php');
include('header.php');



// Fetch packages from database
$sql = "SELECT * FROM packages";
$result = mysqli_query($conn, $sql);
?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manage Packages</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Manage Packages</a>
                </li>
            </ul>
        </div>

        <!-- Add Package Button -->
        <div class="d-flex justify-content-end mb-4">
            <a href="addPackage.php" class="btn btn-primary btn-round">
                <i class="fas fa-plus-circle"></i> Add Package
            </a>
        </div>

        <!-- Package Cards -->
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center align-items-center mb-5">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($package = mysqli_fetch_assoc($result)) {
                            echo '
                            <div class="col-md-3 ps-md-0">
                                <div class="card-pricing2 card-' . strtolower($package['packageName']) . '">
                                    <div class="pricing-header">
                                        <h3 class="fw-bold mb-3">' . $package['packageName'] . '</h3>   
                                    </div>
                                    <div class="price-value">
                                        <div class="value">
                                            <span class="currency">$</span>
                                            <span class="amount">' . $package['PriceFloat'] . '</span>
                                            <span class="month">per unit</span>
                                        </div>
                                    </div>
                                    <ul class="pricing-content">
                                        <li>Status: ' . $package['Status'] . '</li>
                                    </ul>
                                    <a href="editPackage.php?id=' . $package['PackageID'] . '" class="btn btn-primary btn-border btn-lg w-75 fw-bold mb-3">Manage</a>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p>No packages available. Add one now!</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include('footer.php');
?>
