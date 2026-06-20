<?php
session_start();
if (!isset($_SESSION['authenticated'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';


$current_month = (int) date('n');
$current_y     = (int) date('Y');
if ($current_month >= 6) {
    $current_year = $current_y . '-' . ($current_y + 1);
} else {
    $current_year = ($current_y - 1) . '-' . $current_y;
}

$courses_result = mysqli_query($conn, "SELECT DISTINCT course FROM students ORDER BY course");
$all_courses = [];
while ($row = mysqli_fetch_assoc($courses_result)) {
    $all_courses[] = $row['course'];
}

$subjects_result = mysqli_query($conn, "SELECT id, subject_code, subject_name, course FROM subjects ORDER BY subject_code");
$all_subjects = [];
while ($row = mysqli_fetch_assoc($subjects_result)) {
    $all_subjects[] = $row;
}

$academic_year = $_GET['academic_year'] ?? $current_year;
$course        = $_GET['course']        ?? '';
$subject_id    = $_GET['subject_id']    ?? '';

$save_message   = '';
$save_type      = '';
$invalid_grades = []; 

if (isset($_POST['save_grades'])) {
    $posted_academic_year = $_POST['academic_year'] ?? '';
    $posted_subject_id    = $_POST['subject_id']    ?? '';
    $grades               = $_POST['grades']        ?? [];

    if (!empty($grades) && $posted_subject_id && $posted_academic_year) {

        $to_save = [];
        foreach ($grades as $student_id => $grade) {
            $grade = trim($grade);
            if ($grade === '') continue; 

            if (!is_numeric($grade) || $grade < 0 || $grade > 100) {
                $invalid_grades[(int)$student_id] = $grade;
                continue;
            }
            $to_save[(int)$student_id] = (float)$grade;
        }

        if (!empty($invalid_grades)) {
            $save_message = count($invalid_grades) . ' grade(s) are invalid (must be a number between 0 and 100). Nothing was saved — please fix the highlighted field(s) and try again.';
            $save_type    = 'error';
        } elseif (empty($to_save)) {
            $save_message = 'No grades were entered.';
            $save_type    = 'error';
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO grades (student_id, subject_id, academic_year, grade)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE grade = VALUES(grade), updated_at = CURRENT_TIMESTAMP"
            );

            $success_count = 0;
            $failed_count  = 0;
            foreach ($to_save as $student_id => $grade) {
                $stmt->bind_param("iisd", $student_id, $posted_subject_id, $posted_academic_year, $grade);
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $failed_count++;
                }
            }
            $stmt->close();

            if ($failed_count === 0) {
                $save_message = "$success_count grade(s) saved successfully.";
                $save_type    = 'success';
            } else {
                $save_message = "$success_count grade(s) saved, but $failed_count failed due to a database error. Please review and try saving again.";
                $save_type    = 'error';
            }
        }

        $academic_year = $posted_academic_year;
        $subject_id    = $posted_subject_id;
    } else {
        $save_message = 'No grades to save or missing session details (academic year or subject was not set).';
        $save_type    = 'error';
    }
}


$show_roster = ($subject_id && $academic_year);
$students    = [];
$course_mismatch = false;

if ($show_roster) {
    
    $subj_meta_stmt = $conn->prepare("SELECT course, year_level FROM subjects WHERE id = ?");
    $subj_meta_stmt->bind_param("i", $subject_id);
    $subj_meta_stmt->execute();
    $subject_meta_row = $subj_meta_stmt->get_result()->fetch_assoc();
    $subj_meta_stmt->close();

    $subject_course     = $subject_meta_row['course']     ?? null;
    $subject_year_level = $subject_meta_row['year_level'] ?? null;

    if ($subject_course === null) {
        
        $students = [];
    } elseif ($course !== '' && $course !== $subject_course) {
        $course_mismatch = true;
        $students = [];
    } else {
        $sql = "SELECT s.id, s.student_id, s.first_name, s.last_name, s.course, s.section, s.year_level, g.grade, g.updated_at
                FROM students s
                LEFT JOIN grades g
                    ON g.student_id = s.id
                    AND g.subject_id = ?
                    AND g.academic_year = ?
                WHERE s.course = ? AND s.year_level = ?
                ORDER BY s.last_name ASC, s.first_name ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $subject_id, $academic_year, $subject_course, $subject_year_level);
        $stmt->execute();
        $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$subject_name_display = '';
if ($subject_id) {
    $subj_stmt = $conn->prepare("SELECT subject_code, subject_name FROM subjects WHERE id = ?");
    $subj_stmt->bind_param("i", $subject_id);
    $subj_stmt->execute();
    if ($subj_row = $subj_stmt->get_result()->fetch_assoc()) {
        $subject_name_display = htmlspecialchars($subj_row['subject_code'] . ' — ' . $subj_row['subject_name']);
    }
    $subj_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS Portal — Encode Grades</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="navbar.js" defer></script>
    <script src="alert-toast.js" defer></script>
</head>
<body>

<?php if ($save_message): ?>
    <div class="alert-status alert-<?php echo $save_type; ?>" id="saveAlert">
        <i class="ti ti-<?php echo $save_type === 'success' ? 'circle-check' : 'alert-triangle'; ?>"></i>
        <span><?php echo htmlspecialchars($save_message); ?></span>
        <button type="button" class="alert-dismiss" aria-label="Dismiss" onclick="document.getElementById('saveAlert').remove();">
            <i class="ti ti-x"></i>
        </button>
    </div>
<?php endif; ?>

<nav class="navbar">
    <div class="navbar-left">
        <div class="navbar-logo">
            <div class="logo-icon"><i class="ti ti-school"></i></div>
            <span class="logo-text">SIS<span class="logo-accent">Portal</span></span>
        </div>
    </div>
    <button class="hamburger" id="navHamburger" aria-label="Toggle menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <div class="navbar-right navbar" id="navbarRight">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="encode_grades.php" class="nav-link" style="color:#F5C842;">Grades</a>
        <a href="logout.php" class="nav-btn-logout" id="logoutLink">Logout</a>
    </div>
</nav>

<main class="main-content">

    <div class="page-header">
        <h1>Grade Encoding</h1>
        <p>Select a class and subject to begin encoding grades.</p>
    </div>

    <form method="GET" action="encode_grades.php" class="filter-card" id="filterForm">
        <div class="filter-group">
            <span class="filter-label-top">Academic Year</span>
            <select name="academic_year" class="filter-pill" required>
                <option value="<?php echo htmlspecialchars($current_year); ?>" <?php echo $academic_year === $current_year ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($current_year); ?>
                </option>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label-top">Course / Program</span>
            <select name="course" class="filter-pill" id="courseSelect">
                <option value="">All Programs</option>
                <?php foreach ($all_courses as $c): ?>
                    <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $course === $c ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group" style="min-width:180px;">
            <span class="filter-label-top">Subject</span>
            <select name="subject_id" class="filter-pill" id="subjectSelect" required>
                <option value="">Select Subject</option>
                <?php foreach ($all_subjects as $subj): ?>
                    <option value="<?php echo $subj['id']; ?>"
                            data-course="<?php echo htmlspecialchars($subj['course']); ?>"
                            <?php echo $subject_id == $subj['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($subj['subject_code'] . ' — ' . $subj['subject_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
        </div>
        <button type="submit" class="btn-add" id="loadListBtn" style="margin-top:14px;height:42px;">
            <i class="ti ti-reload"></i> Load Class List
        </button>
    </form>

    <form method="POST" action="encode_grades.php" id="gradeForm">
        <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($academic_year); ?>">
        <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>">

        <?php if ($show_roster && count($students) > 0): ?>

            <div class="context-badge">
                <i class="ti ti-book"></i>
                <?php echo htmlspecialchars($subject_course); ?> &bull;
                <?php echo htmlspecialchars($subject_year_level); ?> &bull;
                <?php echo $subject_name_display; ?> &bull;
                <?php echo htmlspecialchars($academic_year); ?>
                &mdash; <strong><?php echo count($students); ?></strong> student(s)
            </div>

            <div class="table-card">
                <div class="table-card-header">
                    <h3 class="table-title">Class List</h3>
                    <span class="table-info-note"><i class="ti ti-info-circle"></i> Leave blank to skip &nbsp;|&nbsp; Valid range: 0–100</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:80px;">#</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Course &amp; Section</th>
                                <th style="width:130px;text-align:center;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $idx = 1; ?>
                            <?php foreach ($students as $s): ?>
                                <?php
                                    $is_invalid = array_key_exists($s['id'], $invalid_grades);
                                    $display_value = $is_invalid
                                        ? $invalid_grades[$s['id']]
                                        : (($s['grade'] !== '' && $s['grade'] !== null) ? $s['grade'] : '');
                                ?>
                                <tr class="<?php echo $is_invalid ? 'row-invalid' : ''; ?>">
                                    <td style="color:var(--text-muted);font-size:12px;"><?php echo $idx++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($s['student_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($s['course'] . ' — ' . $s['section']); ?></td>
                                    <td style="text-align:center;">
                                        <input type="number"
                                               name="grades[<?php echo $s['id']; ?>]"
                                               value="<?php echo htmlspecialchars($display_value); ?>"
                                               step="0.01" min="0" max="100"
                                               placeholder="—"
                                               data-original="<?php echo htmlspecialchars(($s['grade'] !== '' && $s['grade'] !== null) ? $s['grade'] : ''); ?>"
                                               class="grade-input<?php echo $is_invalid ? ' grade-input-invalid' : ''; ?>">
                                        <?php if ($is_invalid): ?>
                                            <span class="grade-error">Must be 0–100</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="save-row">
                    <span class="save-status" id="saveStatus"></span>
                    <button type="submit" name="save_grades" class="btn-add" id="saveBtn" style="font-size:14px;padding:12px 28px;">
                        <i class="ti ti-device-floppy"></i> Save Grades
                    </button>
                </div>
            </div>

        <?php elseif ($show_roster && $course_mismatch): ?>
            <div class="table-card">
                <div class="empty-state">
                    <i class="ti ti-alert-triangle"></i>
                    <p><strong><?php echo htmlspecialchars($subject_name_display); ?></strong> belongs to a different program than the one selected in the Course filter.</p>
                    <a href="encode_grades.php?subject_id=<?php echo urlencode($subject_id); ?>&academic_year=<?php echo urlencode($academic_year); ?>" class="empty-state-link">Clear the Course filter and try again</a>
                </div>
            </div>
        <?php elseif ($show_roster): ?>
            <div class="table-card">
                <div class="empty-state">
                    <i class="ti ti-users"></i>
                    <p>No students found for the selected filters.</p>
                    <a href="encode_grades.php" class="empty-state-link">Clear filters and start over</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-card">
                <div class="empty-state">
                    <i class="ti ti-filter"></i>
                    <p>Use the filters above and click <strong>"Load Class List"</strong> to begin encoding.</p>
                </div>
            </div>
        <?php endif; ?>
    </form>

</main>

<footer class="site-footer">
    <p>&copy; 2026 <strong>SIS Portal</strong> &nbsp;&middot;&nbsp; All rights reserved</p>
</footer>

<script>
(function () {
    var courseSelect  = document.getElementById('courseSelect');
    var subjectSelect = document.getElementById('subjectSelect');
    var subjectHint   = document.getElementById('subjectHint');
    if (!courseSelect || !subjectSelect) return;

    var allOptions = Array.prototype.slice.call(subjectSelect.options);

    function applyFilter() {
        var selectedCourse = courseSelect.value;
        var currentValue = subjectSelect.value;
        var stillValid = false;

        subjectSelect.innerHTML = '';
        allOptions.forEach(function (opt) {
            var matches = selectedCourse === '' || opt.value === '' || opt.dataset.course === selectedCourse;
            if (matches) {
                subjectSelect.appendChild(opt);
                if (opt.value === currentValue) stillValid = true;
            }
        });

        if (!stillValid) subjectSelect.value = '';
        subjectHint.hidden = (selectedCourse === '');
    }

    courseSelect.addEventListener('change', applyFilter);
    applyFilter(); 
})();


(function () {
    var filterForm = document.getElementById('filterForm');
    var loadBtn = document.getElementById('loadListBtn');
    if (!filterForm || !loadBtn) return;

    filterForm.addEventListener('submit', function () {
        loadBtn.disabled = true;
        loadBtn.innerHTML = '<i class="ti ti-loader-2 spin"></i> Loading…';
    });
})();


(function () {
    var gradeForm  = document.getElementById('gradeForm');
    var saveBtn    = document.getElementById('saveBtn');
    var saveStatus = document.getElementById('saveStatus');
    if (!gradeForm || !saveBtn) return;

    var inputs = Array.prototype.slice.call(gradeForm.querySelectorAll('.grade-input'));
    var submitting = false;

    function countChanges() {
        return inputs.filter(function (inp) {
            return inp.value.trim() !== (inp.dataset.original || '').trim();
        }).length;
    }

    function updateStatus() {
        var changed = countChanges();
        if (!saveStatus) return;
        saveStatus.textContent = changed > 0
            ? changed + ' grade(s) changed and not yet saved'
            : '';
        saveStatus.classList.toggle('save-status-pending', changed > 0);
    }

    inputs.forEach(function (inp) {
        inp.addEventListener('input', updateStatus);
    });
    updateStatus();

    gradeForm.addEventListener('submit', function (e) {
        if (submitting) {
            e.preventDefault();
            return;
        }

        var subjectSelectLive = document.getElementById('subjectSelect');
        var hiddenSubjectId = gradeForm.querySelector('input[name="subject_id"]');
        if (subjectSelectLive && hiddenSubjectId && subjectSelectLive.value !== hiddenSubjectId.value) {
            e.preventDefault();
            alert('The Subject filter has changed since this class list was loaded. Click "Load Class List" again before saving to avoid saving grades under the wrong subject.');
            return;
        }

        var changed = countChanges();
        if (changed === 0) {
            e.preventDefault();
            alert('No grades have been changed — nothing to save.');
            return;
        }

        var ok = window.confirm(
            'Save ' + changed + ' changed grade(s)? This will overwrite any existing saved grades for these students.'
        );
        if (!ok) {
            e.preventDefault();
            return;
        }

        submitting = true;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="ti ti-loader-2 spin"></i> Saving…';
    });
})();

(function () {
    var logoutLink = document.getElementById('logoutLink');
    if (!logoutLink) return;

    logoutLink.addEventListener('click', function (e) {
        var gradeForm = document.getElementById('gradeForm');
        var hasUnsaved = gradeForm && Array.prototype.slice.call(gradeForm.querySelectorAll('.grade-input')).some(function (inp) {
            return inp.value.trim() !== (inp.dataset.original || '').trim();
        });

        var message = hasUnsaved
            ? 'You have unsaved grade changes. Log out anyway?'
            : 'Log out of SIS Portal?';

        if (!window.confirm(message)) {
            e.preventDefault();
        }
    });
})();
</script>

</body>
</html>
