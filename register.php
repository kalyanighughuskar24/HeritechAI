<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | HeritechAI</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --cyan:#00e0ff;
  --green:#00ffa6;
  --purple:#a78bfa;
  --pink:#f472b6;
  --dark:#08111f;
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  min-height:100vh;
  overflow-x:hidden;
  color:white;
  position:relative;
  background:#08111f;
}

/* ───────────────── BACKGROUND IMAGE ───────────────── */
body::before{
  content:'';
  position:fixed;
  inset:0;
  background:
  linear-gradient(rgba(5,10,20,0.82), rgba(5,10,20,0.88)),
  url('https://images.unsplash.com/photo-1524492412937-b28074a5d7da?q=80&w=1974&auto=format&fit=crop') center/cover no-repeat;
  z-index:-4;
}

/* Heritage overlay */
body::after{
  content:'';
  position:fixed;
  inset:0;
  background:
  radial-gradient(circle at top left, rgba(0,224,255,0.15), transparent 30%),
  radial-gradient(circle at bottom right, rgba(167,139,250,0.18), transparent 35%);
  z-index:-3;
}

/* ───────────────── ORBS ───────────────── */
.bg-orbs{
  position:fixed;
  inset:0;
  overflow:hidden;
  z-index:-2;
  pointer-events:none;
}

.orb{
  position:absolute;
  border-radius:50%;
  filter:blur(110px);
  opacity:.18;
  animation:floatOrb 12s infinite ease-in-out;
}

.orb1{
  width:420px;
  height:420px;
  background:#00e0ff;
  top:-120px;
  left:-120px;
}

.orb2{
  width:350px;
  height:350px;
  background:#00ffa6;
  right:-100px;
  bottom:-100px;
  animation-delay:4s;
}

.orb3{
  width:300px;
  height:300px;
  background:#a78bfa;
  top:40%;
  left:50%;
  animation-delay:7s;
}

@keyframes floatOrb{
  0%,100%{
    transform:translate(0,0);
  }
  50%{
    transform:translate(30px,-20px);
  }
}

/* ───────────────── PARTICLES ───────────────── */
#particles{
  position:fixed;
  inset:0;
  z-index:-1;
  overflow:hidden;
}

.particle{
  position:absolute;
  border-radius:50%;
  opacity:.5;
  animation:particleMove linear infinite;
}

@keyframes particleMove{
  from{
    transform:translateY(100vh);
    opacity:0;
  }
  20%{
    opacity:.6;
  }
  to{
    transform:translateY(-100px);
    opacity:0;
  }
}

/* ───────────────── NAVBAR ───────────────── */
nav{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  z-index:1000;

  display:flex;
  justify-content:space-between;
  align-items:center;

  padding:18px 70px;

  background:rgba(0,0,0,0.28);
  backdrop-filter:blur(18px);

  border-bottom:1px solid rgba(255,255,255,0.08);
}

.logo{
  font-size:24px;
  font-weight:800;
  letter-spacing:.5px;
}

.logo span{
  color:var(--cyan);
}

.nav-links{
  display:flex;
  align-items:center;
  gap:12px;
  list-style:none;
}

.nav-links a{
  text-decoration:none;
  color:rgba(255,255,255,.75);
  font-size:14px;
  padding:10px 18px;
  border-radius:30px;
  transition:.3s;
}

.nav-links a:hover{
  background:rgba(255,255,255,.08);
  color:white;
}

.btn-nav{
  background:linear-gradient(90deg,var(--cyan),var(--green));
  color:#000 !important;
  font-weight:700;
}

.btn-nav:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 30px rgba(0,224,255,.35);
}

/* ───────────────── MAIN ───────────────── */
.main{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:90px;

  padding:120px 30px 60px;
}

/* ───────────────── LEFT PANEL ───────────────── */
.left-panel{
  width:500px;
  animation:fadeLeft .9s ease;
}

@keyframes fadeLeft{
  from{
    opacity:0;
    transform:translateX(-40px);
  }
  to{
    opacity:1;
    transform:translateX(0);
  }
}

.badge{
  display:inline-flex;
  align-items:center;
  gap:10px;

  background:rgba(0,224,255,.08);
  border:1px solid rgba(0,224,255,.25);

  padding:8px 20px;
  border-radius:50px;

  font-size:13px;
  color:var(--cyan);

  margin-bottom:30px;
}

.badge .dot{
  width:8px;
  height:8px;
  background:var(--cyan);
  border-radius:50%;
  animation:pulse 2s infinite;
}

@keyframes pulse{
  50%{
    transform:scale(1.5);
    opacity:.4;
  }
}

.left-panel h1{
  font-size:60px;
  line-height:1.1;
  margin-bottom:24px;
  font-weight:800;
}

.gradient{
  background:linear-gradient(90deg,var(--cyan),var(--green),var(--purple));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-size:200%;
  animation:gradientMove 5s linear infinite;
}

@keyframes gradientMove{
  to{
    background-position:200%;
  }
}

.left-panel p{
  color:rgba(255,255,255,.72);
  line-height:1.9;
  font-size:15px;
  margin-bottom:40px;
}

.features{
  display:flex;
  flex-direction:column;
  gap:16px;
}

.feature{
  display:flex;
  align-items:center;
  gap:16px;

  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.08);

  padding:18px;
  border-radius:18px;

  backdrop-filter:blur(10px);

  transition:.3s;
}

.feature:hover{
  transform:translateX(8px);
  border-color:rgba(0,224,255,.35);
  background:rgba(0,224,255,.06);
}

.feature-icon{
  font-size:28px;
}

.feature h3{
  font-size:15px;
  margin-bottom:4px;
}

.feature span{
  font-size:12px;
  opacity:.6;
}

/* ───────────────── REGISTER CARD ───────────────── */
.register-card{
  width:450px;

  background:rgba(255,255,255,.08);
  border:1px solid rgba(255,255,255,.12);

  backdrop-filter:blur(24px);

  border-radius:32px;

  padding:45px 40px;

  box-shadow:
  0 20px 80px rgba(0,0,0,.55),
  inset 0 0 0 1px rgba(255,255,255,.04);

  animation:fadeRight .9s ease;
}

@keyframes fadeRight{
  from{
    opacity:0;
    transform:translateX(40px);
  }
  to{
    opacity:1;
    transform:translateX(0);
  }
}

.card-header{
  text-align:center;
  margin-bottom:35px;
}

.card-icon{
  width:80px;
  height:80px;

  margin:auto auto 18px;

  border-radius:24px;

  display:flex;
  align-items:center;
  justify-content:center;

  font-size:35px;

  background:linear-gradient(135deg,
  rgba(0,224,255,.18),
  rgba(0,255,166,.12));

  border:1px solid rgba(0,224,255,.28);
}

.card-header h2{
  font-size:32px;
  font-weight:800;
  margin-bottom:8px;
}

.card-header p{
  opacity:.55;
  font-size:13px;
}

/* ───────────────── FORM ───────────────── */
.input-label{
  display:block;
  margin-bottom:8px;
  font-size:12px;
  font-weight:600;
  letter-spacing:.6px;
  color:rgba(255,255,255,.55);
}

.input-group{
  position:relative;
  margin-bottom:22px;
}

.inp-icon{
  position:absolute;
  top:50%;
  left:18px;
  transform:translateY(-50%);
  font-size:16px;
}

.input-group input{
  width:100%;
  height:58px;

  background:rgba(255,255,255,.06);

  border:1px solid rgba(255,255,255,.12);

  border-radius:16px;

  padding:0 50px;

  color:white;
  font-size:14px;

  outline:none;

  transition:.3s;
}

.input-group input::placeholder{
  color:rgba(255,255,255,.3);
}

.input-group input:focus{
  border-color:var(--cyan);
  background:rgba(0,224,255,.05);

  box-shadow:0 0 0 4px rgba(0,224,255,.08);
}

.toggle-btn{
  position:absolute;
  right:16px;
  top:50%;
  transform:translateY(-50%);

  border:none;
  background:none;

  color:rgba(255,255,255,.45);

  cursor:pointer;
  font-size:16px;
}

/* ───────────────── PASSWORD STRENGTH ───────────────── */
.strength-bar{
  display:flex;
  gap:6px;
  margin-top:-10px;
  margin-bottom:20px;
}

.strength-seg{
  flex:1;
  height:5px;
  border-radius:30px;
  background:rgba(255,255,255,.1);
}

.weak{background:#ff416c;}
.medium{background:#ffc857;}
.strong{background:#00ffa6;}

.strength-label{
  text-align:right;
  font-size:12px;
  opacity:.7;
  margin-top:-14px;
  margin-bottom:20px;
}

/* ───────────────── BUTTON ───────────────── */
.submit-btn{
  width:100%;
  height:58px;

  border:none;
  border-radius:16px;

  background:linear-gradient(90deg,var(--cyan),var(--green));

  color:#000;
  font-size:16px;
  font-weight:800;

  cursor:pointer;

  transition:.35s;

  position:relative;
  overflow:hidden;
}

.submit-btn::before{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(
    120deg,
    transparent,
    rgba(255,255,255,.25),
    transparent
  );
  transform:translateX(-100%);
  transition:.7s;
}

.submit-btn:hover::before{
  transform:translateX(100%);
}

.submit-btn:hover{
  transform:translateY(-3px);
  box-shadow:0 15px 40px rgba(0,224,255,.35);
}

/* ───────────────── EXTRA ───────────────── */
.divider{
  display:flex;
  align-items:center;
  gap:12px;
  margin:28px 0;
  opacity:.25;
  font-size:12px;
}

.divider::before,
.divider::after{
  content:'';
  flex:1;
  height:1px;
  background:rgba(255,255,255,.15);
}

.footer-text{
  text-align:center;
  font-size:13px;
  color:rgba(255,255,255,.55);
}

.footer-text a{
  color:var(--cyan);
  text-decoration:none;
  font-weight:600;
}

.footer-text a:hover{
  text-decoration:underline;
}

.terms{
  margin-top:20px;
  text-align:center;
  font-size:11px;
  line-height:1.7;
  opacity:.45;
}

/* ───────────────── ERROR ───────────────── */
.error-msg{
  background:rgba(255,65,108,.12);
  border:1px solid rgba(255,65,108,.35);
  color:#ff98ac;

  padding:14px 16px;
  border-radius:14px;

  margin-bottom:22px;
  font-size:13px;
}

/* ───────────────── FOOTER ───────────────── */
footer{
  text-align:center;
  padding:25px;
  font-size:13px;
  opacity:.35;
}

/* ───────────────── RESPONSIVE ───────────────── */
@media(max-width:980px){

  .left-panel{
    display:none;
  }

  .main{
    padding:110px 18px 50px;
  }

  .register-card{
    width:100%;
    max-width:450px;
  }

  nav{
    padding:16px 20px;
  }

  .nav-links{
    gap:5px;
  }

  .nav-links a{
    padding:8px 12px;
    font-size:12px;
  }
}

@media(max-width:480px){

  .register-card{
    padding:34px 24px;
  }

  .card-header h2{
    font-size:26px;
  }
}
</style>
</head>

<body>

<!-- ORBS -->
<div class="bg-orbs">
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>
</div>

<!-- PARTICLES -->
<div id="particles"></div>

<!-- NAVBAR -->
<nav>

  <div class="logo">
    🤖 <span>HeritechAI</span>
  </div>

  <ul class="nav-links">

    <li><a href="index.php">Home</a></li>
    <li><a href="about.php">About</a></li>

    <?php if(isset($_SESSION['user_id'])): ?>

      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="submit.php">Upload</a></li>

      <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <li><a href="admin/admin_panel.php" class="btn-nav">Admin</a></li>
      <?php endif; ?>

      <li><a href="logout.php">Logout</a></li>

    <?php else: ?>

      <li><a href="contact.php">Contact</a></li>
      <li><a href="login.php">Login</a></li>

    <?php endif; ?>

  </ul>

</nav>

<!-- MAIN -->
<div class="main">

  <!-- LEFT SIDE -->
  <div class="left-panel">

    <div class="badge">
      <span class="dot"></span>
      AI Powered Community Platform
    </div>

    <h1>
      Discover India's<br>
      <span class="gradient">Heritage & AI</span><br>
      Together ✨
    </h1>

    <p>
      Join the next generation AI creator community inspired by India's beautiful heritage places, innovation, and digital future. Upload projects, connect with creators, and share your ideas globally.
    </p>

    <div class="features">

      <div class="feature">
        <div class="feature-icon">🏛️</div>
        <div>
          <h3>Heritage Inspired Design</h3>
          <span>Modern AI community with Indian cultural aesthetics</span>
        </div>
      </div>

      <div class="feature">
        <div class="feature-icon">📤</div>
        <div>
          <h3>Upload Your AI Projects</h3>
          <span>Share PDFs, images, videos, audio and research</span>
        </div>
      </div>

      <div class="feature">
        <div class="feature-icon">🌎</div>
        <div>
          <h3>Global Community</h3>
          <span>Connect with developers and innovators worldwide</span>
        </div>
      </div>

      <div class="feature">
        <div class="feature-icon">⚡</div>
        <div>
          <h3>Fast & Secure Access</h3>
          <span>Beautiful UI with secure authentication system</span>
        </div>
      </div>

    </div>

  </div>

  <!-- REGISTER CARD -->
  <div class="register-card">

    <div class="card-header">
      <div class="card-icon">🚀</div>
      <h2>Create Account</h2>
      <p>Start your AI journey with HeritechAI</p>
    </div>

    <?php if(isset($_SESSION['register_error'])): ?>

      <div class="error-msg">
        ⚠️ <?php echo $_SESSION['register_error']; unset($_SESSION['register_error']); ?>
      </div>

    <?php endif; ?>

    <form action="register_process.php" method="POST" id="regForm">

      <!-- NAME -->
      <label class="input-label">FULL NAME</label>

      <div class="input-group">
        <span class="inp-icon">👤</span>
        <input type="text"
               name="name"
               placeholder="Enter your full name"
               required>
      </div>

      <!-- EMAIL -->
      <label class="input-label">EMAIL ADDRESS</label>

      <div class="input-group">
        <span class="inp-icon">✉️</span>
        <input type="email"
               name="email"
               placeholder="your@email.com"
               required>
      </div>

      <!-- PASSWORD -->
      <label class="input-label">PASSWORD</label>

      <div class="input-group">

        <span class="inp-icon">🔒</span>

        <input type="password"
               name="password"
               id="password"
               placeholder="Create strong password"
               required
               oninput="checkStrength(this.value)">

        <button type="button"
                class="toggle-btn"
                onclick="togglePass()">👁</button>

      </div>

      <!-- STRENGTH -->
      <div class="strength-bar">
        <div class="strength-seg" id="s1"></div>
        <div class="strength-seg" id="s2"></div>
        <div class="strength-seg" id="s3"></div>
        <div class="strength-seg" id="s4"></div>
      </div>

      <div class="strength-label" id="strengthLabel"></div>

      <!-- BUTTON -->
      <button type="submit"
              class="submit-btn"
              id="submitBtn">

        <span id="btnText">
          🚀 Create My Account
        </span>

      </button>

    </form>

    <div class="divider">OR</div>

    <div class="footer-text">
      Already have an account?
      <a href="login.php">Sign In →</a>
    </div>

    <div class="terms">
      By registering, you agree to our community guidelines.<br>
      Your content may be reviewed before publishing.
    </div>

  </div>

</div>

<footer>
  © <?php echo date("Y"); ?> HeritechAI — Built with ❤️ in India
</footer>

<script>

/* ───────────────── PARTICLES ───────────────── */
const particleBox = document.getElementById('particles');

const colors = [
  '#00e0ff',
  '#00ffa6',
  '#a78bfa',
  '#f472b6'
];

for(let i=0;i<35;i++){

  const p = document.createElement('div');

  const size = Math.random()*4+2;

  const color = colors[Math.floor(Math.random()*colors.length)];

  p.classList.add('particle');

  p.style.width = size+'px';
  p.style.height = size+'px';

  p.style.background = color;

  p.style.left = Math.random()*100+'%';

  p.style.animationDuration = (Math.random()*10+8)+'s';

  p.style.animationDelay = Math.random()*8+'s';

  p.style.boxShadow = `0 0 10px ${color}`;

  particleBox.appendChild(p);
}

/* ───────────────── PASSWORD TOGGLE ───────────────── */
function togglePass(){

  const pass = document.getElementById('password');

  if(pass.type === 'password'){
    pass.type = 'text';
  }else{
    pass.type = 'password';
  }
}

/* ───────────────── PASSWORD STRENGTH ───────────────── */
function checkStrength(val){

  const segs = [
    document.getElementById('s1'),
    document.getElementById('s2'),
    document.getElementById('s3'),
    document.getElementById('s4')
  ];

  const lbl = document.getElementById('strengthLabel');

  segs.forEach(seg=>{
    seg.className='strength-seg';
  });

  let score = 0;

  if(val.length >= 6) score++;
  if(val.length >= 8) score++;
  if(/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
  if(/[^A-Za-z0-9]/.test(val)) score++;

  const levels = ['','weak','medium','strong','strong'];
  const texts = ['','Weak 🔴','Fair 🟡','Good 🟢','Strong 💪'];

  for(let i=0;i<score;i++){
    segs[i].classList.add(levels[score]);
  }

  lbl.innerHTML = texts[score];
}

/* ───────────────── SUBMIT LOADING ───────────────── */
document.getElementById('regForm').addEventListener('submit',function(){

  document.getElementById('btnText').innerHTML =
  '⏳ Creating Account...';

});

</script>

</body>
</html>