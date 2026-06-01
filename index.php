<?php 
session_start(); 

if (!isset($_SESSION['authenticated'])) { 
    header("Location: login.php"); 
    exit(); 

    } 

    include 'config.php';

// Save a new student to the database
/* TODO: Add a filter here to avoid SQL Injection & Move it into a separate file.
 Turn it into a MODAL and Create button */

if (isset($_POST['save'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);

    mysqli_query($conn, "INSERT INTO students (student_id, first_name, last_name, email, course, section, year_level)
                         VALUES ('$student_id', '$first_name', '$last_name', '$email', '$course','$section', '$year')");
    header("Location: index.php?status=success");
    exit();
}

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Section filter
$section_filter = isset($_GET['section']) && $_GET['section'] !== '' ? mysqli_real_escape_string($conn, $_GET['section']) : '';
$where_clause = $section_filter ? "WHERE section = '$section_filter'" : '';
$section_param = $section_filter ? "&section=$section_filter" : '';

// Get total number of records
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students $where_clause");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Get records for current page, newest first
$result = mysqli_query($conn, "SELECT * FROM students $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset");

// Get distinct sections for filter dropdown
$sections_result = mysqli_query($conn, "SELECT DISTINCT section FROM students ORDER BY section");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SIS Engine Management</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>


<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert-status">Completed successfully.</div>
<?php endif; ?>

<body>
    <h1>Student Records</h1>
    <p>Manage and view all student information</p>
    
    <div class="add-refresh">
        <button onclick="window.location.href='add.php'" class="default-btn">Add Student</button>
    <a href="logout.php" class="default-btn">
    Logout
    </a>

        <select class="filter-select" onchange="location.href='?section='+this.value">
            <option value="">All Sections</option>
            <?php while ($s = mysqli_fetch_assoc($sections_result)): ?>
                <option value="<?php echo $s['section']; ?>" <?php echo $section_filter === $s['section'] ? 'selected' : ''; ?>><?php echo $s['section']; ?></option>
            <?php endwhile; ?>
        </select>
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
                <a href="?page=1<?php echo $section_param; ?>">&laquo; First</a>
                <a href="?page=<?php echo $page - 1; ?><?php echo $section_param; ?>">&lsaquo; Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo $section_param; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo $section_param; ?>">Next &rsaquo;</a>
                <a href="?page=<?php echo $total_pages; ?><?php echo $section_param; ?>">Last &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    /*TODO:Fix bug on the record counting feature  */ ?>
    <p class="record-count">Showing
        <?php
        if ($total_records % $limit == 0) {
            echo $limit * $page;
        } else {
            $last_record = ($limit * $total_pages) - $total_records;
            $final_record = ($limit * $total_pages) - $last_record;
            $count = ($page != $total_pages) ? $limit * $page : $final_record;
            echo $count;
        }
        ; ?>
        of
        <?php echo $total_records; ?> records
    </p>

</body>

</html>