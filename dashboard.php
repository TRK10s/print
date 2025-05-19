<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.html'); // Redirect to login if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
</head>
<body>
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
  <p>Role: <?php echo $_SESSION['userRole']; ?></p>
  <a href="logout.php">Logout</a>
</body>
</html>
