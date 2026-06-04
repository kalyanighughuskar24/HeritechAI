<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Submit Content | AI Community</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
min-height:100vh;
background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
color:white;
overflow-x:hidden;
position:relative;
}

body::after{
content:"";
position:absolute;
width:300px;
height:300px;
background:#00e0ff;
filter:blur(150px);
top:20%;
left:40%;
opacity:.3;
z-index:-1;
}

@keyframes gradient{
0%{background-position:0% 50%;}
50%{background-position:100% 50%;}
100%{background-position:0% 50%;}
}

.navbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:20px 80px;
}

.logo{
font-size:30px;
font-weight:700;
}

.logo span{
color:#00eaff;
}

.icon{
position:absolute;
font-size:30px;
opacity:0.3;
animation:float 6s infinite ease-in-out;
}

.icon:nth-child(1){top:20%;left:10%;}
.icon:nth-child(2){top:40%;right:10%;}
.icon:nth-child(3){bottom:20%;left:20%;}
.icon:nth-child(4){bottom:10%;right:20%;}

@keyframes float{
0%{transform:translateY(0)}
50%{transform:translateY(-20px)}
100%{transform:translateY(0)}
}

.container{
width:420px;
margin:100px auto;
padding:45px;
border-radius:20px;
background:rgba(255,255,255,0.08);
backdrop-filter:blur(15px);
box-shadow:0 25px 50px rgba(0,0,0,0.6);
text-align:center;
animation:fade 1s ease;
}

@keyframes fade{
from{opacity:0; transform:translateY(40px);}
to{opacity:1; transform:translateY(0);}
}

.container h2{
margin-bottom:30px;
font-size:30px;
}

.input-group{
position:relative;
margin-bottom:25px;
}

.input-group input,
.input-group textarea{
width:100%;
padding:12px;
background:transparent;
border:none;
border-bottom:2px solid #aaa;
outline:none;
color:white;
font-size:16px;
}

.input-group textarea{
resize:none;
height:80px;
}

.input-group input:focus,
.input-group textarea:focus{
border-bottom:2px solid #00eaff;
}

.input-group label{
position:absolute;
top:12px;
left:5px;
color:#aaa;
font-size:14px;
transition:.3s;
}

.input-group input:focus ~ label,
.input-group input:valid ~ label,
.input-group textarea:focus ~ label,
.input-group textarea:valid ~ label{
top:-10px;
font-size:12px;
color:#00eaff;
}

/* FILE INPUT */
.file-input{
margin-bottom:25px;
text-align:left;
}

.file-input label{
display:block;
font-size:13px;
color:#aaa;
margin-bottom:8px;
}

.file-input input[type="file"]{
width:100%;
color:white;
font-size:14px;
}

.file-note{
font-size:12px;
color:#aaa;
margin-top:6px;
}

button{
width:100%;
padding:13px;
border:none;
border-radius:30px;
background:linear-gradient(45deg,#00c6ff,#0072ff);
color:white;
font-size:17px;
cursor:pointer;
transition:.3s;
}

button:hover{
transform:scale(1.05);
box-shadow:0 15px 35px rgba(0,198,255,0.6);
}

@media(max-width:500px){
.navbar{ padding:20px; }
.container{ width:90%; }
}

</style>
</head>

<body>

<div class="icon">🤖</div>
<div class="icon">💡</div>
<div class="icon">🚀</div>
<div class="icon">🧠</div>

<div class="navbar">
<div class="logo"> 🤖 HeritechAI</div>
</div>

<div class="container">

<h2>Submit Content 🚀</h2>

<form action="submit_process.php" method="POST" enctype="multipart/form-data">

<div class="input-group">
<input type="text" name="title" required>
<label>Title</label>
</div>

<div class="input-group">
<textarea name="description" required></textarea>
<label>Description</label>
</div>

<div class="input-group">
<input type="text" name="category" required>
<label>Category</label>
</div>

<!-- ✅ FIX: accept all file types -->
<div class="file-input">
<label>Upload File</label>
<input type="file" name="file"
  accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.mp3,.zip,.rar,.txt"
  required>

</div>

<button type="submit" name="submit">Submit</button>

</form>

</div>

</body>
</html>