<?php
include 'config.php';

// Save a new student to the database
/* TODO: Add a filter here to avoid SQL Injection & Move it into a separate file.
 Turn it into a MODAL and Create button */

if (isset($_POST['save'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $section = $_POST['section'];
    $year = $_POST['year'];

    mysqli_query($conn, "INSERT INTO students (student_id, first_name, last_name, email, course, section, year_level)
                         VALUES ('$student_id', '$first_name', '$last_name', '$email', '$course','$section', '$year')");
    header("Location: index.php?status=success");
    exit();
}

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of records
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Get records for current page, newest first
$result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC LIMIT $limit OFFSET $offset");
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
                <option value="DCPET">DCPET</option>
                <option value="BSIT">BSIT</option>
                <option value="BSCE">BSCE</option>
                <option value="DCET">DCET</option>
                <option value="DIT">DIT</option>
            </select>
            <select name="section" required>
                <option value="" disabled selected>Select Section</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
            <select name="year" required>
                <option value="" disabled selected>Select Year Level</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
            </select>
            <button type="submit" name="save" class="btn-save">Save</button>
        </form>
    </div>

    <h3>Students</h3>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Section</th>
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
                    <td class="sec">
                        <p><?php echo $row['section']; ?></p>
                    </td>
                    <td><?php echo $row['year_level']; ?></td>
                    <td>
                        <a class="action-link edit-lnk" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a class="action-link del-lnk" href="delete.php?id=<?php echo $row['id']; ?>"
                            onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1">&laquo; First</a>
                <a href="?page=<?php echo $page - 1; ?>">&lsaquo; Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &rsaquo;</a>
                <a href="?page=<?php echo $total_pages; ?>">Last &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    /*TODO:Fix bug on the record counting feature  */ ?>
    <!-- <p class="record-count">Showing <?php echo min($limit, $total_records - $offset); ?> of <?php echo $total_records; ?> records</p> -->

</body>

</html>