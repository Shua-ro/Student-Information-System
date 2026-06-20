<?php

session_start();

if (!isset($_SESSION['authenticated'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

include 'config.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    
    $sql = "SELECT id, student_id, first_name, last_name, course, section, year_level
            FROM students
            ORDER BY id DESC
            LIMIT 6";
} else {
    $like = '%' . mysqli_real_escape_string($conn, $q) . '%';
    $sql = "SELECT id, student_id, first_name, last_name, course, section, year_level
            FROM students
            WHERE student_id LIKE '$like'
               OR first_name LIKE '$like'
               OR last_name LIKE '$like'
               OR CONCAT(first_name, ' ', last_name) LIKE '$like'
            ORDER BY last_name ASC
            LIMIT 8";
}

$result = mysqli_query($conn, $sql);

$students = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

echo json_encode($students);
