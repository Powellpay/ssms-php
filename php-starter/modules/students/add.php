<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

require_login();

$pdo = get_db();
$error = '';
$success = '';

// Streams for the current academic year, used to enrol the new student
$streams = $pdo->query(
    "SELECT st.id, cl.level_name, st.stream_name
     FROM streams st
     JOIN class_levels cl ON cl.id = st.class_level_id
     JOIN academic_years ay ON ay.id = st.academic_year_id
     WHERE ay.is_current = 1
     ORDER BY cl.numeric_level, st.stream_name"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admission_no   = trim($_POST['admission_no'] ?? '');
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $gender         = $_POST['gender'] ?? '';
    $dob            = $_POST['dob'] ?? null;
    $stream_id      = $_POST['stream_id'] ?? null;

    if ($admission_no === '' || $first_name === '' || $last_name === '' || !$gender || !$stream_id) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO students (admission_no, first_name, last_name, gender, dob, admission_date, status)
                 VALUES (:admission_no, :first_name, :last_name, :gender, :dob, CURDATE(), "active")'
            );
            $stmt->execute([
                'admission_no' => $admission_no,
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'gender'       => $gender,
                'dob'          => $dob ?: null,
            ]);

            $student_id = (int) $pdo->lastInsertId();

            $year_id = $pdo->query("SELECT id FROM academic_years WHERE is_current = 1 LIMIT 1")->fetchColumn();

            $stmt = $pdo->prepare(
                'INSERT INTO enrollments (student_id, stream_id, academic_year_id, enrollment_date, status)
                 VALUES (:student_id, :stream_id, :academic_year_id, CURDATE(), "active")'
            );
            $stmt->execute([
                'student_id'       => $student_id,
                'stream_id'        => $stream_id,
                'academic_year_id' => $year_id,
            ]);

            $pdo->commit();
            $success = 'Student added successfully.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Could not save student: ' . $e->getMessage();
        }
    }
}

$page_title = 'Add Student';
require __DIR__ . '/../../includes/header.php';
?>

<h1>Add Student</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card" style="max-width:500px;">
    <form method="post" action="">
        <label for="admission_no">Admission Number</label>
        <input type="text" id="admission_no" name="admission_no" required>

        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">-- Select --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob">

        <label for="stream_id">Class / Stream</label>
        <select id="stream_id" name="stream_id" required>
            <option value="">-- Select --</option>
            <?php foreach ($streams as $s): ?>
                <option value="<?= (int)$s['id'] ?>">
                    <?= htmlspecialchars($s['level_name'] . ' ' . $s['stream_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">Save Student</button>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
