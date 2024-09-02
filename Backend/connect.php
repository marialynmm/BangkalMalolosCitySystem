<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "capstone"

$conn = new mysqli($host,$user,$password,$db);

if($conn -> connect_error){
    echo "connection error".$conn->connect_error;
}
?>