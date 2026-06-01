<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();

if (!isset($_SESSION['authenticated'])) {
    header("Location: login.php");
    exit();
}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
</head>

<body class="form-page">
    <div class="form-container">
        <h3>Add New Student Record</h3>
        <form method="POST" action="index.php">
            <input type="text" name="student_id" placeholder="Student ID (e.g., 2024-00001)" required>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="course" placeholder="Course (e.g., BSMT, BSHM)" required>
            <input type="text" name="section" placeholder="Section (e.g., MTJ2-B2)" required>
            <select name="year" required>
                <option value="" disabled selected>Select Year Level</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
                <option value="5th Year">5th Year</option>
            </select>
            <div class="formbtns">
                <button type="submit" name="save" class="default-btn">Save</button>
                <button type="button" onclick="window.location.href='index.php'" class="default-btn">Cancel</button>
            </div>
        </form>
    </div>

</body>

</html>