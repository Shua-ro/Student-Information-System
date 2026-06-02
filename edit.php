<?php

session_start();

if (!isset($_SESSION['authenticated'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['update'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);

    mysqli_query($conn, "UPDATE students SET student_id='$student_id', first_name='$first_name', last_name='$last_name', course='$course', section='$section', year_level='$year', gender='$gender' WHERE id=$id");
    header("Location: index.php?status=success");
    exit();
}
?>


<?php
$permanent_courses = ['BSMT', 'BSHM'];
$courses_result = mysqli_query($conn, "SELECT DISTINCT course FROM students ORDER BY course");
$db_courses = [];
while ($row2 = mysqli_fetch_assoc($courses_result)) {
    $db_courses[] = $row2['course'];
}
$all_courses = array_unique(array_merge($permanent_courses, $db_courses));
sort($all_courses);

$permanent_sections = ['MTJ2-B2', 'Section 1'];
$sections_result = mysqli_query($conn, "SELECT DISTINCT section FROM students ORDER BY section");
$db_sections = [];
while ($row2 = mysqli_fetch_assoc($sections_result)) {
    $db_sections[] = $row2['section'];
}
$all_sections = array_unique(array_merge($permanent_sections, $db_sections));
sort($all_sections);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit System Record</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="form-page">

    <div class="edit-box">
        <h3>Modify Student Records</h3>
        <form method="POST">
            <?php /* TODO: Make input not required */ ?>
            <input type="text" name="student_id" value="<?php echo $row['student_id']; ?>"
                placeholder="Student ID (e.g., 2024-00001)" required>
            <input type="text" name="first_name" value="<?php echo $row['first_name']; ?>" placeholder="First Name"
                required>
            <input type="text" name="last_name" value="<?php echo $row['last_name']; ?>" placeholder="Last Name"
                required>
            <select name="course" required>
                <?php foreach ($all_courses as $c): ?>
                    <option value="<?php echo $c; ?>"
                        <?php echo $row['course'] === $c ? 'selected' : ''; ?>>
                        <?php echo $c; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="section" required>
                <?php foreach ($all_sections as $s): ?>
                    <option value="<?php echo $s; ?>"
                        <?php echo $row['section'] === $s ? 'selected' : ''; ?>>
                        <?php echo $s; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="year" required>
                <?php $years = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year']; ?>
                <?php foreach ($years as $y): ?>
                    <option value="<?php echo $y; ?>"
                        <?php echo $row['year_level'] === $y ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="gender" required>
                <?php $genders = ['Male', 'Female']; ?>
                <?php foreach ($genders as $g): ?>
                    <option value="<?php echo $g; ?>"
                        <?php echo $row['gender'] === $g ? 'selected' : ''; ?>>
                        <?php echo $g; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="formbtns">
                <button name="update" class="default-btn">Update</button>
                <button type="button" onclick="window.location.href='index.php'" class="default-btn">Cancel</button>
            </div>
        </form>
    </div>

</body>

</html>