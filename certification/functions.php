<?php
function getCurrentTermAndSY(?string $date = null): array
{
    $ts = $date ? strtotime($date) : time();
    $month = (int) date('n', $ts);
    $year = (int) date('Y', $ts);

    if ($month >= 8 && $month <= 11) {
        // Aug - Nov
        $term = 1;
        $syStart = $year;
    } elseif ($month == 12 || ($month >= 1 && $month <= 3)) {
        // Dec - Mar
        $term = 2;
        $syStart = ($month == 12) ? $year : $year - 1;
    } else {
        // Apr - Jul
        $term = 3;
        $syStart = $year - 1;
    }

    return [
        'term' => $term,
        'school_year' => $syStart . '-' . ($syStart + 1),
    ];
}

function termLabel(int $term): string
{
    $labels = [1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
    return $labels[$term] ?? 'Unknown';
}
