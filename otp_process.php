<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if POST data exists
if(!isset($_POST['otp'])){
    echo "<script>
    alert('OTP not provided');
    window.location='otp_verify.php';
    </script>";
    exit;
}

/* DATABASE */

$conn = new mysqli("localhost","root","","ai_community");

if($conn->connect_error){
    die("Connection Failed: ".$conn->connect_error);
}



if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// check session OTP exists
if(!isset($_SESSION['otp'])){
    echo "<script>
    alert('Session expired. Please register again.');
    window.location='register.php';
    </script>";
    exit;
}

$entered_otp = $_POST['otp'];
$session_otp = $_SESSION['otp'];

if($entered_otp == $session_otp)
{
    if(!isset($_SESSION['email'])){
        echo "<script>
        alert('Session data missing. Please register again.');
        window.location='register.php';
        </script>";
        exit;
    }

    $email = $_SESSION['email'];

    // ✅ Just mark user as verified — already inserted in register_process.php
    $stmt = $conn->prepare("UPDATE users SET is_verified=1 WHERE email=?");

    if($stmt === false){
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);

    if($stmt->execute()){

        // clear session after success
        session_unset();
        session_destroy();

        // ✅ FIX: redirect with success flag instead of alert popup
        header("Location: login.php?success=1");
        exit;

    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

}
else
{
    echo "<script>
    alert('Invalid OTP');
    window.location='otp_verify.php';
    </script>";
    exit;
}

$conn->close();
?>