<?php
session_start();

if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $otp = rand(100000,999999);

    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
    $_SESSION['otp'] = $otp;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>OTP Sent | AI Community</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

/* 🌌 Background */
body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:linear-gradient(135deg,#0f172a,#1e293b);
color:white;
}

/* 🔷 Card */
.container{
width:400px;
padding:40px;
border-radius:18px;
background:#1e293b;
box-shadow:0 20px 40px rgba(0,0,0,0.5);
text-align:center;
animation:fade 0.8s ease;
}

@keyframes fade{
from{opacity:0; transform:translateY(30px);}
to{opacity:1; transform:translateY(0);}
}

/* 🤖 Logo */
.logo{
font-size:22px;
font-weight:700;
margin-bottom:20px;
color:#38bdf8;
}

/* ✅ Success Circle */
.success{
width:80px;
height:80px;
margin:0 auto 20px;
border-radius:50%;
background:#22c55e;
display:flex;
justify-content:center;
align-items:center;
font-size:35px;
animation:pop 0.6s ease;
}

@keyframes pop{
0%{transform:scale(0);}
80%{transform:scale(1.2);}
100%{transform:scale(1);}
}

/* Text */
h2{
font-size:24px;
margin-bottom:10px;
}

p{
font-size:14px;
color:#94a3b8;
margin-bottom:10px;
}

/* 📧 Email Highlight Box */
.email-box{
background:#0f172a;
border:1px solid #334155;
padding:10px;
border-radius:8px;
margin:15px 0;
font-size:14px;
color:#38bdf8;
}

/* Button */
.btn{
display:block;
margin-top:20px;
padding:12px;
border-radius:8px;
background:#38bdf8;
color:#0f172a;
text-decoration:none;
font-weight:600;
transition:0.2s;
}

.btn:hover{
background:#0ea5e9;
}

/* Resend */
.resend{
margin-top:12px;
font-size:13px;
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

<div class="success">✓</div>

<h2>OTP Sent Successfully</h2>

<p>We’ve sent a verification code to your email:</p>

<div class="email-box">
<?php echo $_SESSION['email']; ?>
</div>

<p>Please check your inbox and enter the OTP to complete registration.</p>

<a href="otp_verify.php" class="btn">Verify OTP</a>

<div class="resend">
Didn’t receive OTP? <a href="#">Resend</a>
</div>

</div>

</body>
</html>