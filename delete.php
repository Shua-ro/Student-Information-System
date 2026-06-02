<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    mysqli_query($conn, "DELETE FROM students WHERE id=$id");
}

header("Location: index.php?status=success");
exit();
?>