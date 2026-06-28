<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Students List
//  Tables: students, enrollments, streams, class_levels, academic_years
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../../config/database.php';
$page_title = 'Students';
require_once __DIR__ . '/../../includes/header.php';

$db = db();

// ── Filters ────────────────────────────────────────────────────────────────────
$search     = trim($_GET['q']       ?? '');
$level_id   = (int)($_GET['level']  ?? 0);
$status_f   = $_GET['status']       ?? 'active';
$page       = max(1, (int)($_GET['page'] ?? 1));
$per_page   = 20;

// ── Class levels for filter dropdown ──────────────────────────────────────────
$levels = $db->query("SELECT id, level_name FROM class_levels ORDER BY sort_order")->fetchAll();

// ── Build query ───────────────────────────────────────────────────────────────
$where  = ['1=1'];
$params = [];

if ($search !== '') {
    $where[]  = "(s.first_name LIKE ? OR s.last_name LIKE ? OR s.admission_no LIKE ?)";
    $like     = "%$search%";
    $params   = array_merge($params, [$like, $like, $like]);
}
if ($level_id) {
    $where[]  = "cl.id = ?";
    $params[] = $level_id;
}
if ($status_f !== '') {
    $where[]  = "s.status = ?";
    $params[] = $status_f;
}

$where_sql = implode(' AND ', $where);

$total = $db->prepare(
    "SELECT COUNT(*)
     FROM   students s
     LEFT JOIN enrollments e   ON e.student_id = s.id
       AND e.academic_year_id = (SELECT id FROM academic_years WHERE is_current=1 LIMIT 1)
     LEFT JOIN streams st      ON st.id = e.stream_id
     LEFT JOIN class_levels cl ON cl.id = st.class_level_id
     WHERE  $where_sql"
);
$total->execute($params);
$total_rows = (int)$total->fetchColumn();
$total_pages = max(1, ceil($total_rows / $per_page));
$offset = ($page - 1) * $per_page;

$stmt = $db->prepare(
    "SELECT s.id, s.admission_no, s.first_name, s.last_name,
            s.gender, s.status, s.admission_date,
            cl.level_name, st.stream_name
     FROM   students s
     LEFT JOIN enrollments e   ON e.student_id = s.id
       AND e.academic_year_id = (SELECT id FROM academic_years WHERE is_current=1 LIMIT 1)
     LEFT JOIN streams st      ON st.id = e.stream_id
     LEFT JOIN class_levels cl ON cl.id = st.class_level_id
     WHERE  $where_sql
     ORDER BY s.last_name, s.first_name
     LIMIT $per_page OFFSET $offset"
);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<div class="page-header">
    <div>
        <h1>Students</h1>
        <div class="breadcrumb">Showing <?= number_format($total_rows) ?> learner<?= $total_rows !== 1 ? 's' : '' ?></div>
    </div>
    <?php if (hasRole(['Admin','Head Teacher'])): ?>
    <a href="/modules/students/add.php" class="btn btn-primary">+ Add Student</a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" class="flex gap-3 items-center" style="flex-wrap:wrap;">
            <div class="search-bar" style="flex:2;min-width:200px;">
                <span class="search-icon">🔍</span>
                <input class="form-control" type="text" name="q"
                       placeholder="Search name or admission no."
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            <select class="form-control" name="level" style="width:160px;">
                <option value="">All Levels</option>
                <?php foreach ($levels as $l): ?>
                <option value="<?= $l['id'] ?>" <?= $level_id == $l['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($l['level_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select class="form-control" name="status" style="width:140px;">
                <option value="active"       <?= $status_f === 'active'       ? 'selected' : '' ?>>Active</option>
                <option value=""             <?= $status_f === ''              ? 'selected' : '' ?>>All Statuses</option>
                <option value="transferred"  <?= $status_f === 'transferred'   ? 'selected' : '' ?>>Transferred</option>
                <option value="graduated"    <?= $status_f === 'graduated'     ? 'selected' : '' ?>>Graduated</option>
                <option value="dropped"      <?= $status_f === 'dropped'       ? 'selected' : '' ?>>Dropped</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="/modules/students/index.php" class="btn btn-secondary">Clear</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($students)): ?>
        <div class="empty-state">
            <div class="empty-icon">🎓</div>
            <h3>No students found</h3>
            <p><?= $search ? 'Try a different search term.' : 'Add your first student to get started.' ?></p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Adm No.</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Stream</th>
                    <th>Status</th>
                    <th>Admitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $s): ?>
            <tr>
                <td class="mono"><?= htmlspecialchars($s['admission_no']) ?></td>
                <td><strong><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></strong></td>
                <td><?= htmlspecialchars($s['gender']) ?></td>
                <td>
                    <?php if ($s['level_name']): ?>
                    <span class="badge badge-teal"><?= htmlspecialchars($s['level_name'] . ' ' . $s['stream_name']) ?></span>
                    <?php else: ?>
                    <span class="text-muted">Not enrolled</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge badge-<?= $s['status'] === 'active' ? 'active' : 'inactive' ?>">
                        <?= ucfirst(htmlspecialchars($s['status'])) ?>
                    </span>
                </td>
                <td class="text-muted"><?= date('d M Y', strtotime($s['admission_date'])) ?></td>
                <td>
                    <a href="/modules/students/view.php?id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                    <?php if (hasRole(['Admin','Head Teacher'])): ?>
                    <a href="/modules/students/edit.php?id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="card-body flex gap-2 items-center" style="border-top:1px solid var(--border);padding:14px 22px;">
        <?php if ($page > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page-1])) ?>" class="btn btn-secondary btn-sm">← Prev</a>
        <?php endif; ?>
        <span class="text-muted" style="font-size:13px;">Page <?= $page ?> of <?= $total_pages ?></span>
        <?php if ($page < $total_pages): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page+1])) ?>" class="btn btn-secondary btn-sm">Next →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
