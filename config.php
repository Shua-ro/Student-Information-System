<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost", "root", "", "StudentSystem");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>