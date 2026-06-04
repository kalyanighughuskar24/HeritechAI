<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Success - AI Community</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{height:100vh;display:flex;justify-content:center;align-items:center;background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);color:#fff;}


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

.icon:nth-child(1){top:20%;left:10%;}
.icon:nth-child(2){top:40%;right:10%;}
.icon:nth-child(3){bottom:20%;left:20%;}
.icon:nth-child(4){bottom:10%;right:20%;}

@keyframes float{
0%{transform:translateY(0)}
50%{transform:translateY(-20px)}
100%{transform:translateY(0)}
}

.success-card{
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(18px);
  border-radius: 20px;
  padding: 40px 50px;
  width: 400px;
  text-align: center;
  box-shadow:0 20px 50px rgba(0,0,0,0.6);
}

.success-icon{
  font-size: 70px;
  color:#4BB543;
  margin-bottom:25px;
  animation: bounce 1.2s ease infinite alternate;
}

@keyframes bounce{
  from{transform:translateY(0);}
  to{transform:translateY(-15px);}
}

h1{font-size:28px;font-weight:600;margin-bottom:15px;}
p{font-size:16px;color:#d3d3d3;margin-bottom:35px;}
a.button{
  background:linear-gradient(45deg,#00c6ff,#0072ff);
  padding:14px 40px;
  border-radius:30px;
  color:white;
  text-decoration:none;
  font-weight:600;
  font-size:16px;
  transition:0.3s;
  display:inline-block;
}
a.button:hover{box-shadow:0 8px 20px rgba(0,118,255,0.7);transform:scale(1.05);}
</style>
</head>
<body>
  
<div class="icon">🤖</div>
<div class="icon">💡</div>
<div class="icon">🚀</div>
<div class="icon">🧠</div>



<div class="success-card">
  <div class="success-icon">✅</div>
  <h1>Content Submitted Successfully!</h1>
  <p>Thank you for sharing your content with the AI Community. We will review it shortly.</p>
  <a href="dashboard.php" class="button">Go to Dashboard</a>
</div>

</body>
</html>