<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

require_login();

$pdo = get_db();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT s.*, cl.level_name, st.stream_name
     FROM students s
     LEFT JOIN enrollments e ON e.student_id = s.id
        AND e.academic_year_id = (SELECT id FROM academic_years WHERE is_current = 1 LIMIT 1)
     LEFT JOIN streams st ON st.id = e.stream_id
     LEFT JOIN class_levels cl ON cl.id = st.class_level_id
     WHERE s.id = :id"
);
$stmt->execute(['id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    http_response_code(404);
    die('Student not found.');
}

$guardians = $pdo->prepare(
    "SELECT g.first_name, g.last_name, g.relationship, g.phone
     FROM guardians g
     JOIN student_guardians sg ON sg.guardian_id = g.id
     WHERE sg.student_id = :id"
);
$guardians->execute(['id' => $id]);
$guardians = $guardians->fetchAll();

$page_title = 'Student Profile';
require __DIR__ . '/../../includes/header.php';
?>

<h1><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h1>

<div class="card">
    <p><strong>Admission No:</strong> <?= htmlspecialchars($student['admission_no']) ?></p>
    <p><strong>Class:</strong> <?= htmlspecialchars(($student['level_name'] ?? '-') . ' ' . ($student['stream_name'] ?? '')) ?></p>
    <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>
    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['dob'] ?? '-') ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($student['status']) ?></p>
</div>

<div class="card">
    <h3>Guardians</h3>
    <?php if ($guardians): ?>
        <ul>
            <?php foreach ($guardians as $g): ?>
                <li>
                    <?= htmlspecialchars($g['first_name'] . ' ' . $g['last_name']) ?>
                    (<?= htmlspecialchars($g['relationship']) ?>) - <?= htmlspecialchars($g['phone']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No guardian records.</p>
    <?php endif; ?>
</div>

<p>
    <a class="btn" href="/modules/reports/student_report.php?student_id=<?= (int)$student['id'] ?>">
        View New Curriculum Report
    </a>
</p>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
