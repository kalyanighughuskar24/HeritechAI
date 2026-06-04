<?php
session_start();
require_once "config.php";

if(!isset($_POST['contact_submit'])){
    header("Location: contact.php");
    exit();
}

$name    = $conn->real_escape_string($_POST['name']);
$email   = $conn->real_escape_string($_POST['email']);
$subject = $conn->real_escape_string($_POST['subject']);
$message = $conn->real_escape_string($_POST['message']);

// Save to database
$stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?,?,?,?)");

if($stmt){
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    $stmt->execute();
    $stmt->close();
}

header("Location: contact.php?sent=1");
exit();
?>