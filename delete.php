<?php
session_start();

if (!isset($_SESSION['authenticated'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    mysqli_query($conn, "DELETE FROM students WHERE id=$id");
}

header("Location: index.php?status=deleted");
exit();
?>