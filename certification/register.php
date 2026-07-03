<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$errors = [];
$success = false;

$exams = $pdo->query("SELECT id, exam_name FROM certification_exams ORDER BY exam_name ASC")->fetchAll(PDO::FETCH_ASSOC);

$detected = getCurrentTermAndSY();

$form = [
    'student_id_number' => '',
    'last_name' => '',
    'first_name' => '',
    'section' => '',
    'subject_enrolled' => '',
    'certification_exam_id' => '',
    'school_year' => $detected['school_year'],
    'term' => $detected['term'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $default) {
        if (isset($_POST[$key])) {
            $form[$key] = trim($_POST[$key]);
        }
    }

    if ($form['student_id_number'] === '') $errors[] = 'Student ID Number is required.';
    if ($form['last_name'] === '') $errors[] = 'Last Name is required.';
    if ($form['first_name'] === '') $errors[] = 'First Name is required.';
    if ($form['section'] === '') $errors[] = 'Section is required.';
    if ($form['subject_enrolled'] === '') $errors[] = 'Subject Enrolled is required.';
    if ($form['certification_exam_id'] === '') $errors[] = 'Certification Exam is required.';
    if (!preg_match('/^\d{4}-\d{4}$/', $form['school_year'])) $errors[] = 'School Year must be in the format YYYY-YYYY.';
    if (!in_array((string)$form['term'], ['1', '2', '3'], true)) $errors[] = 'Term is invalid.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO registrations
            (student_id_number, last_name, first_name, section, subject_enrolled, certification_exam_id, school_year, term)
            VALUES (:sid, :ln, :fn, :sec, :subj, :exam, :sy, :term)");
        $stmt->execute([
            ':sid' => $form['student_id_number'],
            ':ln' => strtoupper($form['last_name']),
            ':fn' => strtoupper($form['first_name']),
            ':sec' => strtoupper($form['section']),
            ':subj' => strtoupper($form['subject_enrolled']),
            ':exam' => $form['certification_exam_id'],
            ':sy' => $form['school_year'],
            ':term' => $form['term'],
        ]);

        $success = true;
        $form = [
            'student_id_number' => '',
            'last_name' => '',
            'first_name' => '',
            'section' => '',
            'subject_enrolled' => '',
            'certification_exam_id' => '',
            'school_year' => $detected['school_year'],
            'term' => $detected['term'],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Registration - ITS Certification Exam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            max-width: 720px;
            margin: 40px auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">ITS Certification Exam Registration</h4>
                    <a href="teacher.php" class="btn btn-sm btn-outline-secondary">Teacher's Page</a>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success">Registration submitted successfully.</div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (empty($exams)): ?>
                    <div class="alert alert-warning">
                        No certification exams available yet. Please ask your teacher to add one on the
                        <a href="teacher.php">Teacher's Page</a> first.
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student ID Number</label>
                            <input type="text" name="student_id_number" class="form-control"
                                value="<?= htmlspecialchars($form['student_id_number']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control uppercase-input"
                                value="<?= htmlspecialchars($form['section']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control uppercase-input"
                                value="<?= htmlspecialchars($form['last_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control uppercase-input"
                                value="<?= htmlspecialchars($form['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject Enrolled</label>
                            <input type="text" name="subject_enrolled" class="form-control uppercase-input"
                                value="<?= htmlspecialchars($form['subject_enrolled']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Certification Exam</label>
                            <select name="certification_exam_id" class="form-select" required>
                                <option value="">-- Select Exam --</option>
                                <?php foreach ($exams as $exam): ?>
                                    <option value="<?= $exam['id'] ?>"
                                        <?= ((string)$form['certification_exam_id'] === (string)$exam['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($exam['exam_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                School Year <span class="text-muted small">(auto-detected, override if needed)</span>
                            </label>
                            <input type="text" name="school_year" class="form-control"
                                pattern="\d{4}-\d{4}" placeholder="YYYY-YYYY"
                                value="<?= htmlspecialchars($form['school_year']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Term <span class="text-muted small">(auto-detected, override if needed)</span>
                            </label>
                            <select name="term" class="form-select" required>
                                <option value="1" <?= (string)$form['term'] === '1' ? 'selected' : '' ?>>1st Term (Aug - Nov)</option>
                                <option value="2" <?= (string)$form['term'] === '2' ? 'selected' : '' ?>>2nd Term (Dec - Mar)</option>
                                <option value="3" <?= (string)$form['term'] === '3' ? 'selected' : '' ?>>3rd Term (Apr - Jul)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Submit Registration</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.uppercase-input').forEach(function(el) {
            el.addEventListener('input', function() {
                const pos = el.selectionStart;
                el.value = el.value.toUpperCase();
                el.setSelectionRange(pos, pos);
            });
        });
    </script>
</body>

</html>