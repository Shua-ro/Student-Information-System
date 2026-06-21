<?php

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
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);

    mysqli_query($conn, "INSERT INTO students (student_id, first_name, last_name, course, section, year_level)
                         VALUES ('$student_id', '$first_name', '$last_name', '$course','$section', '$year')");

    mysqli_query($conn, "INSERT IGNORE INTO courses (code) VALUES ('$course')");
    mysqli_query($conn, "INSERT IGNORE INTO sections (name) VALUES ('$section')");

    header("Location: index.php?status=success");
    exit();
}

if (isset($_POST['bulk_delete'])) {
    $ids = isset($_POST['selected_ids']) && is_array($_POST['selected_ids']) ? $_POST['selected_ids'] : [];
    $ids = array_filter(array_map('intval', $ids), function ($id) {
        return $id > 0;
    });

    $deleted_count = 0;
    if (!empty($ids)) {
        $id_list = implode(',', $ids);
        mysqli_query($conn, "DELETE FROM students WHERE id IN ($id_list)");
        $deleted_count = count($ids);
    }

    header("Location: index.php?status=bulk_deleted&count=$deleted_count");
    exit();
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$course_filter = isset($_GET['course']) && $_GET['course'] !== '' ? mysqli_real_escape_string($conn, $_GET['course']) : '';
$course_param = $course_filter ? "&course=$course_filter" : '';

// Section filter
$section_filter = isset($_GET['section']) && $_GET['section'] !== '' ? mysqli_real_escape_string($conn, $_GET['section']) : '';
$section_param = $section_filter ? "&section=$section_filter" : '';

// Program filter (same as course filter)
$program_filter = $course_filter;
$program_param = $course_param;

// Year filter
$year_filter = isset($_GET['year']) && $_GET['year'] !== '' ? mysqli_real_escape_string($conn, $_GET['year']) : '';
$year_param = $year_filter ? "&year=$year_filter" : '';

// Gender fitler
$gender_filter = isset($_GET['gender']) && $_GET['gender'] !== '' ? mysqli_real_escape_string($conn, $_GET['gender']) : '';
$gender_param = $gender_filter ? "&gender=$gender_filter" : '';

// Search filter (matches against the whole table, not just the current page)
$search_filter = isset($_GET['search']) && trim($_GET['search']) !== '' ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$search_param = $search_filter ? "&search=" . urlencode($search_filter) : '';

//Where clause
$conditions = [];
if ($section_filter) {
    $conditions[] = "section = '$section_filter'";
}
if ($program_filter) {
    $conditions[] = "course = '$program_filter'";
}
if ($year_filter) {
    $conditions[] = "year_level = '$year_filter'";
}
if ($gender_filter) {
    $conditions[] = "gender = '$gender_filter'";
}
if ($search_filter) {
    $conditions[] = "(student_id LIKE '%$search_filter%' OR first_name LIKE '%$search_filter%' OR last_name LIKE '%$search_filter%' OR CONCAT(first_name, ' ', last_name) LIKE '%$search_filter%')";
}
$where_clause = '';
if (!empty($conditions)) {
    $where_clause = "WHERE " . implode(" AND ", $conditions);
}

// Other-filters string (everything except search) — used for the "clear search" link
$other_filters_param = $section_param . $program_param . $year_param . $gender_param;

// Get total number of records
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students $where_clause");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Get records for current page, newest first
$result = mysqli_query($conn, "SELECT * FROM students $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset");

$courses_result3 = mysqli_query($conn, "SELECT code FROM courses ORDER BY code");
$all_courses_filter = [];
while ($row3 = mysqli_fetch_assoc($courses_result3)) {
    $all_courses_filter[] = $row3['code'];
}

$sections_result3 = mysqli_query($conn, "SELECT name FROM sections ORDER BY name");
$all_sections_filter = [];
while ($row3 = mysqli_fetch_assoc($sections_result3)) {
    $all_sections_filter[] = $row3['name'];
}
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
    <script src="navbar.js" defer></script>
    <script src="alert-toast.js" defer></script>
    <script src="delete-modal.js" defer></script>
</head>

<body>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert-status alert-success" id="saveAlert">
            <i class="ti ti-circle-check"></i>
            <span>Student added successfully.</span>
            <button type="button" class="alert-dismiss" aria-label="Dismiss" onclick="document.getElementById('saveAlert').remove();">
                <i class="ti ti-x"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
        <div class="alert-status alert-error" id="saveAlert">
            <i class="ti ti-circle-check"></i>
            <span>Student deleted successfully.</span>
            <button type="button" class="alert-dismiss" aria-label="Dismiss" onclick="document.getElementById('saveAlert').remove();">
                <i class="ti ti-x"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'bulk_deleted'): ?>
        <?php $deleted_count = isset($_GET['count']) ? (int) $_GET['count'] : 0; ?>
        <div class="alert-status alert-success" id="saveAlert">
            <i class="ti ti-circle-check"></i>
            <span><?php echo $deleted_count; ?> student<?php echo $deleted_count === 1 ? '' : 's'; ?> deleted successfully.</span>
            <button type="button" class="alert-dismiss" aria-label="Dismiss" onclick="document.getElementById('saveAlert').remove();">
                <i class="ti ti-x"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <div class="logo-icon"><img src="Logo2sis.svg" alt="SIS Portal logo"></div>
                <span class="logo-text">SIS<span class="logo-accent">Portal</span></span>
            </div>
        </div>
        <button class="hamburger" id="navHamburger" aria-label="Toggle menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="navbar-right navbar" id="navbarRight">
            <a href="#" class="nav-link" style="color:#F5C842;">Dashboard</a>
            <a href="encode_grades.php" class="nav-link" style="color:White;">Grades</a>
            <a href="logout.php" class="nav-btn-logout" id="logoutLink">Logout</a>
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
            <form class="search-wrap" id="searchForm" action="index.php" method="get">
                <i class="ti ti-search"></i>
                <input type="text" id="searchInput" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search_filter); ?>" autocomplete="off">
                <?php if ($section_filter): ?><input type="hidden" name="section" value="<?php echo htmlspecialchars($section_filter); ?>"><?php endif; ?>
                <?php if ($program_filter): ?><input type="hidden" name="course" value="<?php echo htmlspecialchars($program_filter); ?>"><?php endif; ?>
                <?php if ($year_filter): ?><input type="hidden" name="year" value="<?php echo htmlspecialchars($year_filter); ?>"><?php endif; ?>
                <?php if ($gender_filter): ?><input type="hidden" name="gender" value="<?php echo htmlspecialchars($gender_filter); ?>"><?php endif; ?>
            </form>
            <span class="filter-label">Filter</span>
            <div class="filter-pills">
                <select class="filter-pill"
                    onchange="location.href='?section=' + this.value + '<?php echo $program_param; ?>' + '<?php echo $year_param ?>' + '<?php echo $gender_param ?>' + '<?php echo $search_param ?>'">
                    <option value="">All Sections</option>
                    <?php foreach ($all_sections_filter as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $section_filter === $s ? 'selected' : ''; ?>>
                            <?php echo $s; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="filter-pill"
                    onchange="location.href='?course=' + this.value + '<?php echo $section_param; ?>' + '<?php echo $year_param ?>' + '<?php echo $gender_param ?>' + '<?php echo $search_param ?>'">
                    <option value="">All Program</option>
                    <?php foreach ($all_courses_filter as $p): ?>
                        <option value="<?php echo $p; ?>" <?php echo $program_filter === $p ? 'selected' : ''; ?>>
                            <?php echo $p; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="filter-pill"
                    onchange="location.href='?year=' + this.value + '<?php echo $section_param; ?>' + '<?php echo $program_param ?>' + '<?php echo $gender_param ?>' + '<?php echo $search_param ?>'">
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
                <select class="filter-pill"
                    onchange="location.href='?gender=' + this.value + '<?php echo $section_param; ?>' + '<?php echo $program_param ?>' + '<?php echo $year_param ?>' + '<?php echo $search_param ?>'  ">
                    <option value="">All Gender</option>
                    <?php
                    $gender_result2 = mysqli_query($conn, "SELECT DISTINCT gender FROM students ORDER BY gender");
                    while ($g = mysqli_fetch_assoc($gender_result2)): ?>
                        <option value="<?php echo $g['gender']; ?>" <?php echo $gender_filter === $g['gender'] ? 'selected' : ''; ?>>
                            <?php echo $g['gender']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <h3 class="table-title">Students</h3>
                <div class="header-actions">
                    <button type="button" id="toggleBulkSelect" class="btn-bulk-toggle">
                        <i class="ti ti-checkbox"></i> Bulk Delete
                    </button>
                    <a href="add.php" class="btn-add"><i class="ti ti-plus"></i> Add Student</a>
                </div>
            </div>

            <form method="POST" action="index.php" id="bulkDeleteForm">
                <div class="bulk-actions-bar" id="bulkActionsBar" hidden>
                    <span id="selectedCount">0 selected</span>
                    <button type="submit" name="bulk_delete" class="btn-bulk-delete" id="bulkDeleteBtn">
                        <i class="ti ti-trash"></i> Delete Selected
                    </button>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="checkbox-col"><input type="checkbox" id="selectAll" aria-label="Select all students"></th>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Section</th>
                                <th>Year</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="checkbox-col"><input type="checkbox" class="row-checkbox" name="selected_ids[]" value="<?php echo $row['id']; ?>" aria-label="Select <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"></td>
                                    <td><strong><?php echo htmlspecialchars($row['student_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                                    <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                    <td class="actions-cell">
                                        <a class="action-link edit-lnk" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                                        <span class="action-sep">|</span>
                                        <a class="action-link del-lnk" href="delete.php?id=<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-wrap">
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1<?php echo $section_param; ?><?php echo $program_param; ?><?php echo $year_param; ?><?php echo $gender_param; ?><?php echo $search_param; ?>" class="pg-btn">&laquo;
                                First</a>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $section_param; ?><?php echo $program_param; ?><?php echo $year_param; ?><?php echo $gender_param; ?><?php echo $search_param; ?>"
                                class="pg-btn">&lsaquo;
                                Prev</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $section_param; ?><?php echo $program_param; ?><?php echo $year_param; ?><?php echo $gender_param; ?><?php echo $search_param; ?>"
                                class="pg-btn <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $section_param; ?><?php echo $program_param; ?><?php echo $year_param; ?><?php echo $gender_param; ?><?php echo $search_param; ?>"
                                class="pg-btn">Next
                                &rsaquo;</a>
                            <a href="?page=<?php echo $total_pages; ?><?php echo $section_param; ?><?php echo $program_param; ?><?php echo $year_param; ?><?php echo $gender_param; ?><?php echo $search_param; ?>"
                                class="pg-btn">Last
                                &raquo;</a>
                        <?php endif; ?>
                    </div>

                    <p class="record-count">Showing
                        <?php echo min($page * $limit, $total_records); ?>
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
    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModalOverlay" hidden>
        <div class="modal-box" role="alertdialog" aria-modal="true" aria-labelledby="deleteModalTitle" aria-describedby="deleteModalDesc">
            <div class="modal-icon">
                <i class="ti ti-trash"></i>
            </div>
            <h3 class="modal-title" id="deleteModalTitle">Delete student record?</h3>
            <p class="modal-desc" id="deleteModalDesc">
                Are you sure you want to delete <strong id="deleteModalName">this student</strong>? This action cannot be undone.
            </p>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" id="deleteModalCancel">Cancel</button>
                <a href="#" class="modal-btn modal-btn-danger" id="deleteModalConfirm">Delete</a>
            </div>
        </div>
    </div>

</body>

</html>