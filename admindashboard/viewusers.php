<?php
include('../db.php');
include('header.php');

// Check if session variable userRole exists
if (!isset($_SESSION['userRole'])) {
    echo "User role is not set. Please log in.";
    exit;
}

$userRole = $_SESSION['userRole'];

$query = "SELECT userId, username, userPhone, userRole FROM users";
$result = mysqli_query($conn, $query);
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manage Users</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Manage Users</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">View User</a></li>
            </ul>
        </div>
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Username</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Role</th>
                            <th scope="col">Actions</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        $counter = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $counter . "</td>";
                            echo "<td>" . $row['username'] . "</td>";
                            echo "<td>" . $row['userPhone'] . "</td>";
                            echo "<td>" . $row['userRole'] . "</td>";
                            echo "<td>";
                            echo '<a href="editUser.php?id=' . $row['userId'] . '" class="btn btn-warning btn-sm">Edit</a> ';
                            echo '<button class="btn btn-danger btn-sm" onclick="deleteUser(' . $row['userId'] . ')">Delete</button>';
                            echo "</td>";
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>No users found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
        window.location.href = 'deleteUser.php?userId=' + userId;
    }
}
</script>

<?php include('footer.php'); ?>
