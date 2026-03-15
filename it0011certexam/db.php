<?php

$host = "sql311.infinityfree.com";
$user = "if0_41359977";
$password = "InfinityFree619";
$database = "if0_41359977_students_db";

$conn = new mysqli($host,$user,$password,$database);

if($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}

?>