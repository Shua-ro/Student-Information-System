<?php

session_start();

if (!isset($_SESSION['authenticated'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$permanent_courses = ['BSMT', 'BSHM'];

$courses_result = mysqli_query($conn, "SELECT DISTINCT course FROM students ORDER BY course");
$db_courses = [];
while ($row = mysqli_fetch_assoc($courses_result)) {
    $db_courses[] = $row['course'];
}
$all_courses = array_unique(array_merge($permanent_courses, $db_courses));
sort($all_courses);

// Permanent sections
$permanent_sections = ['MTJ2-B2', 'Section 1'];

$sections_result = mysqli_query($conn, "SELECT DISTINCT section FROM students ORDER BY section");
$db_sections = [];
while ($row = mysqli_fetch_assoc($sections_result)) {
    $db_sections[] = $row['section'];
}
$all_sections = array_unique(array_merge($permanent_sections, $db_sections));
sort($all_sections);
?>
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
            <select name="course" required>
                <option value="" disabled selected>Select Course</option>
                <?php foreach ($all_courses as $course): ?>
                    <option value="<?php echo $course; ?>">
                        <?php echo $course; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="section" required>
                <option value="" disabled selected>Select Section</option>
                <?php foreach ($all_sections as $section): ?>
                    <option value="<?php echo $section; ?>">
                        <?php echo $section; ?>
                    </option>
                <?php endforeach; ?>
            </select>
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