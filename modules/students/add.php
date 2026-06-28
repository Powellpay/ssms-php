<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Add New Student
//  Inserts into: students, enrollments
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../../config/database.php';
$page_title = 'Add Student';
require_once __DIR__ . '/../../includes/header.php';

if (!hasRole(['Admin', 'Head Teacher'])) {
    echo '<div class="alert alert-error">⚠ You do not have permission to add students.</div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$db = db();
$errors = [];
$success = '';

// Dropdown data
$streams = $db->query(
    "SELECT st.id, CONCAT(cl.level_name, ' ', st.stream_name) AS label
     FROM   streams st
     JOIN   class_levels cl ON cl.id = st.class_level_id
     WHERE  st.academic_year_id = (SELECT id FROM academic_years WHERE is_current=1 LIMIT 1)
     ORDER  BY cl.sort_order, st.stream_name"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f = array_map('trim', $_POST);

    // Validate
    if (empty($f['first_name']))    $errors[] = 'First name is required.';
    if (empty($f['last_name']))     $errors[] = 'Last name is required.';
    if (empty($f['admission_no']))  $errors[] = 'Admission number is required.';
    if (empty($f['gender']))        $errors[] = 'Gender is required.';
    if (empty($f['admission_date']))$errors[] = 'Admission date is required.';

    // Duplicate admission no check
    if (empty($errors)) {
        $dup = $db->prepare("SELECT id FROM students WHERE admission_no = ?");
        $dup->execute([$f['admission_no']]);
        if ($dup->fetch()) $errors[] = 'Admission number already exists.';
    }

    if (empty($errors)) {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                "INSERT INTO students
                    (admission_no, first_name, last_name, gender, dob, religion, address, admission_date, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')"
            );
            $stmt->execute([
                $f['admission_no'],
                $f['first_name'],
                $f['last_name'],
                $f['gender'],
                $f['dob'] ?: null,
                $f['religion'] ?: null,
                $f['address'] ?: null,
                $f['admission_date'],
            ]);
            $student_id = (int)$db->lastInsertId();

            // Enrol in selected stream (optional)
            if (!empty($f['stream_id'])) {
                $yr = $db->query("SELECT id FROM academic_years WHERE is_current=1 LIMIT 1")->fetchColumn();
                $db->prepare(
                    "INSERT INTO enrollments (student_id, stream_id, academic_year_id, enrollment_date)
                     VALUES (?, ?, ?, CURDATE())"
                )->execute([$student_id, $f['stream_id'], $yr]);
            }

            $db->commit();
            header("Location: /modules/students/view.php?id=$student_id&added=1");
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<div class="page-header">
    <div>
        <h1>Add New Student</h1>
        <div class="breadcrumb"><a href="/modules/students/index.php">Students</a> › Add</div>
    </div>
</div>

<?php foreach ($errors as $e): ?>
<div class="alert alert-error">⚠ <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<form method="POST">
    <div class="card">
        <div class="card-header"><h2>Personal Information</h2></div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>Admission Number *</label>
                    <input class="form-control" type="text" name="admission_no"
                           placeholder="e.g. 2026/S1/001"
                           value="<?= htmlspecialchars($_POST['admission_no'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>First Name *</label>
                    <input class="form-control" type="text" name="first_name"
                           value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name *</label>
                    <input class="form-control" type="text" name="last_name"
                           value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender *</label>
                    <select class="form-control" name="gender" required>
                        <option value="">Select…</option>
                        <option value="Male"   <?= ($_POST['gender'] ?? '') === 'Male'   ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($_POST['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input class="form-control" type="date" name="dob"
                           value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Admission Date *</label>
                    <input class="form-control" type="date" name="admission_date"
                           value="<?= htmlspecialchars($_POST['admission_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group">
                    <label>Religion</label>
                    <input class="form-control" type="text" name="religion"
                           placeholder="e.g. Catholic, Muslim"
                           value="<?= htmlspecialchars($_POST['religion'] ?? '') ?>">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Address</label>
                    <input class="form-control" type="text" name="address"
                           placeholder="e.g. Kampala, Nakawa"
                           value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Class Enrolment (Current Year)</h2></div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>Stream</label>
                    <select class="form-control" name="stream_id">
                        <option value="">— Skip enrolment for now —</option>
                        <?php foreach ($streams as $st): ?>
                        <option value="<?= $st['id'] ?>"
                            <?= ($_POST['stream_id'] ?? '') == $st['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['label']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-3 mt-4">
        <button type="submit" class="btn btn-primary">Save Student</button>
        <a href="/modules/students/index.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
