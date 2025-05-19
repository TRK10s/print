<?php

include('../db.php');
include('header.php');

// Check if session variable userRole exists
if (!isset($_SESSION['userRole'])) {
    echo "User role is not set. Please log in.";
    exit; // Stop further execution
}

$userRole = $_SESSION['userRole']; // Now it's safe to access session variable

$query = "SELECT packages.*, koperasbranch.BranchName 
          FROM packages 
          INNER JOIN koperasbranch ON packages.BranchID = koperasbranch.BranchID";
$result = mysqli_query($conn, $query);
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
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">View Packages</a>
                </li>
            </ul>
        </div>
        <div class="card">
            <div class="card-header">
                <!-- Bootstrap Filter Dropdown -->
                <div class="d-flex justify-content-between">
                    <div>
                        <label for="packageFilter" class="form-label">Filter by Package Name</label>
                        <select id="packageFilter" class="form-select" onchange="filterTable()">
                            <option value="">All</option>
                            <?php
                            // Fetch unique package names for filtering
                            $filterQuery = "SELECT DISTINCT BranchName FROM koperasbranch";
                            $filterResult = mysqli_query($conn, $filterQuery);
                            while ($filterRow = mysqli_fetch_assoc($filterResult)) {
                                echo "<option value='" . $filterRow['BranchName'] . "'>" . $filterRow['BranchName'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="packagesTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Package Name</th>
                            <th scope="col">Package Price</th>
                            <th scope="col">Branch</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch and display each package in the table
                        if (mysqli_num_rows($result) > 0) {
                            $counter = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $counter . "</td>";
                                echo "<td>" . $row['packageName'] . "</td>";
                                echo "<td>" . $row['PriceFloat'] . "</td>";
                                echo "<td class='branch-name'>" . $row['BranchName'] . "</td>";
                                echo "</tr>";
                                $counter++;
                            }
                        } else {
                            echo "<tr><td colspan='4'>No packages found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript function to filter the table based on selected package name
function filterTable() {
    const filterValue = document.getElementById("packageFilter").value.toLowerCase();
    const rows = document.querySelectorAll("#packagesTable tbody tr");

    rows.forEach(row => {
        const packageName = row.querySelector(".branch-name").textContent.toLowerCase();
        if (filterValue === "" || packageName === filterValue) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<?php
include('footer.php');
?>
