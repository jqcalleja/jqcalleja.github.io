<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

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

$filename = 'registrations_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Student ID Number', 'Last Name', 'First Name', 'Section', 'Subject Enrolled', 'Certification Exam', 'School Year', 'Term', 'Date Registered']);

foreach ($students as $s) {
    fputcsv($out, [
        $s['student_id_number'],
        $s['last_name'],
        $s['first_name'],
        $s['section'],
        $s['subject_enrolled'],
        $s['exam_name'],
        $s['school_year'],
        termLabel((int)$s['term']),
        $s['date_registered'],
    ]);
}

fclose($out);
exit;
