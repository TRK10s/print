<?php

session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Protect against SQL injection
    $username = $conn->real_escape_string($username);
    $role = $conn->real_escape_string($role);

    // Query to check login credentials
    $query = "SELECT * FROM users WHERE username = '$username' AND userRole = '$role'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch user details
        $storedPassword = $user['userpass'];

        // Check if password is hashed or not
        if (password_verify($password, $storedPassword)) {
            // Login successful for hashed password
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['userRole'] = $user['userRole'];

            if ($user['userRole'] === 'Admin' || $user['userRole'] === 'Staff') {
                header('Location: admindashboard/index.php');
            } else {
                header('Location: admindashboard/index.php');
            }
            exit();
        } elseif ($password === $storedPassword) {
            // Login successful for plaintext password
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['userRole'] = $user['userRole'];

            if ($user['userRole'] === 'Admin' || $user['userRole'] === 'Staff') {
                header('Location: admindashboard/index.php');
            } else {
                header('Location: admindashboard/index.php');
            }
            exit();
        } else {
            $error = "Invalid username, password, or role!";
        }
    } else {
        $error = "Invalid username, password, or role!";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-container {
      background-color: white; /* White background */
      padding: 2rem;
      border-radius: 15px; /* Rounded edges */
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
      max-width: 495px; /* Limit form width */
        margin-bottom: 350px;
    }
    .background-image {
      background: url('campus-image.jpg') no-repeat center center;
      background-size: cover;
      height: 100vh;
    }
  </style>
</head>
<body style="background-image: url(assets/img/login.jpg)">
  <div class="container-fluid p-0">
    <div class="row g-0">
      <!-- Background Image -->
      <div class="col-lg-8 d-none d-lg-block background-image">
      </div>
      <!-- Form Section -->
      <div class="col-lg-4 d-flex align-items-center justify-content-center">
        <div class="form-container">
          <div class="text-center mb-4">
            <img src="assets/img/umpsa-logo-new.jpeg" alt="UMPSA Logo" class="img-fluid mb-3" style="max-width: 120px;">
            <h5>Universiti Malaysia Pahang Al-Sultan Abdullah</h5>
          </div>
<form action="login.php" method="POST">
  <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" name="username" class="form-control" id="username" placeholder="Enter Your Username" required>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
  </div>
  <div class="mb-3">
    <label for="role" class="form-label">Role</label>
    <select name="role" id="role" class="form-select" required>
    <option selected>Admin</option>
      <option>Staff</option>
      <option>Student</option>
    </select>
  </div>
  <div class="d-grid">
    <button type="submit" class="btn btn-primary">Login</button>
  </div>
  <?php if (isset($error)): ?>
  <div class="text-danger mt-3"><?php echo $error; ?></div>
  <?php endif; ?>
</form>


        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>