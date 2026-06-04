<?php
session_start();

// Check session
if(!isset($_SESSION['otp'])){
    echo "<script>
    alert('Session expired. Please register again.');
    window.location='register.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP | AI Community</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

/* 🌿 Clean Background */
body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:linear-gradient(135deg,#1e293b,#0f172a);
color:white;
}

/* 🔷 Card */
.container{
width:350px;
padding:35px;
border-radius:15px;
background:#1e293b;
box-shadow:0 10px 30px rgba(0,0,0,0.4);
text-align:center;
}

/* Logo */
.logo{
font-size:22px;
font-weight:600;
margin-bottom:15px;
color:#38bdf8;
}

/* Heading */
h2{
font-size:22px;
margin-bottom:8px;
}

p{
font-size:13px;
color:#94a3b8;
margin-bottom:20px;
}

/* OTP Inputs */
.otp-box{
display:flex;
justify-content:space-between;
gap:8px;
}

.otp-box input{
width:45px;
height:50px;
border-radius:8px;
border:1px solid #334155;
background:#0f172a;
color:white;
text-align:center;
font-size:18px;
outline:none;
transition:0.2s;
}

.otp-box input:focus{
border-color:#38bdf8;
}

/* Button */
button{
margin-top:20px;
width:100%;
padding:12px;
border:none;
border-radius:8px;
background:#38bdf8;
color:#0f172a;
font-size:15px;
cursor:pointer;
transition:0.2s;
}

button:hover{
background:#0ea5e9;
}

/* Small link */
.resend{
margin-top:12px;
font-size:12px;
color:#94a3b8;
}

.resend a{
color:#38bdf8;
text-decoration:none;
}

</style>
</head>

<body>

<div class="container">

<div class="logo">🤖 HeritechAI</div>

<h2>Verify OTP</h2>
<p>Enter the 6-digit code sent to your email</p>

<form action="otp_process.php" method="POST">

<div class="otp-box">
<input type="text" maxlength="1" required>
<input type="text" maxlength="1" required>
<input type="text" maxlength="1" required>
<input type="text" maxlength="1" required>
<input type="text" maxlength="1" required>
<input type="text" maxlength="1" required>
</div>

<input type="hidden" name="otp" id="fullOtp">

<button type="submit">Verify</button>

</form>

<div class="resend">
Didn't receive OTP? <a href="#">Resend</a>
</div>

</div>

<script>
// OTP auto move
const inputs = document.querySelectorAll(".otp-box input");
const hidden = document.getElementById("fullOtp");

inputs.forEach((input, index) => {

input.addEventListener("input", () => {
if(input.value.length === 1 && index < inputs.length - 1){
inputs[index + 1].focus();
}
updateOTP();
});

input.addEventListener("keydown", (e) => {
if(e.key === "Backspace" && index > 0 && !input.value){
inputs[index - 1].focus();
}
});

});

function updateOTP(){
hidden.value = [...inputs].map(i => i.value).join('');
}
</script>

</body>
</html>