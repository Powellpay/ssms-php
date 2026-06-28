<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

require_login();

$pdo = get_db();

$students = $pdo->query(
    "SELECT id, admission_no, first_name, last_name FROM students WHERE status='active' ORDER BY last_name"
)->fetchAll();

$terms = $pdo->query(
    "SELECT t.id, t.term_name, ay.year_name
     FROM terms t JOIN academic_years ay ON ay.id = t.academic_year_id
     ORDER BY ay.year_name DESC, t.id DESC"
)->fetchAll();

$page_title = 'New Curriculum Reports';
require __DIR__ . '/../../includes/header.php';
?>

<h1>New Curriculum Report - Select Learner</h1>

<div class="card" style="max-width:480px;">
    <form method="get" action="/modules/reports/student_report.php">
        <label for="student_id">Learner</label>
        <select id="student_id" name="student_id" required>
            <option value="">-- Select learner --</option>
            <?php foreach ($students as $s): ?>
                <option value="<?= (int)$s['id'] ?>">
                    <?= htmlspecialchars($s['admission_no'] . ' - ' . $s['first_name'] . ' ' . $s['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="term_id">Term</label>
        <select id="term_id" name="term_id" required>
            <option value="">-- Select term --</option>
            <?php foreach ($terms as $t): ?>
                <option value="<?= (int)$t['id'] ?>">
                    <?= htmlspecialchars($t['term_name'] . ', ' . $t['year_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">View Report</button>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
