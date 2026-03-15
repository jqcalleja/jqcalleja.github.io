<?php

require "db.php";

$section = "";

if(isset($_GET['section'])){
    $section = $_GET['section'];
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=students_export.xls");

echo "ID Number\tLast Name\tFirst Name\tMI\tEmail\tSection\tCertification Exam\n";

if($section == ""){
    $stmt = $conn->prepare("SELECT * FROM students ORDER BY last_name");
}else{
    $stmt = $conn->prepare("SELECT * FROM students WHERE section=? ORDER BY last_name");
    $stmt->bind_param("s",$section);
}

$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
    echo $row['id_number']."\t".
        $row['last_name']."\t".
        $row['first_name']."\t".
        $row['middle_initial']."\t".
        $row['email']."\t".
        $row['section']."\n";
        $row['certification_exam']."\n";
}

$stmt->close();
$conn->close();
?>