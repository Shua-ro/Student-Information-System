<?php
include 'config.php';

$id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: index.php");
    exit();
}

// Update the student's info when the form is submitted
/* TODO: Add a filter here to avoid SQL Injection  */
if (isset($_POST['update'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $section = $_POST['section'];
    $year = $_POST['year'];

    mysqli_query($conn, "UPDATE students SET student_id='$student_id', first_name='$first_name', last_name='$last_name', email='$email', course='$course', section='$section', year_level='$year' WHERE id=$id");
    header("Location: index.php?status=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit System Record</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

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
            <input type="email" name="email" value="<?php echo $row['email']; ?>" placeholder="Email" required>
            <input type="text" name="course" value="<?php echo $row['course']; ?>"
                placeholder="Course (e.g., BSMT, BSHM)" required>
            <input type="text" name="section" value="<?php echo $row['section']; ?>"
                placeholder="Section (e.g., MTJ2-B2)" required>
            <select name="year" required>
                <option value="1st Year" <?php if ($row['year_level'] == '1st Year')
                    echo 'selected'; ?>>1st Year</option>
                <option value="2nd Year" <?php if ($row['year_level'] == '2nd Year')
                    echo 'selected'; ?>>2nd Year</option>
                <option value="3rd Year" <?php if ($row['year_level'] == '3rd Year')
                    echo 'selected'; ?>>3rd Year</option>
                <option value="4th Year" <?php if ($row['year_level'] == '4th Year')
                    echo 'selected'; ?>>4th Year</option>
                <option value="5th Year" <?php if ($row['year_level'] == '5th Year')
                    echo 'selected'; ?>>5th Year</option>
            </select>
            <div class="formbtns">
                <button name="update" class="default-btn">Update</button>
                <button type="button" onclick="window.location.href='index.php'" class="default-btn">Cancel</button>
            </div>
        </form>
    </div>

</body>

</html>