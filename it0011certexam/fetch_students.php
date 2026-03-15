<?php
require "db.php";

$section = $_POST['section'] ?? "";
$exam = $_POST['exam'] ?? "";

$query = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if($section != ""){
    $query .= " AND section=?";
    $params[] = $section;
    $types .= "s";
}

if($exam != ""){
    $query .= " AND certification_exam=?";
    $params[] = $exam;
    $types .= "s";
}

$query .= " ORDER BY last_name ASC";

$stmt = $conn->prepare($query);

if(!empty($params)){
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$count = 1;
while($row = $result->fetch_assoc()){
    echo "<tr>";
    echo "<td>".$count."</td>";
    echo "<td>".$row['id_number']."</td>";
    echo "<td>".$row['last_name']."</td>";
    echo "<td>".$row['first_name']."</td>";
    echo "<td>".$row['middle_initial']."</td>";
    echo "<td>".$row['email']."</td>";
    echo "<td>".$row['section']."</td>";
    echo "<td>".$row['certification_exam']."</td>";
    echo "</tr>";
    $count = $count + 1;
}

$stmt->close();
$conn->close();
?>