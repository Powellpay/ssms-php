<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Student Profile View
//  Tables: students, enrollments, streams, class_levels,
//          subject_term_results, subjects, terms, guardians,
//          attendance, generic_skill_ratings, generic_skills
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../../config/database.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$student = $db->prepare(
    "SELECT s.*, u.username,
            cl.level_name, st.stream_name, ay.year_name, t.term_name
     FROM   students s
     LEFT JOIN users u        ON u.id = s.user_id
     LEFT JOIN enrollments e  ON e.student_id = s.id
       AND e.academic_year_id = (SELECT id FROM academic_years WHERE is_current=1 LIMIT 1)
     LEFT JOIN streams st     ON st.id = e.stream_id
     LEFT JOIN class_levels cl ON cl.id = st.class_level_id
     LEFT JOIN academic_years ay ON ay.id = e.academic_year_id
     LEFT JOIN terms t        ON t.is_current = 1
     WHERE s.id = ?  LIMIT 1"
);
$student->execute([$id]);
$s = $student->fetch();
if (!$s) { echo '<p>Student not found.</p>'; exit; }

$page_title = $s['first_name'] . ' ' . $s['last_name'];
require_once __DIR__ . '/../../includes/header.php';

// Guardians
$guardians = $db->prepare(
    "SELECT g.*, sg.is_primary_contact
     FROM   guardians g
     JOIN   student_guardians sg ON sg.guardian_id = g.id
     WHERE  sg.student_id = ?
     ORDER  BY sg.is_primary_contact DESC"
);
$guardians->execute([$id]);
$guardians = $guardians->fetchAll();

// Term results (current term)
$results = $db->prepare(
    "SELECT sub.subject_name, r.ca_score, r.eot_score, r.final_score, r.grade
     FROM   subject_term_results r
     JOIN   subjects sub ON sub.id = r.subject_id
     JOIN   terms t ON t.id = r.term_id AND t.is_current = 1
     WHERE  r.student_id = ?
     ORDER  BY sub.subject_name"
);
$results->execute([$id]);
$results = $results->fetchAll();

// Attendance summary (current term)
$att = $db->prepare(
    "SELECT
        SUM(status = 'Present') AS present,
        SUM(status = 'Absent')  AS absent,
        SUM(status = 'Late')    AS late,
        COUNT(*)                AS total
     FROM attendance a
     JOIN terms t ON t.id = a.term_id AND t.is_current = 1
     WHERE a.student_id = ?"
);
$att->execute([$id]);
$att = $att->fetch();

// Generic skill ratings
$skills = $db->prepare(
    "SELECT gs.skill_name, srs.level_name
     FROM   generic_skill_ratings gsr
     JOIN   generic_skills gs      ON gs.id = gsr.skill_id
     JOIN   skill_rating_scale srs ON srs.id = gsr.rating_scale_id
     JOIN   terms t ON t.id = gsr.term_id AND t.is_current = 1
     WHERE  gsr.student_id = ?"
);
$skills->execute([$id]);
$skills = $skills->fetchAll();

$grade_class = ['A'=>'grade-A','B'=>'grade-B','C'=>'grade-C','D'=>'grade-D','E'=>'grade-E'];
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></h1>
        <div class="breadcrumb">
            <a href="/modules/students/index.php">Students</a> ›
            <span class="mono"><?= htmlspecialchars($s['admission_no']) ?></span>
        </div>
    </div>
    <div class="flex gap-2">
        <?php if (hasRole(['Admin','Head Teacher'])): ?>
        <a href="edit.php?id=<?= $id ?>" class="btn btn-secondary">Edit</a>
        <?php endif; ?>
        <a href="/modules/reports/student_report.php?id=<?= $id ?>" class="btn btn-primary">📄 Report Card</a>
    </div>
</div>

<?php if (!empty($_GET['added'])): ?>
<div class="alert alert-success">✅ Student added successfully!</div>
<?php endif; ?>

<!-- Bio + Stream -->
<div class="flex gap-3" style="align-items:flex-start;flex-wrap:wrap;">

    <div class="card" style="flex:1;min-width:280px;">
        <div class="card-header"><h2>Personal Details</h2></div>
        <div class="card-body">
            <table style="width:100%;font-size:14px;">
                <tr><td class="text-muted" style="width:42%;padding:7px 0;">Admission No.</td><td class="mono"><strong><?= htmlspecialchars($s['admission_no']) ?></strong></td></tr>
                <tr><td class="text-muted" style="padding:7px 0;">Gender</td><td><?= htmlspecialchars($s['gender']) ?></td></tr>
                <tr><td class="text-muted" style="padding:7px 0;">Date of Birth</td><td><?= $s['dob'] ? date('d M Y', strtotime($s['dob'])) : '—' ?></td></tr>
                <tr><td class="text-muted" style="padding:7px 0;">Religion</td><td><?= htmlspecialchars($s['religion'] ?? '—') ?></td></tr>
                <tr><td class="text-muted" style="padding:7px 0;">Address</td><td><?= htmlspecialchars($s['address'] ?? '—') ?></td></tr>
                <tr><td class="text-muted" style="padding:7px 0;">Admission Date</td><td><?= date('d M Y', strtotime($s['admission_date'])) ?></td></tr>
                <tr><td class="text-muted" style="padding:7px 0;">Status</td>
                    <td><span class="badge badge-<?= $s['status'] === 'active' ? 'active' : 'inactive' ?>"><?= ucfirst($s['status']) ?></span></td></tr>
                <tr><td class="text-muted" style="padding:7px 0;">Login</td><td class="mono"><?= htmlspecialchars($s['username'] ?? '—') ?></td></tr>
            </table>
        </div>
    </div>

    <div style="flex:1;min-width:240px;display:flex;flex-direction:column;gap:16px;">

        <!-- Current stream -->
        <div class="card">
            <div class="card-header"><h2>📚 Current Enrolment</h2></div>
            <div class="card-body">
                <?php if ($s['level_name']): ?>
                <div style="font-size:28px;font-weight:800;color:var(--teal);">
                    <?= htmlspecialchars($s['level_name'] . ' ' . $s['stream_name']) ?>
                </div>
                <div class="text-muted" style="font-size:13px;">
                    <?= htmlspecialchars($s['year_name']) ?> · <?= htmlspecialchars($s['term_name'] ?? '') ?>
                </div>
                <?php else: ?>
                <p class="text-muted">Not enrolled this year.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attendance summary -->
        <div class="card">
            <div class="card-header"><h2>📋 Attendance (This Term)</h2></div>
            <div class="card-body">
                <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:0;">
                    <div class="stat-card" style="padding:12px;margin:0;border-color:#059669;">
                        <span class="stat-label">Present</span>
                        <span class="stat-value" style="font-size:24px;color:#059669;"><?= (int)($att['present']??0) ?></span>
                    </div>
                    <div class="stat-card" style="padding:12px;margin:0;border-color:#DC2626;">
                        <span class="stat-label">Absent</span>
                        <span class="stat-value" style="font-size:24px;color:#DC2626;"><?= (int)($att['absent']??0) ?></span>
                    </div>
                    <div class="stat-card" style="padding:12px;margin:0;border-color:#D97706;">
                        <span class="stat-label">Late</span>
                        <span class="stat-value" style="font-size:24px;color:#D97706;"><?= (int)($att['late']??0) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Guardians -->
<?php if ($guardians): ?>
<div class="card">
    <div class="card-header"><h2>👨‍👩‍👧 Guardians</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Relationship</th><th>Phone</th><th>Email</th><th>Primary?</th></tr></thead>
            <tbody>
            <?php foreach ($guardians as $g): ?>
            <tr>
                <td><strong><?= htmlspecialchars($g['full_name']) ?></strong></td>
                <td><?= htmlspecialchars($g['relationship'] ?? '—') ?></td>
                <td class="mono"><?= htmlspecialchars($g['phone']) ?></td>
                <td><?= htmlspecialchars($g['email'] ?? '—') ?></td>
                <td><?= $g['is_primary_contact'] ? '✅' : '' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Results (current term) -->
<div class="card">
    <div class="card-header">
        <h2>📊 Subject Results — Current Term</h2>
        <?php if ($results): ?>
        <a href="/modules/reports/student_report.php?id=<?= $id ?>" class="btn btn-secondary btn-sm">Full Report Card</a>
        <?php endif; ?>
    </div>
    <?php if (empty($results)): ?>
    <div class="empty-state"><div class="empty-icon">📊</div><h3>No results recorded yet</h3></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Subject</th><th>CA (20)</th><th>EOT (80)</th><th>Final (100)</th><th>Grade</th></tr></thead>
            <tbody>
            <?php foreach ($results as $r): ?>
            <tr>
                <td><strong><?= htmlspecialchars($r['subject_name']) ?></strong></td>
                <td><?= $r['ca_score'] !== null  ? number_format($r['ca_score'], 1)  : '—' ?></td>
                <td><?= $r['eot_score'] !== null ? number_format($r['eot_score'], 1) : '—' ?></td>
                <td><?= $r['final_score'] !== null ? number_format($r['final_score'], 1) : '—' ?></td>
                <td><span class="badge badge-grade-<?= htmlspecialchars($r['grade'] ?? 'E') ?>"><?= htmlspecialchars($r['grade'] ?? '—') ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Generic Skills -->
<?php if ($skills): ?>
<div class="card">
    <div class="card-header"><h2>⭐ Generic Skills — Current Term</h2></div>
    <div class="card-body">
        <div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr));">
            <?php foreach ($skills as $sk): ?>
            <div class="stat-card">
                <span class="stat-label"><?= htmlspecialchars($sk['skill_name']) ?></span>
                <span class="stat-value" style="font-size:18px;color:var(--teal);"><?= htmlspecialchars($sk['level_name']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
