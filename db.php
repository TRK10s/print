<?php
$conn = mysqli_connect("localhost", "root", "", "rapidprint");

if (!$conn) {
    die('Could not connect: ' . mysqli_connect_error());
}

?>