<?php 
include 'config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Turn into a number so nobody can inject bad SQL
    mysqli_query($conn, "DELETE FROM students WHERE id=$id");
}

header("Location: index.php?status=success");
exit();
?>