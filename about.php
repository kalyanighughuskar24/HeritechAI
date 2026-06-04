<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us | AI Community</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --cyan:#00e0ff;
  --green:#00ffa6;
  --purple:#a78bfa;
  --pink:#f472b6;
  --dark:#0a1628;
  --card-bg:rgba(255,255,255,0.06);
}
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
html{scroll-behavior:smooth;}
body{background:#0a1628;color:white;overflow-x:hidden;}

/* ── ANIMATED BG ORBS ── */
.bg-orbs{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;}
.orb{position:absolute;border-radius:50%;filter:blur(110px);opacity:.13;animation:orbFloat 14s infinite ease-in-out;}
.orb1{width:550px;height:550px;background:#00e0ff;top:-150px;left:-120px;animation-delay:0s;}
.orb2{width:420px;height:420px;background:#a78bfa;top:35%;right:-120px;animation-delay:4s;}
.orb3{width:380px;height:380px;background:#00ffa6;bottom:-120px;left:25%;animation-delay:8s;}
.orb4{width:300px;height:300px;background:#f472b6;top:60%;left:10%;animation-delay:2s;}
@keyframes orbFloat{
  0%,100%{transform:translate(0,0) scale(1);}
  33%{transform:translate(40px,-40px) scale(1.05);}
  66%{transform:translate(-25px,25px) scale(.95);}
}

/* ── NAVBAR ── */
nav{
  position:fixed;top:0;left:0;right:0;z-index:1000;
  display:flex;justify-content:space-between;align-items:center;
  padding:16px 80px;
  background:rgba(10,22,40,0.8);
  backdrop-filter:blur(20px);
  border-bottom:1px solid rgba(255,255,255,0.06);
  transition:.3s;
}

.logo{font-size:22px;font-weight:700;}
.logo span{color:#00e0ff;}

.nav-links{display:flex;align-items:center;gap:8px;list-style:none;}
.nav-links a{
  text-decoration:none;color:rgba(255,255,255,0.75);font-size:14px;font-weight:500;
  padding:8px 16px;border-radius:20px;transition:.3s;
}
.nav-links a:hover{color:white;background:rgba(255,255,255,0.08);}
.nav-links .btn-nav{
  background:linear-gradient(90deg,var(--cyan),var(--green));
  color:#000!important;font-weight:700;border-radius:20px;
}
.nav-links .btn-nav:hover{transform:scale(1.05);box-shadow:0 0 20px rgba(0,224,255,0.4);}
.nav-links .btn-admin{
  background:linear-gradient(90deg,#a78bfa,#f472b6);
  color:#000!important;font-weight:700;
}

/* ── PAGE WRAPPER ── */
.page{position:relative;z-index:1;}

/* ── HERO ── */
.hero{
  min-height:100vh;
  display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  text-align:center;padding:120px 20px 80px;
  position:relative;
}
.hero-tag{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(0,224,255,0.08);border:1px solid rgba(0,224,255,0.25);
  padding:7px 20px;border-radius:30px;font-size:13px;color:var(--cyan);
  margin-bottom:30px;animation:fadeUp .8s ease;
}
.hero-tag .dot{
  width:8px;height:8px;border-radius:50%;background:var(--cyan);
  animation:pulse 2s infinite;
}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(1.5);}}

.hero h1{
  font-size:74px;font-weight:800;line-height:1.1;margin-bottom:26px;
  animation:fadeUp .8s .1s ease both;
}
.hero h1 .grad{
  background:linear-gradient(90deg,var(--cyan),var(--green),var(--purple),var(--pink));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-size:300%;animation:gradShift 6s linear infinite;
}
@keyframes gradShift{0%{background-position:0%;}100%{background-position:300%;}}

.hero p{
  font-size:20px;max-width:650px;opacity:.7;line-height:1.75;
  margin-bottom:44px;animation:fadeUp .8s .2s ease both;
}
.hero-btns{
  display:flex;gap:14px;flex-wrap:wrap;justify-content:center;
  animation:fadeUp .8s .3s ease both;
}
.btn-hero{
  padding:14px 36px;border-radius:30px;font-size:15px;font-weight:700;
  text-decoration:none;transition:.35s;display:inline-flex;align-items:center;gap:8px;
}
.btn-primary{
  background:linear-gradient(90deg,var(--cyan),var(--green));color:#000;
  box-shadow:0 0 30px rgba(0,224,255,0.25);
}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 12px 40px rgba(0,224,255,0.45);}
.btn-outline{
  border:1px solid rgba(255,255,255,0.18);color:white;
  background:rgba(255,255,255,0.05);
}
.btn-outline:hover{border-color:var(--cyan);color:var(--cyan);transform:translateY(-3px);}

/* Floating particles */
.particle{
  position:absolute;border-radius:50%;pointer-events:none;
  animation:particleFloat linear infinite;opacity:0;
}
@keyframes particleFloat{
  0%{transform:translateY(100vh) scale(0);opacity:0;}
  10%{opacity:.6;}
  90%{opacity:.3;}
  100%{transform:translateY(-20px) scale(1);opacity:0;}
}

/* Scroll hint */
.scroll-hint{margin-top:60px;display:flex;flex-direction:column;align-items:center;gap:8px;opacity:.35;font-size:12px;animation:fadeUp .8s .5s ease both;}
.scroll-line{width:1px;height:50px;background:linear-gradient(to bottom,var(--cyan),transparent);animation:scrollAnim 2s infinite;}
@keyframes scrollAnim{0%{transform:scaleY(0);transform-origin:top;}50%{transform:scaleY(1);}100%{transform:scaleY(0);transform-origin:bottom;}}
@keyframes fadeUp{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}

/* ── SECTION BASE ── */
.section{padding:100px 80px;position:relative;}
.section-label{
  font-size:11px;font-weight:700;letter-spacing:4px;
  color:var(--cyan);text-transform:uppercase;margin-bottom:14px;
}
.section-title{font-size:44px;font-weight:800;line-height:1.2;margin-bottom:16px;}
.section-sub{font-size:16px;opacity:.6;max-width:560px;line-height:1.75;}

/* ── STORY / ABOUT ── */
.story-section{
  padding:120px 80px;
  display:grid;grid-template-columns:1fr 1fr;
  gap:80px;align-items:center;
  background:rgba(255,255,255,0.02);
  border-top:1px solid rgba(255,255,255,0.05);
  border-bottom:1px solid rgba(255,255,255,0.05);
}
.story-visual{position:relative;}
.story-main-card{
  background:linear-gradient(135deg,rgba(0,224,255,0.1),rgba(167,139,250,0.08));
  border:1px solid rgba(0,224,255,0.2);
  border-radius:24px;padding:48px;text-align:center;
  position:relative;overflow:hidden;
}
.story-main-card::before{
  content:'';position:absolute;inset:-1px;border-radius:24px;
  background:linear-gradient(135deg,rgba(0,224,255,0.15),rgba(0,255,166,0.05),rgba(167,139,250,0.1));
  opacity:0;transition:.4s;
}
.story-main-card:hover::before{opacity:1;}
.story-main-card .big-icon{font-size:90px;display:block;margin-bottom:20px;animation:iconPop 3s ease-in-out infinite;}
@keyframes iconPop{0%,100%{transform:scale(1);}50%{transform:scale(1.08) rotate(3deg);}}
.story-main-card h3{font-size:22px;font-weight:700;margin-bottom:10px;}
.story-main-card p{opacity:.6;font-size:14px;line-height:1.7;}

.floating-badge{
  position:absolute;background:rgba(10,22,40,0.95);
  border:1px solid rgba(0,224,255,0.3);border-radius:14px;
  padding:12px 18px;font-size:13px;font-weight:600;
  display:flex;align-items:center;gap:8px;
  animation:badgeFloat 4s ease-in-out infinite;
  backdrop-filter:blur(10px);white-space:nowrap;
}
.badge-1{top:-20px;right:-20px;animation-delay:0s;}
.badge-2{bottom:-20px;left:-20px;animation-delay:2s;}
@keyframes badgeFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.badge-dot{width:8px;height:8px;border-radius:50%;background:var(--green);}

.story-text{}
.story-text h2{font-size:42px;font-weight:800;line-height:1.2;margin-bottom:20px;}
.story-text h2 span{
  background:linear-gradient(90deg,var(--cyan),var(--green));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.story-text p{font-size:16px;opacity:.7;line-height:1.85;margin-bottom:16px;}
.story-stats{
  display:grid;grid-template-columns:repeat(3,1fr);
  gap:16px;margin-top:32px;
}
.s-stat{
  background:var(--card-bg);border:1px solid rgba(255,255,255,0.08);
  border-radius:14px;padding:18px;text-align:center;
}
.s-stat .n{
  font-size:28px;font-weight:800;display:block;
  background:linear-gradient(90deg,var(--cyan),var(--green));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.s-stat .l{font-size:12px;opacity:.5;margin-top:4px;}

/* ── MISSION ── */
.mission-section{padding:100px 80px;}
.mission-grid{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:20px;margin-top:60px;
}
.mission-card{
  background:var(--card-bg);
  border:1px solid rgba(255,255,255,0.07);
  border-radius:20px;padding:32px 24px;
  text-align:center;transition:.4s;
  position:relative;overflow:hidden;
}
.mission-card::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,var(--cyan),var(--green));
  transform:scaleX(0);transition:.4s;transform-origin:left;
}
.mission-card:hover{
  transform:translateY(-10px);
  border-color:rgba(0,224,255,0.25);
  background:rgba(0,224,255,0.06);
}
.mission-card:hover::after{transform:scaleX(1);}
.m-icon{
  font-size:40px;margin-bottom:20px;display:block;
  width:72px;height:72px;line-height:72px;text-align:center;
  border-radius:18px;margin:0 auto 20px;
}
.m-icon.c1{background:rgba(0,224,255,0.1);}
.m-icon.c2{background:rgba(0,255,166,0.1);}
.m-icon.c3{background:rgba(167,139,250,0.1);}
.m-icon.c4{background:rgba(244,114,182,0.1);}
.mission-card h3{font-size:17px;font-weight:700;margin-bottom:10px;}
.mission-card p{font-size:13px;opacity:.6;line-height:1.7;}

/* ── VISION ── */
.vision-section{
  padding:100px 80px;
  background:rgba(255,255,255,0.02);
  border-top:1px solid rgba(255,255,255,0.05);
  border-bottom:1px solid rgba(255,255,255,0.05);
}
.vision-grid{
  display:grid;grid-template-columns:repeat(2,1fr);
  gap:24px;margin-top:60px;
}
.vision-card{
  background:var(--card-bg);border:1px solid rgba(255,255,255,0.07);
  border-radius:20px;padding:36px;
  display:flex;gap:24px;align-items:flex-start;transition:.4s;
}
.vision-card:hover{
  transform:translateX(6px);
  border-color:rgba(0,224,255,0.25);
  background:rgba(0,224,255,0.04);
}
.v-icon{
  font-size:32px;min-width:60px;height:60px;
  display:flex;align-items:center;justify-content:center;
  border-radius:16px;background:rgba(0,224,255,0.1);
  flex-shrink:0;
}
.v-text h3{font-size:17px;font-weight:700;margin-bottom:8px;}
.v-text p{font-size:14px;opacity:.6;line-height:1.7;}

/* ── TEAM ── */
.team-section{padding:100px 80px;text-align:center;}
.team-grid{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:24px;margin-top:60px;
}
.team-card{
  background:var(--card-bg);border:1px solid rgba(255,255,255,0.07);
  border-radius:22px;padding:36px 24px;
  transition:.4s;position:relative;overflow:hidden;
}
.team-card::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(0,224,255,0.06),rgba(167,139,250,0.04));
  opacity:0;transition:.4s;
}
.team-card:hover{transform:translateY(-10px);border-color:rgba(0,224,255,0.25);}
.team-card:hover::before{opacity:1;}
.team-avatar{
  width:80px;height:80px;border-radius:50%;margin:0 auto 20px;
  background:linear-gradient(135deg,var(--cyan),var(--purple));
  display:flex;align-items:center;justify-content:center;
  font-size:32px;border:2px solid rgba(0,224,255,0.3);
}
.team-card h4{font-size:16px;font-weight:700;margin-bottom:6px;}
.team-card .role{
  font-size:12px;color:var(--cyan);font-weight:600;
  letter-spacing:1px;text-transform:uppercase;margin-bottom:12px;
  display:block;
}
.team-card p{font-size:13px;opacity:.5;line-height:1.6;}
.team-social{
  display:flex;justify-content:center;gap:10px;margin-top:18px;
}
.team-social a{
  width:34px;height:34px;border-radius:10px;
  background:rgba(255,255,255,0.08);display:flex;
  align-items:center;justify-content:center;
  font-size:14px;text-decoration:none;transition:.3s;
}
.team-social a:hover{background:rgba(0,224,255,0.15);transform:scale(1.1);}

/* ── CTA ── */
.cta-section{
  padding:120px 20px;text-align:center;
  background:linear-gradient(135deg,rgba(0,224,255,0.04),rgba(167,139,250,0.04));
  border-top:1px solid rgba(255,255,255,0.05);
}
.cta-section h2{font-size:50px;font-weight:800;margin-bottom:20px;line-height:1.2;}
.cta-section p{font-size:18px;opacity:.6;margin-bottom:44px;max-width:500px;margin-left:auto;margin-right:auto;}

/* ── FOOTER ── */
footer{
  position:relative;z-index:1;text-align:center;padding:30px;
  border-top:1px solid rgba(255,255,255,0.06);font-size:14px;opacity:.4;
}

/* ── RESPONSIVE ── */
@media(max-width:1100px){
  .story-section{grid-template-columns:1fr;gap:50px;padding:80px 40px;}
  .mission-grid{grid-template-columns:repeat(2,1fr);}
  .team-grid{grid-template-columns:repeat(2,1fr);}
  .vision-grid{grid-template-columns:1fr;}
  .section,.mission-section,.vision-section,.team-section{padding:80px 40px;}
}
@media(max-width:768px){
  nav{padding:14px 20px;}
  nav ul{display:none;}
  .hero h1{font-size:42px;}
  .hero p{font-size:16px;}
  .mission-grid{grid-template-columns:1fr;}
  .team-grid{grid-template-columns:1fr 1fr;}
  .story-stats{grid-template-columns:1fr;}
  .section,.mission-section,.vision-section,.team-section{padding:60px 20px;}
}
</style>
</head>
<body>

<!-- ANIMATED BG -->
<div class="bg-orbs">
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>
  <div class="orb orb4"></div>
</div>

<!-- PARTICLES (JS generated) -->
<div id="particles" style="position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;"></div>

<!-- NAVBAR -->
<nav>
  <div class="logo">🤖 HeritechAI</div>
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
      <li><a href="register.php" class="btn-nav">Register Now →</a></li>
    <?php endif; ?>
  </ul>
</nav>

<div class="page">

<!-- ══════════════════════════════════════
     HERO
══════════════════════════════════════ -->
<section class="hero">
  <div class="hero-tag"><span class="dot"></span> Our Story & Vision</div>
  <h1>Building the Future<br>of <span class="grad">AI Together</span></h1>
  <p>We're a passionate community of innovators, developers, and dreamers — united by the belief that AI should be accessible, collaborative, and transformative for everyone.</p>
  <div class="hero-btns">
    <a href="register.php" class="btn-hero btn-primary">🚀 Join the Community</a>
    <a href="contact.php" class="btn-hero btn-outline">Get in Touch →</a>
  </div>
  <div class="scroll-hint">
    <div class="scroll-line"></div>
    Scroll to explore
  </div>
</section>

<!-- ══════════════════════════════════════
     STORY / ABOUT
══════════════════════════════════════ -->
<section class="story-section">
  <div class="story-visual">
    <div class="story-main-card">
      <span class="big-icon">🤖</span>
      <h3>AI-Powered Platform</h3>
      <p>Built by the community, for the community. A space where AI innovation thrives every day.</p>
    </div>
    <div class="floating-badge badge-1">
      <div class="badge-dot"></div>
      ✅ Admin Reviewed Content
    </div>
    <div class="floating-badge badge-2">
      <span>🌍</span> Global AI Network
    </div>
  </div>

  <div class="story-text">
    <div class="section-label">Who We Are</div>
    <h2>We're <span>AI Enthusiasts</span><br>on a Mission</h2>
    <p>Welcome to AI Community — an AI-powered content platform designed to turn your ideas into stunning digital realities. We built this space for creators, researchers, and developers who want to share their AI work with the world.</p>
    <p>Our platform helps transform imagination into reality within seconds. Every piece of content is reviewed by our dedicated admin team to maintain the highest quality standards before it goes live on the homepage.</p>
    <p>Whether you're sharing an AI-generated image, a research paper, a demo video, or an audio project — this is your stage. 🎨🧠🚀</p>

    <div class="story-stats">
      <div class="s-stat">
        <span class="n">500+</span>
        <div class="l">👥 Members</div>
      </div>
      <div class="s-stat">
        <span class="n">120+</span>
        <div class="l">📁 Projects</div>
      </div>
      <div class="s-stat">
        <span class="n">50+</span>
        <div class="l">🧠 Experts</div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════
     MISSION
══════════════════════════════════════ -->
<section class="mission-section">
  <div style="text-align:center;">
    <div class="section-label" style="text-align:center;">What Drives Us</div>
    <div class="section-title" style="text-align:center;">Our Mission 🌍</div>
    <div class="section-sub" style="margin:0 auto;text-align:center;">
      We're committed to making AI innovation accessible, collaborative, and impactful for everyone on the planet.
    </div>
  </div>
  <div class="mission-grid">
    <div class="mission-card">
      <span class="m-icon c1">💡</span>
      <h3>Innovation First</h3>
      <p>We push the boundaries of what's possible with AI — inspiring new ideas and cutting-edge solutions every day.</p>
    </div>
    <div class="mission-card">
      <span class="m-icon c2">🤝</span>
      <h3>Global Collaboration</h3>
      <p>Connecting developers, researchers, and creators worldwide to build something bigger than any one person can alone.</p>
    </div>
    <div class="mission-card">
      <span class="m-icon c3">📚</span>
      <h3>Open Learning</h3>
      <p>Making AI education simple, free, and accessible for everyone — from beginners to experts.</p>
    </div>
    <div class="mission-card">
      <span class="m-icon c4">🌐</span>
      <h3>Strong Community</h3>
      <p>Building a thriving, supportive network where every voice matters and every idea gets heard.</p>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════
     VISION
══════════════════════════════════════ -->
<section class="vision-section">
  <div style="text-align:center;margin-bottom:0;">
    <div class="section-label" style="text-align:center;">Where We're Headed</div>
    <div class="section-title" style="text-align:center;">Our Vision 🚀</div>
    <div class="section-sub" style="margin:0 auto 60px;text-align:center;">
      A future where AI creation is universal, effortless, and celebrated by communities across the globe.
    </div>
  </div>
  <div class="vision-grid">
    <div class="vision-card">
      <div class="v-icon">🌍</div>
      <div class="v-text">
        <h3>A Creative AI Future</h3>
        <p>We envision a world where everyone — regardless of background or technical skill — can harness the power of AI to create and share remarkable content.</p>
      </div>
    </div>
    <div class="vision-card">
      <div class="v-icon">⚡</div>
      <div class="v-text">
        <h3>Ideas into Reality, Instantly</h3>
        <p>Our platform will continue to evolve so that turning any idea into a polished, shareable AI creation takes seconds, not days.</p>
      </div>
    </div>
    <div class="vision-card">
      <div class="v-icon">🎓</div>
      <div class="v-text">
        <h3>AI for Everybody</h3>
        <p>We believe technology should never be a barrier. Our vision is a platform where anyone can learn, create, and grow with AI — for free.</p>
      </div>
    </div>
    <div class="vision-card">
      <div class="v-icon">🤖</div>
      <div class="v-text">
        <h3>Global Creator Network</h3>
        <p>Building bridges across countries and cultures, connecting AI creators everywhere into one powerful, collaborative network.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════
     TEAM
══════════════════════════════════════ -->
<section class="team-section">
  <div class="section-label">The People Behind It</div>
  <div class="section-title">Meet Our Team 👩‍💻</div>
  <div class="section-sub" style="margin:0 auto;">
    A passionate group of builders who eat, sleep, and breathe AI innovation.
  </div>

  <div class="team-grid">
    <div class="team-card">
      <div class="team-avatar">👨‍💼</div>
      <h4>Your Name</h4>
      <span class="role">Founder & CEO</span>
      <p>Visionary leader driving the AI Community's mission and long-term strategy.</p>
      <div class="team-social">
        <a href="#">🔗</a>
        <a href="#">🐦</a>
        <a href="#">💼</a>
      </div>
    </div>
    <div class="team-card">
      <div class="team-avatar" style="background:linear-gradient(135deg,var(--green),var(--cyan));">👩‍💻</div>
      <h4>Your Name</h4>
      <span class="role">Lead Developer</span>
      <p>Full-stack wizard building the technology that powers everything you see.</p>
      <div class="team-social">
        <a href="#">🔗</a>
        <a href="#">🐦</a>
        <a href="#">💼</a>
      </div>
    </div>
    <div class="team-card">
      <div class="team-avatar" style="background:linear-gradient(135deg,var(--purple),var(--pink));">🎨</div>
      <h4>Your Name</h4>
      <span class="role">UI/UX Designer</span>
      <p>Creative mind behind every pixel, color, and interaction on the platform.</p>
      <div class="team-social">
        <a href="#">🔗</a>
        <a href="#">🐦</a>
        <a href="#">💼</a>
      </div>
    </div>
    <div class="team-card">
      <div class="team-avatar" style="background:linear-gradient(135deg,var(--pink),#ff6b6b);">🧠</div>
      <h4>Your Name</h4>
      <span class="role">AI Research Lead</span>
      <p>Exploring the frontiers of artificial intelligence to keep our community ahead.</p>
      <div class="team-social">
        <a href="#">🔗</a>
        <a href="#">🐦</a>
        <a href="#">💼</a>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════
     CTA
══════════════════════════════════════ -->
<section class="cta-section">
  <h2>Ready to Be Part<br>of <span style="background:linear-gradient(90deg,var(--cyan),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Something Big?</span></h2>
  <p>Join thousands of AI innovators sharing their work and shaping the future together.</p>
  <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
    <a href="register.php" class="btn-hero btn-primary" style="font-size:17px;padding:16px 44px;">🚀 Join for Free</a>
    <a href="index.php" class="btn-hero btn-outline" style="font-size:17px;padding:16px 44px;">← Back to Home</a>
  </div>
</section>

</div><!-- end .page -->

<footer>
  <p>© <?php echo date("Y"); ?> AI Community — Built for Innovators 🤖</p>
</footer>

<script>
// Generate floating particles
const container = document.getElementById('particles');
const colors = ['#00e0ff','#00ffa6','#a78bfa','#f472b6'];
for(let i = 0; i < 25; i++){
  const p = document.createElement('div');
  const size = Math.random() * 4 + 2;
  const color = colors[Math.floor(Math.random() * colors.length)];
  p.className = 'particle';
  p.style.cssText = `
    width:${size}px;height:${size}px;
    background:${color};
    left:${Math.random()*100}%;
    animation-duration:${Math.random()*15+10}s;
    animation-delay:${Math.random()*10}s;
    box-shadow:0 0 6px ${color};
  `;
  container.appendChild(p);
}

// Scroll reveal animation
const observer = new IntersectionObserver((entries) => {
  entries.forEach(el => {
    if(el.isIntersecting){
      el.target.style.opacity = '1';
      el.target.style.transform = 'translateY(0)';
    }
  });
}, {threshold: 0.1});

document.querySelectorAll('.mission-card,.vision-card,.team-card,.story-main-card').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(30px)';
  el.style.transition = 'opacity .6s ease, transform .6s ease';
  observer.observe(el);
});
</script>

</body>
</html>