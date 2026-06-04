<?php
session_start();
require_once "config.php";  // ✅ FIX 1: correct path

$email = $_POST['email'];
$password_input = $_POST['password'];

// ✅ FIX 2: fetch user by email only, then verify password separately
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_verified = 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();

    // ✅ FIX 3: use password_verify() because password is hashed
    if(password_verify($password_input, $user['password'])){
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role']       = $user['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Email or Password!'); window.location='login.php';</script>";
    }

} else {
    echo "<script>alert('Invalid Email or Password!'); window.location='login.php';</script>";
}
?>