<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Dashboard
//  Pulls live counts from: students, staff, enrollments, invoices,
//  payments, announcements, academic_years, terms
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../../config/database.php';
$page_title = 'Dashboard';
require_once __DIR__ . '/../../includes/header.php';

$db = db();

// ── Live stats ────────────────────────────────────────────────────────────────
$stats = [
    'students'  => $db->query("SELECT COUNT(*) FROM students  WHERE status = 'active'")->fetchColumn(),
    'staff'     => $db->query("SELECT COUNT(*) FROM staff     WHERE status = 'active'")->fetchColumn(),
    'streams'   => $db->query("SELECT COUNT(*) FROM streams   WHERE academic_year_id = (SELECT id FROM academic_years WHERE is_current=1 LIMIT 1)")->fetchColumn(),
    'balance'   => $db->query("SELECT COALESCE(SUM(i.amount_due),0) - COALESCE((SELECT SUM(amount_paid) FROM payments),0) FROM invoices i")->fetchColumn(),
];

// ── Current term ──────────────────────────────────────────────────────────────
$term = $db->query(
    "SELECT t.term_name, ay.year_name
     FROM   terms t
     JOIN   academic_years ay ON ay.id = t.academic_year_id
     WHERE  t.is_current = 1
     LIMIT  1"
)->fetch();

// ── Recent students (last 5 admitted) ────────────────────────────────────────
$recent_students = $db->query(
    "SELECT s.admission_no, s.first_name, s.last_name,
            cl.level_name, st.stream_name, s.admission_date
     FROM   students s
     LEFT JOIN enrollments e   ON e.student_id = s.id
       AND e.academic_year_id = (SELECT id FROM academic_years WHERE is_current=1 LIMIT 1)
     LEFT JOIN streams st      ON st.id = e.stream_id
     LEFT JOIN class_levels cl ON cl.id = st.class_level_id
     ORDER BY s.id DESC
     LIMIT 5"
)->fetchAll();

// ── Announcements (active, visible to this role) ──────────────────────────────
$role = currentRole();
$announcements = $db->query(
    "SELECT a.title, a.message, a.created_at, r.role_name
     FROM   announcements a
     LEFT   JOIN roles r ON r.id = a.role_id
     WHERE  (a.role_id IS NULL OR r.role_name = " . $db->quote($role) . ")
       AND  (a.expires_at IS NULL OR a.expires_at > NOW())
     ORDER  BY a.created_at DESC
     LIMIT  4"
)->fetchAll();

// ── Attendance today ─────────────────────────────────────────────────────────
$today_att = $db->query(
    "SELECT
        SUM(status = 'Present') AS present,
        SUM(status = 'Absent')  AS absent
     FROM attendance
     WHERE att_date = CURDATE()"
)->fetch();
?>

<!-- ── Page Header ──────────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <div class="breadcrumb">
            <?php if ($term): ?>
                📅 <?= htmlspecialchars($term['term_name']) ?> — <?= htmlspecialchars($term['year_name']) ?>
            <?php else: ?>
                📅 No active term set
            <?php endif; ?>
        </div>
    </div>
    <?php if (hasRole(['Admin','Head Teacher'])): ?>
    <div class="flex gap-2">
        <a href="/modules/students/add.php" class="btn btn-primary">+ Add Student</a>
    </div>
    <?php endif; ?>
</div>

<!-- ── Stat Cards ───────────────────────────────────────────────────── -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Active Students</span>
        <span class="stat-value"><?= number_format($stats['students']) ?></span>
        <span class="stat-sub">Enrolled this year</span>
    </div>
    <div class="stat-card seafoam">
        <span class="stat-label">Active Staff</span>
        <span class="stat-value"><?= number_format($stats['staff']) ?></span>
        <span class="stat-sub">Teaching &amp; non-teaching</span>
    </div>
    <div class="stat-card mint">
        <span class="stat-label">Streams</span>
        <span class="stat-value"><?= number_format($stats['streams']) ?></span>
        <span class="stat-sub">Class sections this year</span>
    </div>
    <div class="stat-card dark">
        <span class="stat-label">Outstanding Fees</span>
        <span class="stat-value" style="font-size:22px">UGX <?= number_format((float)$stats['balance']) ?></span>
        <span class="stat-sub">Total unpaid balance</span>
    </div>
</div>

<!-- ── Row: recent students + announcements ───────────────────────── -->
<div class="flex gap-3" style="align-items:flex-start;flex-wrap:wrap;">

    <!-- Recent Students -->
    <div class="card" style="flex:2;min-width:320px;">
        <div class="card-header">
            <h2>🎓 Recently Admitted Students</h2>
            <a href="/modules/students/index.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            <?php if (empty($recent_students)): ?>
            <div class="empty-state"><div class="empty-icon">🎓</div><h3>No students yet</h3><p>Add your first student to get started.</p></div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Adm No.</th>
                        <th>Name</th>
                        <th>Stream</th>
                        <th>Admitted</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_students as $s): ?>
                <tr>
                    <td class="mono"><?= htmlspecialchars($s['admission_no']) ?></td>
                    <td><strong><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></strong></td>
                    <td>
                        <?php if ($s['level_name']): ?>
                        <span class="badge badge-teal"><?= htmlspecialchars($s['level_name'] . ' ' . $s['stream_name']) ?></span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted"><?= date('d M Y', strtotime($s['admission_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right column -->
    <div style="flex:1;min-width:260px;display:flex;flex-direction:column;gap:16px;">

        <!-- Today's Attendance -->
        <div class="card">
            <div class="card-header"><h2>📋 Attendance Today</h2></div>
            <div class="card-body">
                <div class="stats-grid" style="grid-template-columns:1fr 1fr;gap:12px;margin-bottom:0;">
                    <div class="stat-card" style="padding:14px;margin:0;">
                        <span class="stat-label">Present</span>
                        <span class="stat-value" style="color:#059669;"><?= (int)($today_att['present'] ?? 0) ?></span>
                    </div>
                    <div class="stat-card" style="padding:14px;margin:0;border-color:#DC2626;">
                        <span class="stat-label">Absent</span>
                        <span class="stat-value" style="color:#DC2626;"><?= (int)($today_att['absent'] ?? 0) ?></span>
                    </div>
                </div>
                <?php if (!($today_att['present'] ?? 0) && !($today_att['absent'] ?? 0)): ?>
                <p class="text-muted mt-4" style="font-size:13px;">Attendance not marked yet today.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Announcements -->
        <div class="card">
            <div class="card-header"><h2>📢 Announcements</h2></div>
            <div class="card-body" style="padding:14px;">
                <?php if (empty($announcements)): ?>
                <p class="text-muted" style="font-size:13px;">No active announcements.</p>
                <?php else: ?>
                <?php foreach ($announcements as $a): ?>
                <div style="padding:10px 0;border-bottom:1px solid var(--border);">
                    <div style="font-weight:600;font-size:13px;color:var(--dark);"><?= htmlspecialchars($a['title']) ?></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                        <?= $a['role_name'] ? '🔒 ' . htmlspecialchars($a['role_name']) . ' only' : '🌐 Everyone' ?>
                        · <?= date('d M', strtotime($a['created_at'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
