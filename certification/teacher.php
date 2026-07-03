<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$examError = '';
$examSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam'])) {
    $examName = trim($_POST['exam_name'] ?? '');
    if ($examName === '') {
        $examError = 'Exam name cannot be empty.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO certification_exams (exam_name) VALUES (:name)");
            $stmt->execute([':name' => strtoupper($examName)]);
            $examSuccess = 'Certification exam added.';
        } catch (PDOException $e) {
            $examError = str_contains($e->getMessage(), 'UNIQUE')
                ? 'That certification exam already exists.'
                : 'Failed to add exam: ' . $e->getMessage();
        }
    }
}

$exams = $pdo->query("SELECT id, exam_name FROM certification_exams ORDER BY exam_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$schoolYears = $pdo->query("SELECT DISTINCT school_year FROM registrations ORDER BY school_year DESC")->fetchAll(PDO::FETCH_COLUMN);
$sections = $pdo->query("SELECT DISTINCT section FROM registrations ORDER BY section ASC")->fetchAll(PDO::FETCH_COLUMN);

$filterSY = $_GET['school_year'] ?? '';
$filterTerm = $_GET['term'] ?? '';
$filterExam = $_GET['certification_exam_id'] ?? '';
$filterSection = $_GET['section'] ?? '';
$filterKeyword = trim($_GET['keyword'] ?? '');

$where = [];
$params = [];

if ($filterSY !== '') {
    $where[] = 'r.school_year = :sy';
    $params[':sy'] = $filterSY;
}
if ($filterTerm !== '') {
    $where[] = 'r.term = :term';
    $params[':term'] = $filterTerm;
}
if ($filterExam !== '') {
    $where[] = 'r.certification_exam_id = :exam';
    $params[':exam'] = $filterExam;
}
if ($filterSection !== '') {
    $where[] = 'r.section = :section';
    $params[':section'] = $filterSection;
}
if ($filterKeyword !== '') {
    $where[] = '(r.student_id_number LIKE :kw OR r.last_name LIKE :kw OR r.first_name LIKE :kw)';
    $params[':kw'] = '%' . $filterKeyword . '%';
}

$sql = "SELECT r.*, ce.exam_name FROM registrations r JOIN certification_exams ce ON ce.id = r.certification_exam_id";
if (!empty($where)) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY r.date_registered DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$csvQuery = htmlspecialchars(http_build_query([
    'school_year' => $filterSY,
    'term' => $filterTerm,
    'certification_exam_id' => $filterExam,
    'section' => $filterSection,
    'keyword' => $filterKeyword,
]));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher's Page - ITS Certification Exam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }

        .container {
            max-width: 1100px;
            margin-top: 30px;
            margin-bottom: 60px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Teacher's Page</h4>
            <a href="register.php" class="btn btn-sm btn-outline-secondary">Registration Page</a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Add Certification Exam</h5>
                <?php if ($examError): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($examError) ?></div><?php endif; ?>
                <?php if ($examSuccess): ?><div class="alert alert-success py-2"><?= htmlspecialchars($examSuccess) ?></div><?php endif; ?>
                <form method="POST" class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="exam_name" class="form-control" placeholder="e.g. CISCO CCNA, COMPTIA A+" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" name="add_exam" value="1" class="btn btn-primary w-100">Add Exam</button>
                    </div>
                </form>

                <?php if (!empty($exams)): ?>
                    <div class="mt-3">
                        <span class="text-muted small">Existing exams:</span>
                        <?php foreach ($exams as $exam): ?>
                            <span class="badge bg-secondary me-1"><?= htmlspecialchars($exam['exam_name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Registered Students</h5>

                <form method="GET" class="row g-2 align-items-end mb-3">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">School Year</label>
                        <select name="school_year" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php foreach ($schoolYears as $sy): ?>
                                <option value="<?= htmlspecialchars($sy) ?>" <?= $filterSY === $sy ? 'selected' : '' ?>><?= htmlspecialchars($sy) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Term</label>
                        <select name="term" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1" <?= $filterTerm === '1' ? 'selected' : '' ?>>1st Term</option>
                            <option value="2" <?= $filterTerm === '2' ? 'selected' : '' ?>>2nd Term</option>
                            <option value="3" <?= $filterTerm === '3' ? 'selected' : '' ?>>3rd Term</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Section</label>
                        <select name="section" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?= htmlspecialchars($sec) ?>" <?= $filterSection === $sec ? 'selected' : '' ?>><?= htmlspecialchars($sec) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Certification Exam</label>
                        <select name="certification_exam_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?= $exam['id'] ?>" <?= (string)$filterExam === (string)$exam['id'] ? 'selected' : '' ?>><?= htmlspecialchars($exam['exam_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Search</label>
                        <input type="text" name="keyword" class="form-control form-control-sm" placeholder="ID / Name" value="<?= htmlspecialchars($filterKeyword) ?>">
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small"><?= count($students) ?> record(s) found</span>
                    <a class="btn btn-sm btn-success" href="export_csv.php?<?= $csvQuery ?>">Download CSV</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Section</th>
                                <th>Subject Enrolled</th>
                                <th>Certification Exam</th>
                                <th>School Year</th>
                                <th>Term</th>
                                <th>Date Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($s['student_id_number']) ?></td>
                                        <td><?= htmlspecialchars($s['last_name']) ?></td>
                                        <td><?= htmlspecialchars($s['first_name']) ?></td>
                                        <td><?= htmlspecialchars($s['section']) ?></td>
                                        <td><?= htmlspecialchars($s['subject_enrolled']) ?></td>
                                        <td><?= htmlspecialchars($s['exam_name']) ?></td>
                                        <td><?= htmlspecialchars($s['school_year']) ?></td>
                                        <td><?= htmlspecialchars(termLabel((int)$s['term'])) ?></td>
                                        <td><?= htmlspecialchars($s['date_registered']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>