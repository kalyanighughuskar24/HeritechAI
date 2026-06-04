<?php
$conn = new mysqli("localhost", "root", "", "ai_community");

if($conn->connect_error){
    die("Connection Failed: ".$conn->connect_error);
}
?>