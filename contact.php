<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | AI Community</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

:root{
  --cyan:#00e0ff;
  --green:#00ffa6;
  --purple:#a78bfa;
  --pink:#f472b6;
  --dark:#07111f;
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  min-height:100vh;
  background:#07111f;
  color:white;
  overflow-x:hidden;
  display:flex;
  flex-direction:column;
  position:relative;
}

/* ───────────────── BACKGROUND IMAGE ───────────────── */

body::before{
  content:'';
  position:fixed;
  inset:0;
  background:
    linear-gradient(rgba(7,17,31,.88), rgba(7,17,31,.92)),
    url('https://images.unsplash.com/photo-1524492412937-b28074a5d7da?q=80&w=1600&auto=format&fit=crop');
  background-size:cover;
  background-position:center;
  z-index:-3;
}

/* ───────────────── ORBS ───────────────── */

.bg-orbs{
  position:fixed;
  inset:0;
  z-index:-2;
  pointer-events:none;
  overflow:hidden;
}

.orb{
  position:absolute;
  border-radius:50%;
  filter:blur(120px);
  opacity:.14;
  animation:orbFloat 15s infinite ease-in-out;
}

.orb1{
  width:520px;
  height:520px;
  background:#00e0ff;
  top:-180px;
  left:-150px;
}

.orb2{
  width:420px;
  height:420px;
  background:#a78bfa;
  bottom:-120px;
  right:-120px;
  animation-delay:5s;
}

.orb3{
  width:300px;
  height:300px;
  background:#f472b6;
  top:45%;
  left:50%;
  animation-delay:9s;
}

@keyframes orbFloat{
  0%,100%{transform:translate(0,0);}
  33%{transform:translate(40px,-30px);}
  66%{transform:translate(-25px,25px);}
}

/* ───────────────── PARTICLES ───────────────── */

.particle{
  position:fixed;
  border-radius:50%;
  pointer-events:none;
  animation:particleFloat linear infinite;
  opacity:0;
}

@keyframes particleFloat{
  0%{
    transform:translateY(100vh);
    opacity:0;
  }
  10%{opacity:.55;}
  90%{opacity:.2;}
  100%{
    transform:translateY(-50px);
    opacity:0;
  }
}

/* ───────────────── NAVBAR ───────────────── */

nav{
  position:fixed;
  top:0;
  left:0;
  right:0;
  z-index:1000;

  display:flex;
  justify-content:space-between;
  align-items:center;

  padding:16px 80px;

  background:rgba(8,15,30,.72);
  backdrop-filter:blur(18px);

  border-bottom:1px solid rgba(255,255,255,.06);
}

.logo{
  font-size:24px;
  font-weight:800;
  text-decoration:none;
  color:white;
}

.logo span{
  color:var(--cyan);
}

.nav-links{
  display:flex;
  align-items:center;
  gap:8px;
  list-style:none;
}

.nav-links a{
  text-decoration:none;
  color:rgba(255,255,255,.72);
  font-size:14px;
  font-weight:500;
  padding:8px 16px;
  border-radius:30px;
  transition:.3s;
}

.nav-links a:hover{
  color:white;
  background:rgba(255,255,255,.08);
}

.nav-links .btn-nav{
  background:linear-gradient(90deg,var(--cyan),var(--green));
  color:#000 !important;
  font-weight:700;
}

.nav-links .btn-nav:hover{
  transform:translateY(-2px);
  box-shadow:0 0 20px rgba(0,224,255,.4);
}

.nav-links .btn-admin{
  background:linear-gradient(90deg,#a78bfa,#f472b6);
  color:#000 !important;
}

/* ───────────────── MAIN ───────────────── */

.main{
  position:relative;
  z-index:1;

  flex:1;

  display:flex;
  align-items:center;
  justify-content:center;

  gap:90px;

  padding:120px 25px 70px;
}

/* ───────────────── LEFT PANEL ───────────────── */

.left-panel{
  max-width:480px;
  animation:fadeLeft .8s ease both;
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

.left-tag{
  display:inline-flex;
  align-items:center;
  gap:8px;

  padding:7px 18px;

  border-radius:30px;

  background:rgba(167,139,250,.09);
  border:1px solid rgba(167,139,250,.22);

  color:var(--purple);
  font-size:12px;

  margin-bottom:28px;
}

.left-tag .dot{
  width:7px;
  height:7px;
  border-radius:50%;
  background:var(--purple);
  animation:pulse 2s infinite;
}

@keyframes pulse{
  0%,100%{
    opacity:1;
    transform:scale(1);
  }
  50%{
    opacity:.4;
    transform:scale(1.5);
  }
}

.left-panel h1{
  font-size:58px;
  font-weight:800;
  line-height:1.12;
  margin-bottom:20px;
}

.left-panel .grad{
  background:linear-gradient(90deg,var(--purple),var(--cyan),var(--green));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-size:200%;
  animation:gradShift 5s linear infinite;
}

@keyframes gradShift{
  0%{background-position:0%;}
  100%{background-position:200%;}
}

.left-panel p{
  font-size:16px;
  line-height:1.9;
  opacity:.68;
  margin-bottom:35px;
}

/* ───────────────── CONTACT ITEMS ───────────────── */

.contact-info{
  display:flex;
  flex-direction:column;
  gap:14px;
}

.contact-item{
  display:flex;
  align-items:center;
  gap:14px;

  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.08);

  padding:16px 18px;
  border-radius:18px;

  backdrop-filter:blur(10px);

  transition:.3s;
}

.contact-item:hover{
  transform:translateX(6px);
  border-color:rgba(167,139,250,.3);
  background:rgba(167,139,250,.06);
}

.contact-icon{
  width:48px;
  height:48px;
  border-radius:14px;

  display:flex;
  align-items:center;
  justify-content:center;

  font-size:20px;

  background:rgba(255,255,255,.07);
}

.contact-text strong{
  display:block;
  font-size:14px;
  margin-bottom:2px;
}

.contact-text span,
.contact-text a{
  font-size:12px;
  opacity:.58;
  color:white;
  text-decoration:none;
}

.contact-text a:hover{
  color:var(--cyan);
}

/* ───────────────── MAP ───────────────── */

.map-wrap{
  margin-top:20px;
  overflow:hidden;
  border-radius:20px;
  border:1px solid rgba(255,255,255,.08);
  position:relative;
}

.map-wrap iframe{
  width:100%;
  height:160px;
  border:0;
  filter:grayscale(1) brightness(.7);
}

.map-tag{
  position:absolute;
  bottom:10px;
  left:50%;
  transform:translateX(-50%);

  background:rgba(10,22,40,.85);

  border:1px solid rgba(255,255,255,.1);

  padding:5px 14px;
  border-radius:20px;

  font-size:11px;
  font-weight:600;
}

/* ───────────────── SOCIAL ───────────────── */

.social-row{
  display:flex;
  gap:10px;
  margin-top:18px;
}

.social-link{
  flex:1;

  text-decoration:none;

  display:flex;
  align-items:center;
  justify-content:center;
  gap:7px;

  padding:11px 10px;

  border-radius:14px;

  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.08);

  color:rgba(255,255,255,.5);
  font-size:12px;
  font-weight:600;

  transition:.3s;
}

.social-link:hover{
  color:var(--cyan);
  border-color:rgba(0,224,255,.25);
  transform:translateY(-2px);
}

/* ───────────────── CONTACT CARD ───────────────── */

.contact-card{
  width:460px;

  background:rgba(255,255,255,.06);

  border:1px solid rgba(255,255,255,.08);

  border-radius:30px;

  padding:44px 40px;

  backdrop-filter:blur(24px);

  box-shadow:0 30px 80px rgba(0,0,0,.55);

  animation:fadeRight .8s ease both;
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
  margin-bottom:30px;
}

.icon-wrap{
  width:74px;
  height:74px;

  margin:0 auto 18px;

  border-radius:22px;

  display:flex;
  align-items:center;
  justify-content:center;

  font-size:32px;

  background:linear-gradient(135deg,
  rgba(167,139,250,.15),
  rgba(0,224,255,.08));

  border:1px solid rgba(167,139,250,.22);
}

.card-header h2{
  font-size:28px;
  font-weight:800;
  margin-bottom:6px;
}

.card-header p{
  font-size:13px;
  opacity:.5;
}

/* ───────────────── SUCCESS ───────────────── */

.success-msg{
  background:rgba(0,255,166,.1);
  border:1px solid rgba(0,255,166,.25);

  color:#00ffa6;

  padding:13px 16px;
  border-radius:14px;

  margin-bottom:22px;

  font-size:13px;
}

/* ───────────────── INPUTS ───────────────── */

.row-2{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:14px;
}

.input-label{
  display:block;
  font-size:12px;
  font-weight:600;
  letter-spacing:.5px;
  color:rgba(255,255,255,.5);

  margin-bottom:8px;
}

.input-group{
  position:relative;
  margin-bottom:18px;
}

.inp-icon{
  position:absolute;
  left:16px;
  top:50%;
  transform:translateY(-50%);
  font-size:16px;
}

.area-group .inp-icon{
  top:18px;
  transform:none;
}

.input-group input,
.input-group textarea{
  width:100%;

  padding:15px 16px 15px 48px;

  background:rgba(255,255,255,.06);

  border:1px solid rgba(255,255,255,.1);

  border-radius:15px;

  color:white;
  font-size:14px;

  outline:none;

  transition:.3s;
}

.input-group textarea{
  min-height:130px;
  resize:vertical;
  line-height:1.7;
}

.input-group input::placeholder,
.input-group textarea::placeholder{
  color:rgba(255,255,255,.3);
}

.input-group input:focus,
.input-group textarea:focus{
  border-color:var(--purple);
  background:rgba(167,139,250,.06);
  box-shadow:0 0 0 3px rgba(167,139,250,.08);
}

/* ───────────────── PILLS ───────────────── */

.pills{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  margin-bottom:20px;
}

.pill{
  padding:7px 14px;

  border-radius:30px;

  background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.08);

  color:rgba(255,255,255,.5);

  font-size:12px;
  cursor:pointer;

  transition:.25s;
}

.pill:hover,
.pill.active{
  color:var(--cyan);
  border-color:rgba(0,224,255,.25);
  background:rgba(0,224,255,.08);
}

/* ───────────────── BUTTON ───────────────── */

.submit-btn{
  width:100%;

  padding:15px;

  border:none;
  border-radius:16px;

  background:linear-gradient(90deg,var(--purple),var(--cyan));

  color:white;

  font-size:16px;
  font-weight:800;

  cursor:pointer;

  transition:.35s;

  display:flex;
  align-items:center;
  justify-content:center;
  gap:8px;

  position:relative;
  overflow:hidden;
}

.submit-btn::before{
  content:'';
  position:absolute;
  inset:0;

  background:linear-gradient(
    90deg,
    transparent,
    rgba(255,255,255,.2),
    transparent
  );

  transform:translateX(-100%);
  transition:.7s;
}

.submit-btn:hover::before{
  transform:translateX(100%);
}

.submit-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 14px 35px rgba(167,139,250,.35);
}

/* ───────────────── FOOTER ───────────────── */

footer{
  position:relative;
  z-index:1;

  text-align:center;

  padding:22px;

  font-size:13px;
  opacity:.35;

  border-top:1px solid rgba(255,255,255,.05);
}

/* ───────────────── RESPONSIVE ───────────────── */

@media(max-width:980px){

  .left-panel{
    display:none;
  }

  .main{
    justify-content:center;
    padding:100px 18px 50px;
  }

  .contact-card{
    width:100%;
    max-width:460px;
  }

}

@media(max-width:480px){

  nav{
    padding:14px 18px;
  }

  .contact-card{
    padding:34px 24px;
  }

  .row-2{
    grid-template-columns:1fr;
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

<div id="particles"></div>

<!-- NAVBAR -->
<nav>

  <a href="index.php" class="logo">
    🤖 <span>HeritechAI</span>
  </a>

  <ul class="nav-links">

    <li><a href="index.php">Home</a></li>
    <li><a href="about.php">About Us</a></li>

    <?php if(isset($_SESSION['user_id'])): ?>

      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="submit.php">📤 Upload</a></li>

      <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <li><a href="admin/admin_panel.php" class="btn-nav btn-admin">⚙️ Admin</a></li>
      <?php endif; ?>

      <li><a href="logout.php">Logout</a></li>

    <?php else: ?>

      <li><a href="contact.php">Contact Us</a></li>
      <li><a href="login.php">Login</a></li>
      <li><a href="register.php" class="btn-nav">Register →</a></li>

    <?php endif; ?>

  </ul>

</nav>

<!-- MAIN -->
<div class="main">

  <!-- LEFT -->
  <div class="left-panel">

    <div class="left-tag">
      <span class="dot"></span>
      We'd Love to Hear From You
    </div>

    <h1>
      Let's Start a<br>
      <span class="grad">Conversation</span><br>
      Together 💬
    </h1>

    <p>
      Have a question, idea, or want to collaborate?
      We're always open to connecting with creators,
      researchers, and AI enthusiasts from around the world.
    </p>

    <!-- CONTACT ITEMS -->

    <div class="contact-info">

      <div class="contact-item">
        <div class="contact-icon">📍</div>
        <div class="contact-text">
          <strong>Our Location</strong>
          <span>Nagpur, Maharashtra, India</span>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon">📞</div>
        <div class="contact-text">
          <strong>Phone Number</strong>
          <a href="tel:+919023112909">+91 90231 12909</a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon">📧</div>
        <div class="contact-text">
          <strong>Email Address</strong>
          <a href="mailto:embedded.creations@gmail.com">
            embedded.creations@gmail.com
          </a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon">⏱️</div>
        <div class="contact-text">
          <strong>Response Time</strong>
          <span>Usually within 24 hours</span>
        </div>
      </div>

    </div>

    <!-- MAP -->

    <div class="map-wrap">

      <iframe
        src="https://maps.google.com/maps?q=Nagpur&t=&z=13&ie=UTF8&iwloc=&output=embed"
        allowfullscreen
        loading="lazy">
      </iframe>

      <div class="map-tag">📍 Nagpur, MH</div>

    </div>

    <!-- SOCIAL -->

    <div class="social-row">

      <a href="#" class="social-link">🐦 Twitter</a>

      <a href="#" class="social-link">💼 LinkedIn</a>

      <a href="#" class="social-link">🐙 GitHub</a>

    </div>

  </div>

  <!-- CONTACT CARD -->

  <div class="contact-card">

    <div class="card-header">

      <div class="icon-wrap">✉️</div>

      <h2>Send a Message</h2>

      <p>
        Fill out the form and we'll get back to you
      </p>

    </div>

    <?php if(isset($_GET['sent']) && $_GET['sent']==1): ?>

      <div class="success-msg">
        ✅ Message sent! We'll get back to you soon.
      </div>

    <?php endif; ?>

    <!-- FORM -->

    <form action="contact_process.php" method="POST" id="contactForm">

      <!-- ROW -->

      <div class="row-2">

        <div>

          <label class="input-label">FULL NAME</label>

          <div class="input-group">

            <span class="inp-icon">👤</span>

            <input
              type="text"
              name="name"
              placeholder="John Doe"
              required
              autocomplete="name">

          </div>

        </div>

        <div>

          <label class="input-label">EMAIL ADDRESS</label>

          <div class="input-group">

            <span class="inp-icon">✉️</span>

            <input
              type="email"
              name="email"
              placeholder="you@email.com"
              required
              autocomplete="email">

          </div>

        </div>

      </div>

      <!-- SUBJECT -->

      <div>

        <label class="input-label">SUBJECT</label>

        <div class="input-group">

          <span class="inp-icon">💬</span>

          <input
            type="text"
            name="subject"
            id="subjectInput"
            placeholder="What's this about?"
            required>

        </div>

      </div>

      <!-- PILLS -->

      <div class="pills" id="pills">

        <div class="pill" data-val="General Inquiry">General Inquiry</div>

        <div class="pill" data-val="Partnership">Partnership</div>

        <div class="pill" data-val="Bug Report">Bug Report</div>

        <div class="pill" data-val="Content Request">Content Request</div>

        <div class="pill" data-val="Other">Other</div>

      </div>

      <!-- MESSAGE -->

      <div>

        <label class="input-label">MESSAGE</label>

        <div class="input-group area-group">

          <span class="inp-icon">📝</span>

          <textarea
            name="message"
            placeholder="Tell us everything — the more detail, the better we can help…"
            required></textarea>

        </div>

      </div>

      <!-- BUTTON -->

      <button type="submit" class="submit-btn" id="submitBtn">

        <span id="btnText">📨 Send Message</span>

      </button>

    </form>

  </div>

</div>

<!-- FOOTER -->

<footer>
  © <?php echo date("Y"); ?> AI Community — Built for Innovators 🤖
</footer>

<script>

/* PARTICLES */

const pc = document.getElementById('particles');

const colors = [
  '#00e0ff',
  '#00ffa6',
  '#a78bfa',
  '#f472b6'
];

for(let i=0;i<25;i++){

  const p = document.createElement('div');

  const sz = Math.random()*3 + 2;

  const col = colors[Math.floor(Math.random()*colors.length)];

  p.className='particle';

  p.style.cssText = `
    width:${sz}px;
    height:${sz}px;
    background:${col};
    left:${Math.random()*100}%;

    animation-duration:${Math.random()*12+8}s;
    animation-delay:${Math.random()*8}s;

    box-shadow:0 0 6px ${col};
  `;

  pc.appendChild(p);

}

/* PILLS */

document.querySelectorAll('.pill').forEach(pill => {

  pill.addEventListener('click', () => {

    document.querySelectorAll('.pill')
      .forEach(p => p.classList.remove('active'));

    pill.classList.add('active');

    document.getElementById('subjectInput').value =
      pill.dataset.val;

  });

});

/* BUTTON ANIMATION */

document.getElementById('contactForm')
.addEventListener('submit', function(){

  const btn = document.getElementById('submitBtn');

  const txt = document.getElementById('btnText');

  btn.style.opacity = '.85';

  txt.textContent = '⏳ Sending...';

});

</script>

</body>
</html>