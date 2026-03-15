<?php

require "db.php";

$id = $_POST['id_number'];
$last = $_POST['last_name'];
$first = $_POST['first_name'];
$mi = $_POST['middle_initial'];
$email = $_POST['email'];
$section = $_POST['section'];
$certification = $_POST['certification_exam'];

if(empty($id) || empty($last) || empty($first) || empty($email) || empty($section) || empty($certification)){
    echo "All required fields must be filled.";
    exit();
}

$stmt = $conn->prepare(
"INSERT INTO students (id_number,last_name,first_name,middle_initial,email,section,certification_exam)
VALUES (?,?,?,?,?,?,?)"
);

$stmt->bind_param("sssssss",$id,$last,$first,$mi,$email,$section,$certification);

if($stmt->execute()){
    echo "You have signed-up sucessfully!";
}else{
    echo "Error: ID number may already exist.";
}

$stmt->close();
$conn->close();

?>