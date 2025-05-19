<?php
include('../db.php'); // Include the database connection file
include('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userphone = $_POST['userPhone'];
    $role = $_POST['role'];

    // Validate and sanitize inputs
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);
    $role = $conn->real_escape_string($role);
    $userphone = $conn->real_escape_string($userphone);

    // Encrypt the password (optional but recommended)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert the user into the database
    $query = "INSERT INTO users (username, userpass, userRole, userPhone) VALUES ('$username', '$hashed_password', '$role', '$userphone')";

    if ($conn->query($query) === TRUE) {
        echo "<script>
            alert('Registration successful!');
            window.location.href = 'viewusers.php';
        </script>";
    } else {
        echo "<script>
            alert('Error: " . $conn->error . "');
            window.history.back();
        </script>";
    }
}
?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manage Users</h3>
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
                    <a href="#">Manage Users</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Add User</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Add User</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <form action="register.php" method="POST">
                                    <label for="username">Username</label>
                                    <input type="text" name="username" class="form-control" id="username" placeholder="Enter Your Username" required>
                                    <label for="userPhone">User Phone</label>
                                    <input type="text" name="userPhone" class="form-control" id="userPhone" placeholder="Enter Your Phone Number" required>
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Your Password" required>
                                    <label for="role" class="form-label">Role</label>
                                    <select name="role" id="role" class="form-select" required>
                                        <option value="Admin">Admin</option>
                                        <option value="Staff">Staff</option>
                                        <option value="Student">Student</option>
                                    </select>
                            </div>
                            <div class="card-action">
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include('footer.php');
?>
