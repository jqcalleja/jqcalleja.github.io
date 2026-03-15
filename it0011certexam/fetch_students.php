<?php

require "db.php";

$section = "";

if(isset($_POST['section'])){
    $section = $_POST['section'];
}

if($section == ""){

$query = "SELECT * FROM students ORDER BY last_name ASC";
$stmt = $conn->prepare($query);

}else{

$query = "SELECT * FROM students WHERE section=? ORDER BY last_name ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s",$section);

}

$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()){

echo "<tr>";

echo "<td>".$row['id_number']."</td>";
echo "<td>".$row['last_name']."</td>";
echo "<td>".$row['first_name']."</td>";
echo "<td>".$row['middle_initial']."</td>";
echo "<td>".$row['email']."</td>";
echo "<td>".$row['section']."</td>";
echo "<td>".$row['certification_exam']."</td>";

echo "</tr>";

}

$stmt->close();
$conn->close();

?>