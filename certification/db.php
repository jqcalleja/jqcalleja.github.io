<?php
$dbFile = __DIR__ . '/data/registration.db';

if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$pdo->exec("CREATE TABLE IF NOT EXISTS certification_exams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    exam_name TEXT NOT NULL UNIQUE,
    date_added TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS registrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id_number TEXT NOT NULL,
    last_name TEXT NOT NULL,
    first_name TEXT NOT NULL,
    section TEXT NOT NULL,
    subject_enrolled TEXT NOT NULL,
    certification_exam_id INTEGER NOT NULL,
    school_year TEXT NOT NULL,
    term INTEGER NOT NULL,
    date_registered TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (certification_exam_id) REFERENCES certification_exams(id)
)");
