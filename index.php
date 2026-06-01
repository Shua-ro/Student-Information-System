<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();

if (!isset($_SESSION['authenticated'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

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
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$course_filter = isset($_GET['course']) && $_GET['course'] !== '' ? mysqli_real_escape_string($conn, $_GET['course']) : '';
$course_param = $course_filter ? "&section=$course_filter" : '';

// Section filter
$section_filter = isset($_GET['section']) && $_GET['section'] !== '' ? mysqli_real_escape_string($conn, $_GET['section']) : '';
/* $where_clause = $section_filter ? "WHERE section = '$section_filter' & " : ''; */
$section_param = $section_filter ? "&section=$section_filter" : '';

// Program filter
$program_filter = isset($_GET['course']) && $_GET['course'] !== '' ? mysqli_real_escape_string($conn, $_GET['course']) : '';
$program_param = $program_filter ? "&course=$program_filter" : '';

// Year filter
$year_filter = isset($_GET['year']) && $_GET['year'] !== '' ? mysqli_real_escape_string($conn, $_GET['year']) : '';
$year_param = $program_filter ? "&year=$year_filter" : '';

// Gender fitler

//Where clause
$conditions = [];
if ($section_filter) {
    $conditions[] = "section = '$section_filter'";
}
if ($program_filter) {
    $conditions[] = "course = '$program_filter'";
}
$where_clause = '';
if (!empty($conditions)) {
    $where_clause = "WHERE " . implode(" AND ", $conditions);
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS Portal — Student Records</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <script src="search.js" defer></script>
</head>

<body>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert-status"><i class="ti ti-circle-check"></i> Student added successfully.</div>
    <?php endif; ?>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <div class="logo-icon"><i class="ti ti-school"></i></div>
                <span class="logo-text">SIS<span class="logo-accent">Portal</span></span>
            </div>
        </div>
        <div class="navbar-right">
            <a href="#" class="nav-link">Dashboard</a>
            <a href="logout.php" class="nav-btn-logout">Logout</a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- Page Header -->
        <div class="page-header">
            <h1>Student Records</h1>
            <p>Manage and view all student information</p>
        </div>

        <!-- Search & Filter Bar -->
        <div class="filter-card">
            <div class="search-wrap">
                <i class="ti ti-search"></i>
                <input type="text" id="searchInput" placeholder="Search...">
            </div>
            <span class="filter-label">Filter</span>
            <div class="filter-pills">
                <select class="filter-pill"
                    onchange="location.href='?section=' + this.value + '<?php echo $program_param; ?>' + '<?php echo $year_param ?>'">
                    <option value="">All Sections</option>
                    <?php
                    // Reset sections result pointer
                    $sections_result2 = mysqli_query($conn, "SELECT DISTINCT section FROM students ORDER BY section");
                    while ($s = mysqli_fetch_assoc($sections_result2)): ?>
                        <option value="<?php echo $s['section']; ?>" <?php echo $section_filter === $s['section'] ? 'selected' : ''; ?>>
                            <?php echo $s['section']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <select class="filter-pill"
                    onchange="location.href='?section=' + this.value + '<?php echo $section_param; ?>' +'<?php echo $year_param ?>'">
                    <option value="">All Program</option>
                    <?php
                    // Reset sections result pointer
                    $programs_result2 = mysqli_query($conn, "SELECT DISTINCT course FROM students ORDER BY course");
                    while ($p = mysqli_fetch_assoc($programs_result2)): ?>
                        <option value="<?php echo $p['course']; ?>" <?php echo $program_filter === $p['course'] ? 'selected' : ''; ?>>
                            <?php echo $p['course']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <select class="filter-pill"
                    onchange="location.href='?section=' + this.value + '<?php echo $section_param; ?>' + '<?php echo $program_param ?>'  ">
                    <option value="">All Year Level</option>
                    <?php
                    // Reset sections result pointer
                    $year_result2 = mysqli_query($conn, "SELECT DISTINCT year_level FROM students ORDER BY year_level");
                    while ($y = mysqli_fetch_assoc($year_result2)): ?>
                        <option value="<?php echo $y['year_level']; ?>" <?php echo $year_filter === $y['year_level'] ? 'selected' : ''; ?>>
                            <?php echo $y['year_level']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <select class="filter-pill">
                    <option>All Gender</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <h3 class="table-title">Students</h3>
                <a href="add.php" class="btn-add"><i class="ti ti-plus"></i> Add Student</a>
            </div>

            <div class="table-wrap">
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
                    <tbody id="studentTableBody">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['student_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['course']); ?></td>
                                <td><?php echo htmlspecialchars($row['section']); ?></td>
                                <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                <td class="actions-cell">
                                    <a class="action-link edit-lnk" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                                    <span class="action-sep">|</span>
                                    <a class="action-link del-lnk" href="delete.php?id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-wrap">
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1<?php echo $section_param; ?><?php echo $program_param; ?>" class="pg-btn">&laquo;
                                First</a>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $section_param; ?><?php echo $program_param; ?>"
                                class="pg-btn">&lsaquo;
                                Prev</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $section_param; ?><?php echo $program_param; ?>"
                                class="pg-btn <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $section_param; ?><?php echo $program_param; ?>"
                                class="pg-btn">Next
                                &rsaquo;</a>
                            <a href="?page=<?php echo $total_pages; ?><?php echo $section_param; ?><?php echo $program_param; ?>"
                                class="pg-btn">Last
                                &raquo;</a>
                        <?php endif; ?>
                    </div>

                    <p class="record-count">Showing
                        <?php
                        if ($total_records % $limit == 0) {
                            echo $limit * $page;
                        } else {
                            $last_record = ($limit * $total_pages) - $total_records;
                            $final_record = ($limit * $total_pages) - $last_record;
                            $count = ($page != $total_pages) ? $limit * $page : $final_record;
                            echo $count;
                        } ?>
                        of <?php echo $total_records; ?> records
                    </p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <p>© 2026 <strong>SIS Portal</strong> &nbsp;·&nbsp; All rights reserved</p>
    </footer>

</body>

</html>