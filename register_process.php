<?php
session_start();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if config.php exists
if (!file_exists("config.php")) {
    die("Error: config.php not found!");
}
require_once "config.php";

// ✅ FIXED PHPMailer PATH
$phpmailer_path = __DIR__ . '/PHPMailer/src/';

// Check PHPMailer files exist
if (!file_exists($phpmailer_path . 'PHPMailer.php') || 
    !file_exists($phpmailer_path . 'SMTP.php') || 
    !file_exists($phpmailer_path . 'Exception.php')) {
    die("Error: PHPMailer files not found! Path checked: " . $phpmailer_path);
}

require $phpmailer_path . 'PHPMailer.php';
require $phpmailer_path . 'SMTP.php';
require $phpmailer_path . 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if form was submitted
if(!isset($_POST['register'])){
    $_SESSION['register_error'] = "Invalid request!";
    header("Location: register.php");
    exit();
}

// Validate and sanitize inputs
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password_raw = isset($_POST['password']) ? $_POST['password'] : '';

// Basic validation
if(empty($name) || empty($email) || empty($password_raw)){
    $_SESSION['register_error'] = "All fields are required!";
    header("Location: register.php");
    exit();
}

// Validate email format
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION['register_error'] = "Invalid email format!";
    header("Location: register.php");
    exit();
}

$role = "user";
$password = password_hash($password_raw, PASSWORD_DEFAULT);
$otp = random_int(100000,999999);

// Check database connection
if(!isset($conn) || $conn->connect_error){
    $_SESSION['register_error'] = "Database connection failed!";
    header("Location: register.php");
    exit();
}

/* CHECK EMAIL */
$check = $conn->prepare("SELECT id FROM users WHERE email=?");
if(!$check){
    $_SESSION['register_error'] = "Database error: " . $conn->error;
    header("Location: register.php");
    exit();
}

$check->bind_param("s",$email);
$check->execute();
$result = $check->get_result();

if($result->num_rows > 0){
    $_SESSION['register_error'] = "Email already registered!";
    header("Location: register.php");
    exit();
}

/* INSERT USER */
$stmt = $conn->prepare("INSERT INTO users(name,email,password,role,otp,is_verified) VALUES (?,?,?,?,?,0)");
if(!$stmt){
    $_SESSION['register_error'] = "Database error: " . $conn->error;
    header("Location: register.php");
    exit();
}

$stmt->bind_param("ssssi",$name,$email,$password,$role,$otp);

if($stmt->execute()){

    $_SESSION['verify_email'] = $email;
    $_SESSION['otp']          = $otp;       // ✅ FIX: set OTP in session
    $_SESSION['name']         = $name;      // ✅ FIX: set name in session
    $_SESSION['email']        = $email;     // ✅ FIX: set email in session
    $_SESSION['password']     = $password;  // ✅ FIX: set password in session

    // Try to send email, but don't fail registration if email fails
    $mail_sent = false;
    $mail_error = "";
    
    try{
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'anirudhchaudhari581@gmail.com';
        $mail->Password = 'hnuwjllzfvsydlqd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('anirudhchaudhari581@gmail.com','AI Community');
        $mail->addAddress($email);
        
        $mail->isHTML(false);
        $mail->Subject = "OTP Verification";
        $mail->Body = "Your OTP is: $otp";
        
        $mail->SMTPDebug = 0; // Set to 2 for debugging
        
        $mail_sent = $mail->send();
        
    } catch(Exception $e){
        $mail_error = $mail->ErrorInfo;
        error_log("Mail Error: " . $mail_error);
    }

    if($mail_sent){
        header("Location: verify.php");
    } else {
        $_SESSION['otp_display'] = $otp;
        $_SESSION['mail_error'] = "Email could not be sent. Your OTP is shown below.";
        header("Location: verify.php");
    }
    exit();

} else {
    $_SESSION['register_error'] = "Registration failed: " . $stmt->error;
    header("Location: register.php");
    exit();
}
?>