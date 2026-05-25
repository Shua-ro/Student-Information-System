<?php
include 'config.php';

// Save a new student to the database
if (isset($_POST['save'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $year = $_POST['year'];

    mysqli_query($conn, "INSERT INTO students (student_id, first_name, last_name, email, course, year_level) 
                         VALUES ('$student_id', '$first_name', '$last_name', '$email', '$course', '$year')");
    header("Location: index.php?status=success");
    exit();
}

// Get all students from the database, newest first
$result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SIS Engine Management</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert-status">Completed successfully.</div>
    <?php endif; ?>

    <div class="form-container">
        <h3>Add New Student Record</h3>
        <form method="POST" action="index.php">
            <input type="text" name="student_id" placeholder="Student ID (e.g., 2024-00001)" required>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <select name="course" required>
                <option value="" disabled selected>Select Course</option>
                <option value="BSCS">BSCS</option>
                <option value="BSIT">BSIT</option>
                <option value="BSCE">BSCE</option>
                <option value="BSEE">BSEE</option>
                <option value="BSME">BSME</option>
            </select>
            <select name="year" required>
                <option value="" disabled selected>Select Year Level</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
            </select>
            <button type="submit" name="save" class="btn-save">Execute Save</button>
        </form>
    </div>

    <h3>Active Student Ledger</h3>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><strong><?php echo $row['student_id']; ?></strong></td>
                    <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['course']; ?></td>
                    <td><?php echo $row['year_level']; ?></td>
                    <td>
                        <a class="action-link edit-lnk" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a class="action-link del-lnk" href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>

</html>