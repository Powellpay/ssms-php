<?php
/**
 * New Curriculum Report Card
 * ---------------------------------------------------------------------
 * Renders a single learner's end-of-term report under Uganda's New Lower
 * Secondary Curriculum (Competency-Based Curriculum / CBC):
 *
 *   1. Header / learner & class details
 *   2. Subject results table (CA score, End-of-Term score, Final score,
 *      Grade A-E + descriptor, subject teacher's comment)
 *   3. Generic skills assessment (Communication, Cooperation & Self-
 *      Directed Learning, Critical Thinking & Problem Solving, Creativity
 *      & Innovation) rated on the skill_rating_scale
 *   4. Attendance summary
 *   5. Class teacher & head teacher comments, next term date
 *   6. Grading key (legend)
 *
 * NOTE FOR INTERNS: There is no single nationally-mandated layout for the
 * New Curriculum report card - NCDC sets the assessment framework, but
 * each school designs its own report card. Before going live, compare
 * this layout against your school's actual printed report card and adjust
 * the grading_scale / skill_rating_scale tables and the sections below to
 * match (e.g. add/remove generic skills, change wording, add a school
 * logo/letterhead, etc).
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

require_login();

$pdo = get_db();

$student_id = (int) ($_GET['student_id'] ?? 0);
$term_id    = (int) ($_GET['term_id'] ?? 0);

if (!$student_id) {
    http_response_code(400);
    die('Missing student_id.');
}

// ---------------------------------------------------------------------
// 1. Learner + class/stream details
// ---------------------------------------------------------------------

// If no term given, default to the current term
if (!$term_id) {
    $stmt = $pdo->query(
        "SELECT t.id
         FROM terms t
         JOIN academic_years ay ON ay.id = t.academic_year_id
         WHERE t.is_current = 1 AND ay.is_current = 1
         LIMIT 1"
    );
    $term_id = (int) ($stmt->fetchColumn() ?: 0);
}

$stmt = $pdo->prepare(
    "SELECT t.id AS term_id, t.term_name, t.next_term_begins,
            ay.id AS academic_year_id, ay.year_name
     FROM terms t
     JOIN academic_years ay ON ay.id = t.academic_year_id
     WHERE t.id = :term_id"
);
$stmt->execute(['term_id' => $term_id]);
$term = $stmt->fetch();

if (!$term) {
    http_response_code(404);
    die('Term not found. Please select a term from the Reports page.');
}

$stmt = $pdo->prepare(
    "SELECT s.id, s.admission_no, s.first_name, s.last_name, s.gender, s.dob, s.photo,
            cl.level_name, st.stream_name, st.id AS stream_id
     FROM students s
     LEFT JOIN enrollments e ON e.student_id = s.id AND e.academic_year_id = :ay
     LEFT JOIN streams st ON st.id = e.stream_id
     LEFT JOIN class_levels cl ON cl.id = st.class_level_id
     WHERE s.id = :sid"
);
$stmt->execute(['sid' => $student_id, 'ay' => $term['academic_year_id']]);
$student = $stmt->fetch();

if (!$student) {
    http_response_code(404);
    die('Learner not found.');
}

// ---------------------------------------------------------------------
// 2. Subject results (uses the v_subject_term_report view)
// ---------------------------------------------------------------------
$stmt = $pdo->prepare(
    "SELECT subject_name, ca_score, eot_score, final_score, final_grade,
            grade_descriptor, subject_teacher_comment
     FROM v_subject_term_report
     WHERE student_id = :sid AND term_name = :term_name AND year_name = :year_name
     ORDER BY subject_name"
);
$stmt->execute([
    'sid'       => $student_id,
    'term_name' => $term['term_name'],
    'year_name' => $term['year_name'],
]);
$results = $stmt->fetchAll();

// Summary: average final score and a simple grade tally
$gradeTally = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
$scoreSum   = 0;
$scoreCount = 0;
foreach ($results as $r) {
    if ($r['final_grade'] && isset($gradeTally[$r['final_grade']])) {
        $gradeTally[$r['final_grade']]++;
    }
    if ($r['final_score'] !== null) {
        $scoreSum += (float) $r['final_score'];
        $scoreCount++;
    }
}
$average = $scoreCount > 0 ? round($scoreSum / $scoreCount, 1) : null;

// ---------------------------------------------------------------------
// 3. Generic skills assessment
// ---------------------------------------------------------------------
$stmt = $pdo->prepare(
    "SELECT gs.skill_name, gs.description AS skill_description,
            srs.rating_label, srs.rating_value, gsr.remarks
     FROM generic_skill_ratings gsr
     JOIN generic_skills gs     ON gs.id = gsr.generic_skill_id
     JOIN skill_rating_scale srs ON srs.id = gsr.rating_id
     WHERE gsr.student_id = :sid AND gsr.term_id = :term_id
     ORDER BY gs.id"
);
$stmt->execute(['sid' => $student_id, 'term_id' => $term['term_id']]);
$skills = $stmt->fetchAll();

// ---------------------------------------------------------------------
// 4 & 5. Report card record (attendance + comments + next term)
// ---------------------------------------------------------------------
$stmt = $pdo->prepare(
    "SELECT days_present, days_absent, class_teacher_comment, head_teacher_comment,
            next_term_begins, date_issued
     FROM report_cards
     WHERE student_id = :sid AND term_id = :term_id"
);
$stmt->execute(['sid' => $student_id, 'term_id' => $term['term_id']]);
$reportCard = $stmt->fetch();

// ---------------------------------------------------------------------
// 6. Grading key (legend) - pulled from grading_scale so it stays in
//    sync with whatever bands the school has configured
// ---------------------------------------------------------------------
$gradingScale = $pdo->query(
    "SELECT grade, descriptor, min_score, max_score FROM grading_scale ORDER BY grade"
)->fetchAll();

$skillScale = $pdo->query(
    "SELECT rating_code, rating_label, rating_value FROM skill_rating_scale ORDER BY rating_value"
)->fetchAll();

$page_title = 'New Curriculum Report - ' . $student['first_name'] . ' ' . $student['last_name'];
require __DIR__ . '/../../includes/header.php';
?>

<div class="no-print" style="margin-bottom:1rem; display:flex; justify-content:space-between; align-items:center;">
    <a href="/modules/reports/index.php">&larr; Back to report selector</a>
    <button class="btn" onclick="window.print()">Print / Save as PDF</button>
</div>

<div class="report-card">

    <div class="report-header">
        <h1>Your School Name Here</h1>
        <p class="school-tagline">P.O. Box 0000, Kampala, Uganda &middot; New Lower Secondary Curriculum</p>
        <span class="report-title">Learner Progress Report &mdash; <?= htmlspecialchars($term['term_name'] . ', ' . $term['year_name']) ?></span>
    </div>

    <div class="report-meta">
        <div><span class="label">Learner:</span><strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong></div>
        <div><span class="label">Admission No:</span><?= htmlspecialchars($student['admission_no']) ?></div>
        <div><span class="label">Class:</span><?= htmlspecialchars(($student['level_name'] ?? '-') . ' ' . ($student['stream_name'] ?? '')) ?></div>
        <div><span class="label">Gender:</span><?= htmlspecialchars($student['gender']) ?></div>
        <div><span class="label">Term:</span><?= htmlspecialchars($term['term_name'] . ', ' . $term['year_name']) ?></div>
        <div><span class="label">Date Issued:</span><?= htmlspecialchars($reportCard['date_issued'] ?? '-') ?></div>
    </div>

    <h3 class="report-section-title">Subject Performance</h3>
    <?php if ($results): ?>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>CA (20)</th>
                    <th>End of Term (80)</th>
                    <th>Final (100)</th>
                    <th>Grade</th>
                    <th>Descriptor</th>
                    <th>Teacher's Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['subject_name']) ?></td>
                        <td><?= $r['ca_score'] !== null ? htmlspecialchars($r['ca_score']) : '-' ?></td>
                        <td><?= $r['eot_score'] !== null ? htmlspecialchars($r['eot_score']) : '-' ?></td>
                        <td><strong><?= $r['final_score'] !== null ? htmlspecialchars($r['final_score']) : '-' ?></strong></td>
                        <td class="grade-<?= htmlspecialchars($r['final_grade'] ?? '') ?>"><?= htmlspecialchars($r['final_grade'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($r['grade_descriptor'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($r['subject_teacher_comment'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if ($average !== null): ?>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Average Final Score</strong></td>
                    <td><strong><?= $average ?></strong></td>
                    <td colspan="3">
                        <?php
                        $tallyParts = [];
                        foreach ($gradeTally as $g => $count) {
                            if ($count > 0) $tallyParts[] = "$count x $g";
                        }
                        echo htmlspecialchars(implode(', ', $tallyParts));
                        ?>
                    </td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    <?php else: ?>
        <p>No subject results have been recorded for this learner this term yet.</p>
    <?php endif; ?>

    <h3 class="report-section-title">Generic Skills Assessment</h3>
    <?php if ($skills): ?>
        <div class="skills-grid">
            <?php foreach ($skills as $sk): ?>
                <div class="skill-card">
                    <div class="skill-name"><?= htmlspecialchars($sk['skill_name']) ?></div>
                    <div class="skill-rating"><?= htmlspecialchars($sk['rating_label']) ?></div>
                    <?php if ($sk['remarks']): ?>
                        <p class="skill-remarks"><?= htmlspecialchars($sk['remarks']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No generic skills have been rated for this learner this term yet.</p>
    <?php endif; ?>

    <h3 class="report-section-title">Attendance</h3>
    <?php if ($reportCard): ?>
        <div class="attendance-summary">
            <div class="stat">
                <span class="num"><?= (int) $reportCard['days_present'] ?></span>
                <span class="lbl">Days Present</span>
            </div>
            <div class="stat">
                <span class="num"><?= (int) $reportCard['days_absent'] ?></span>
                <span class="lbl">Days Absent</span>
            </div>
            <div class="stat">
                <span class="num"><?= (int) $reportCard['days_present'] + (int) $reportCard['days_absent'] ?></span>
                <span class="lbl">Total School Days</span>
            </div>
        </div>
    <?php else: ?>
        <p>No attendance summary recorded for this term yet.</p>
    <?php endif; ?>

    <h3 class="report-section-title">Comments</h3>
    <div class="comment-box">
        <span class="comment-label">Class Teacher's Comment</span>
        <?= $reportCard && $reportCard['class_teacher_comment'] ? htmlspecialchars($reportCard['class_teacher_comment']) : '<em>Not yet recorded.</em>' ?>
    </div>
    <div class="comment-box">
        <span class="comment-label">Head Teacher's Comment</span>
        <?= $reportCard && $reportCard['head_teacher_comment'] ? htmlspecialchars($reportCard['head_teacher_comment']) : '<em>Not yet recorded.</em>' ?>
    </div>
    <?php if ($reportCard && $reportCard['next_term_begins']): ?>
        <p><strong>Next term begins:</strong> <?= htmlspecialchars($reportCard['next_term_begins']) ?></p>
    <?php endif; ?>

    <h3 class="report-section-title">Grading Key</h3>
    <table>
        <thead>
            <tr><th>Grade</th><th>Descriptor</th><th>Score Range (%)</th></tr>
        </thead>
        <tbody>
            <?php foreach ($gradingScale as $g): ?>
                <tr>
                    <td class="grade-<?= htmlspecialchars($g['grade']) ?>"><?= htmlspecialchars($g['grade']) ?></td>
                    <td><?= htmlspecialchars($g['descriptor']) ?></td>
                    <td><?= htmlspecialchars($g['min_score']) ?> - <?= htmlspecialchars($g['max_score']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="grading-key">
        Generic skills scale:
        <?php
        $scaleParts = [];
        foreach ($skillScale as $sc) {
            $scaleParts[] = $sc['rating_value'] . ' = ' . $sc['rating_label'];
        }
        echo htmlspecialchars(implode(', ', $scaleParts));
        ?>
    </p>
    <p class="grading-key">
        CA = Continuous Assessment (20% of final mark). End of Term = summative
        exam (80% of final mark). Final = CA + End of Term.
    </p>

</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
