<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost", "root", "", "StudentSystem", 3306);



if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>