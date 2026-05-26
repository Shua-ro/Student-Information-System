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
    $year = $_POST['year'];

    mysqli_query($conn, "UPDATE students SET student_id='$student_id', first_name='$first_name', last_name='$last_name', email='$email', course='$course', year_level='$year' WHERE id=$id");
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
            <select name="course" required>
                <option value="" disabled>Select Course</option>
                <option value="DCPET" <?php if ($row['course'] == 'DCPET')
                    echo 'selected'; ?>>DCPET</option>
                <option value="BSIT" <?php if ($row['course'] == 'BSIT')
                    echo 'selected'; ?>>BSIT</option>
                <option value="BSCE" <?php if ($row['course'] == 'BSCE')
                    echo 'selected'; ?>>BSCE</option>
                <option value="DCET" <?php if ($row['course'] == 'DCET')
                    echo 'selected'; ?>>DCET</option>
                <option value="DIT" <?php if ($row['course'] == 'DIT')
                    echo 'selected'; ?>>DIT</option>
            </select>
            <select name="section" required>
                <option value="" disabled selected>Select Section</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
            <select name="year" required>
                <option value="1st Year" <?php if ($row['year_level'] == '1st Year')
                    echo 'selected'; ?>>1st Year</option>
                <option value="2nd Year" <?php if ($row['year_level'] == '2nd Year')
                    echo 'selected'; ?>>2nd Year</option>
                <option value="3rd Year" <?php if ($row['year_level'] == '3rd Year')
                    echo 'selected'; ?>>3rd Year</option>
            </select>

            <button name="update" class="btn-save">Update</button>
        </form>
    </div>

</body>

</html>