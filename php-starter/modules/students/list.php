<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

require_login();

$pdo = get_db();

$students = $pdo->query(
    "SELECT s.id, s.admission_no, s.first_name, s.last_name, s.gender,
            cl.level_name, st.stream_name
     FROM students s
     LEFT JOIN enrollments e ON e.student_id = s.id
        AND e.academic_year_id = (SELECT id FROM academic_years WHERE is_current = 1 LIMIT 1)
     LEFT JOIN streams st ON st.id = e.stream_id
     LEFT JOIN class_levels cl ON cl.id = st.class_level_id
     WHERE s.status = 'active'
     ORDER BY cl.numeric_level, st.stream_name, s.last_name"
)->fetchAll();

$page_title = 'Students';
require __DIR__ . '/../../includes/header.php';
?>

<h1>Students</h1>

<p><a class="btn" href="/modules/students/add.php">+ Add Student</a></p>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Admission No.</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Class</th>
                <th>Stream</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $stu): ?>
                <tr>
                    <td><?= htmlspecialchars($stu['admission_no']) ?></td>
                    <td><?= htmlspecialchars($stu['first_name'] . ' ' . $stu['last_name']) ?></td>
                    <td><?= htmlspecialchars($stu['gender']) ?></td>
                    <td><?= htmlspecialchars($stu['level_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($stu['stream_name'] ?? '-') ?></td>
                    <td><a href="/modules/students/view.php?id=<?= (int)$stu['id'] ?>">View</a></td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$students): ?>
                <tr><td colspan="6">No students found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
