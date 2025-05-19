<?php
include('../db.php');
include('header.php');

// Check if 'id' is passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    exit("Invalid or missing ID.");
}
$userId = intval($_GET['id']); // Sanitize input

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $userPhone = mysqli_real_escape_string($conn, $_POST['userPhone']);
    $userRole = mysqli_real_escape_string($conn, $_POST['userRole']);

    // Update query
    $updateQuery = "UPDATE users SET username = '$username', userPhone = '$userPhone', userRole = '$userRole' WHERE userId = $userId";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('User updated successfully!'); window.location.href = 'viewusers.php';</script>";
    } else {
        exit("Error updating user: " . mysqli_error($conn));
    }
}

// Fetch user details from the database
$query = "SELECT * FROM users WHERE userId = $userId";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    exit("User not found.");
}

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="path_to_your_styles.css"> <!-- Add your CSS link -->
</head>
<body>
    <div class="container">
        <h3 class="fw-bold mb-3">Edit User</h3>
        <form method="POST">
            <input type="hidden" name="userId" value="<?php echo $userId; ?>">

            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= ($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="userPhone" class="form-label">Phone Number:</label>
                <input type="text" class="form-control" id="userPhone" name="userPhone" value="<?= ($user['userPhone']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="userRole" class="form-label">Role:</label>
                <select id="userRole" class="form-control" name="userRole">
                    <option value="Admin" <?= $user['userRole'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Student" <?= $user['userRole'] == 'Student' ? 'selected' : '' ?>>Student</option>
                    <option value="Staff" <?= $user['userRole'] == 'Staff' ? 'selected' : '' ?>>Staff</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>
