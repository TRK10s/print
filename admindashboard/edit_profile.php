<?php
// Include the necessary files
include('../db.php');
include('header.php');

if (!isset($_SESSION['username']) || $_SESSION['userRole'] !== 'Student') {
    echo "<script>alert('Access denied. Please log in as a student to access this page.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

$userId = $_SESSION['userID'];

$query = "SELECT * FROM users WHERE userID = '$userId'";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo "<script>alert('User data not found.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

$userData = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = (trim($_POST['username']));
    $phone = (trim($_POST['userPhone']));

    if (empty($name) || empty($phone)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        $sanitizedName = $conn->real_escape_string($name);
        $sanitizedPhone = $conn->real_escape_string($phone);

        $updateQuery = "UPDATE users SET username = '$sanitizedName', userPhone = '$sanitizedPhone' WHERE userID = '$userId'";

        if ($conn->query($updateQuery)) {
            echo "<script>alert('Profile updated successfully.'); window.location.href = 'edit_profile.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error updating profile. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Profile</h3>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Update Your Profile Details</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Name</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= ($userData['username']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="userPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="userPhone" name="userPhone" value="<?= ($userData['userPhone']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
