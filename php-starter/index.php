<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$pdo = get_db();

$counts = [
    'students' => $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn(),
    'staff'    => $pdo->query("SELECT COUNT(*) FROM staff WHERE status = 'active'")->fetchColumn(),
    'streams'  => $pdo->query("SELECT COUNT(*) FROM streams")->fetchColumn(),
    'subjects' => $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn(),
];

$current_term = $pdo->query(
    "SELECT t.term_name, ay.year_name
     FROM terms t JOIN academic_years ay ON ay.id = t.academic_year_id
     WHERE t.is_current = 1 LIMIT 1"
)->fetch();

$page_title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<h1>Welcome, <?= htmlspecialchars(current_user()['username']) ?></h1>

<?php if ($current_term): ?>
    <p>Current period: <strong><?= htmlspecialchars($current_term['term_name']) ?>, <?= htmlspecialchars($current_term['year_name']) ?></strong></p>
<?php endif; ?>

<div class="card" style="display:flex;gap:1.5rem;flex-wrap:wrap;">
    <div>
        <h3><?= (int)$counts['students'] ?></h3>
        <p>Active Students</p>
    </div>
    <div>
        <h3><?= (int)$counts['staff'] ?></h3>
        <p>Active Staff</p>
    </div>
    <div>
        <h3><?= (int)$counts['streams'] ?></h3>
        <p>Streams</p>
    </div>
    <div>
        <h3><?= (int)$counts['subjects'] ?></h3>
        <p>Subjects</p>
    </div>
</div>

<div class="card">
    <h3>Quick links</h3>
    <p>
        <a class="btn" href="/modules/students/list.php">Manage Students</a>
        &nbsp;
        <a class="btn" href="/modules/reports/">New Curriculum Reports</a>
    </p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
