=== BUG / ISSUE LOG ===
Last updated: 2026-05-29

--- SECURITY ---

1. [edit.php, L16-24] SQL Injection - UPDATE query uses raw $_POST values.
   No mysqli_real_escape_string() on student_id, first_name, last_name, email,
   course, section, or year. TODO on L14 acknowledges this.

2. [index.php, L89-100] XSS - Student data (name, email, course, section, etc.)
   echoed directly without htmlspecialchars(). If malicious data enters the DB,
   it executes as HTML in the browser. Same issue in edit.php L44-65.

--- LOGIC ---

3. [index.php, L125-140] Record counting logic is fragile. When total_records
   is an exact multiple of limit, $limit * $page can show a number larger than
   total_records. The formula should be simplified to:
     min($page * $limit, $total_records)

4. [index.php, L24] Pagination limit is hardcoded to 5. No way for the user to
   change how many records they see per page.

--- HTML / STRUCTURE ---

5. [index.php, L56-58] .alert-status is placed outside <body> (between </head>
   and <body>). Invalid HTML — should be moved inside <body>.

6. [style.css, L100-104] td.sec uses display: flex on a table cell. This breaks
   table layout in some browsers and can cause column alignment issues.

7. [style.css, L77-79] .btn-save:hover has no matching element — all buttons
   now use .default-btn.

--- COSMETIC / MINOR ---

8. [edit.php, L43] TODO to make student_id not required on edit — user might
   want to edit other fields without changing the ID.

9. [style.css, L36-37] .form-container has justify-content: center on a column
   flex layout — has no visible effect since the content fills the container.