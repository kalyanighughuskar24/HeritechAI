<?php
session_start();
require_once "config.php";

$approved_content = $conn->query("
    SELECT c.*, u.name AS uploader_name
    FROM contents c
    JOIN users u ON c.creator_id = u.id
    WHERE c.status = 'approved'
    ORDER BY c.id DESC
    LIMIT 12
");

$total_users   = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='user'")->fetch_assoc()['c'];
$total_content = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE status='approved'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HeritechAI Community — Share. Learn. Innovate.</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════
   ROOT TOKENS
══════════════════════════════════ */
:root{
  --cyan:#00e0ff; --green:#00ffa6; --purple:#a78bfa; --pink:#f472b6;
  --gold:#f5c842; --amber:#ff9d00;
  --dark:#060d1f; --darker:#030810;
  --sw:272px;
  --font-head:'Syne',sans-serif;
  --font-body:'DM Sans',sans-serif;
  --font-serif:'Playfair Display',serif;
  --nav-h:62px;
  --ticker-h:38px;
  --top-offset:100px;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{background:var(--dark);color:white;overflow-x:hidden;font-family:var(--font-body);}

/* ══════════════════════════════════
   BACKGROUND SYSTEM
══════════════════════════════════ */
.hero-bg{position:fixed;inset:0;z-index:0;background:var(--darker);overflow:hidden;}
.hero-bg .slide{position:absolute;inset:0;background-size:cover;background-position:center;opacity:0;transition:opacity 2.2s ease;transform:scale(1.07);}
.hero-bg .slide.active{opacity:1;}
.hero-bg .slide:nth-child(1){background-image:url('https://images.unsplash.com/photo-1548013146-72479768bada?w=1920&q=80');}
.hero-bg .slide:nth-child(2){background-image:url('https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1920&q=80');}
.hero-bg .slide:nth-child(3){background-image:url('https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?w=1920&q=80');}
.hero-bg-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(3,8,16,0.82) 0%,rgba(6,13,31,0.65) 40%,rgba(6,13,31,0.85) 80%,var(--dark) 100%),linear-gradient(90deg,rgba(0,0,0,0.4) 0%,transparent 55%);z-index:1;}
.hero-bg-noise{position:absolute;inset:0;z-index:2;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");opacity:.4;pointer-events:none;}
.bg-orbs{position:fixed;inset:0;z-index:3;pointer-events:none;overflow:hidden;}
.orb{position:absolute;border-radius:50%;filter:blur(100px);animation:orbFloat 16s infinite ease-in-out;}
.orb1{width:650px;height:650px;background:rgba(0,224,255,0.09);top:-180px;left:-180px;}
.orb2{width:520px;height:520px;background:rgba(167,139,250,0.08);top:30%;right:-180px;animation-delay:5s;}
.orb3{width:480px;height:480px;background:rgba(245,200,66,0.06);bottom:-120px;left:20%;animation-delay:9s;}
@keyframes orbFloat{0%,100%{transform:translate(0,0);}33%{transform:translate(50px,-50px);}66%{transform:translate(-35px,35px);}}
.aurora-layer{position:fixed;inset:0;z-index:2;pointer-events:none;overflow:hidden;}
.aurora-band{position:absolute;border-radius:50%;filter:blur(130px);animation:auroraDrift 20s ease-in-out infinite;mix-blend-mode:screen;}
.aurora1{width:110vw;height:50vh;background:linear-gradient(90deg,rgba(0,224,255,0.06),rgba(167,139,250,0.04),transparent);top:-10%;left:-10%;}
.aurora2{width:80vw;height:40vh;background:linear-gradient(120deg,rgba(245,200,66,0.04),rgba(255,100,0,0.03),transparent);top:20%;right:-15%;animation-delay:7s;}
.aurora3{width:90vw;height:45vh;background:linear-gradient(60deg,rgba(0,255,166,0.04),rgba(0,224,255,0.03),transparent);bottom:-5%;left:5%;animation-delay:14s;}
@keyframes auroraDrift{0%,100%{transform:translateX(0) scaleX(1);}50%{transform:translateX(60px) scaleX(1.06);}}
#circuit-canvas{position:fixed;inset:0;z-index:4;pointer-events:none;}
#particles-canvas{position:fixed;inset:0;z-index:5;pointer-events:none;}
.float-cultural{position:fixed;z-index:6;pointer-events:none;font-size:30px;animation:culturalFloat 22s ease-in-out infinite;filter:drop-shadow(0 0 8px rgba(245,200,66,0.2));}
.float-cultural:nth-child(1){top:16%;left:2%;opacity:.10;animation-duration:19s;}
.float-cultural:nth-child(2){top:44%;right:2%;opacity:.08;animation-duration:25s;animation-delay:4s;font-size:26px;}
.float-cultural:nth-child(3){bottom:22%;left:5%;opacity:.07;animation-duration:22s;animation-delay:8s;font-size:24px;}
.float-cultural:nth-child(4){top:70%;right:5%;opacity:.07;animation-duration:24s;animation-delay:12s;font-size:22px;}
.float-cultural:nth-child(5){top:30%;left:1%;opacity:.06;animation-duration:27s;animation-delay:6s;font-size:20px;}
.float-cultural:nth-child(6){bottom:42%;right:1%;opacity:.06;animation-duration:23s;animation-delay:10s;font-size:18px;}
@keyframes culturalFloat{0%{transform:translateY(0) rotate(0deg);}25%{transform:translateY(-24px) rotate(3deg);}50%{transform:translateY(12px) rotate(-2deg);}75%{transform:translateY(-16px) rotate(4deg);}100%{transform:translateY(0) rotate(0deg);}}

/* ══════════════════════════════════
   SIDEBAR
══════════════════════════════════ */
.sidebar{position:fixed;top:0;left:0;width:var(--sw);height:100vh;z-index:1100;background:rgba(4,9,20,0.97);backdrop-filter:blur(32px);border-right:1px solid rgba(255,255,255,0.06);display:flex;flex-direction:column;transform:translateX(calc(-1 * var(--sw)));transition:transform .4s cubic-bezier(.4,0,.2,1);}
.sidebar.open{transform:translateX(0);box-shadow:12px 0 80px rgba(0,0,0,0.8);}
.sb-logo{display:flex;align-items:center;gap:12px;padding:22px 16px 18px;border-bottom:1px solid rgba(255,255,255,0.06);flex-shrink:0;}
.sb-logo-ico{width:46px;height:46px;border-radius:14px;flex-shrink:0;background:linear-gradient(135deg,#f472b6,#a78bfa);display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(244,114,182,0.35);}
.sb-logo-ico svg{width:22px;height:22px;}
.sb-logo-name{font-size:15px;font-weight:700;font-family:var(--font-head);}
.sb-logo-sub{font-size:10px;color:rgba(255,255,255,0.3);margin-top:2px;letter-spacing:1px;text-transform:uppercase;}
.sb-nav{flex:1;overflow-y:auto;padding:10px 8px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,0.06) transparent;}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.07);border-radius:2px;}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 11px;border-radius:12px;color:rgba(255,255,255,0.5);font-size:13.5px;font-weight:500;text-decoration:none;cursor:pointer;margin-bottom:2px;border:1px solid transparent;background:none;width:100%;text-align:left;transition:all .2s;font-family:var(--font-body);}
.sb-item:hover{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.9);}
.sb-item.active{background:linear-gradient(90deg,rgba(0,224,255,0.13),rgba(167,139,250,0.08));color:#fff;border-color:rgba(0,224,255,0.18);}
.sb-ico{width:34px;height:34px;border-radius:9px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.05);transition:.2s;}
.sb-ico svg{width:17px;height:17px;stroke:rgba(255,255,255,0.45);stroke-width:1.75;fill:none;stroke-linecap:round;stroke-linejoin:round;transition:.2s;}
.sb-item:hover .sb-ico{background:rgba(255,255,255,0.1);}
.sb-item:hover .sb-ico svg{stroke:rgba(255,255,255,0.9);}
.sb-item.active .sb-ico{background:linear-gradient(135deg,rgba(0,224,255,0.22),rgba(167,139,250,0.22));}
.sb-item.active .sb-ico svg{stroke:#00e0ff;}
.sb-lbl{flex:1;}
.sb-arr{width:16px;height:16px;flex-shrink:0;opacity:.3;transition:transform .25s,opacity .2s;display:flex;align-items:center;justify-content:center;}
.sb-arr svg{width:11px;height:11px;stroke:currentColor;stroke-width:2.2;fill:none;stroke-linecap:round;stroke-linejoin:round;}
.sb-item.expanded .sb-arr{transform:rotate(180deg);opacity:.6;}
.sb-sub{overflow:hidden;max-height:0;transition:max-height .3s ease;}
.sb-sub.open{max-height:220px;}
.sb-si{display:flex;align-items:center;gap:9px;padding:8px 11px 8px 55px;border-radius:9px;color:rgba(255,255,255,0.36);font-size:13px;text-decoration:none;transition:.2s;margin-bottom:1px;}
.sb-si:hover{background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.75);}
.sb-dot{width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,0.2);flex-shrink:0;transition:.2s;}
.sb-si:hover .sb-dot{background:var(--cyan);}
.sb-div{height:1px;background:rgba(255,255,255,0.05);margin:8px 3px;}
.sb-grp{font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.18);padding:8px 11px 3px;font-family:var(--font-head);}
.sb-foot{padding:11px 8px 14px;border-top:1px solid rgba(255,255,255,0.06);flex-shrink:0;display:flex;flex-direction:column;gap:7px;}
.sb-cta{display:flex;align-items:center;justify-content:center;gap:8px;padding:13px 14px;border-radius:13px;background:linear-gradient(90deg,#a78bfa,#f472b6);color:#fff;font-size:13px;font-weight:700;text-decoration:none;transition:.25s;box-shadow:0 4px 24px rgba(167,139,250,0.32);font-family:var(--font-head);}
.sb-cta:hover{transform:translateY(-2px);box-shadow:0 8px 36px rgba(167,139,250,0.5);}
.sb-cta svg{width:15px;height:15px;stroke:white;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0;}
.sb-help{display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:11px;color:rgba(255,255,255,0.34);font-size:12.5px;background:rgba(255,255,255,0.04);text-decoration:none;transition:.2s;}
.sb-help:hover{color:rgba(255,255,255,0.7);background:rgba(255,255,255,0.08);}
.sb-help svg{width:14px;height:14px;stroke:currentColor;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round;}
.sb-copy{text-align:center;font-size:10px;color:rgba(255,255,255,0.14);line-height:1.7;}
.sb-copy b{color:rgba(0,224,255,0.45);font-weight:600;}
.sb-overlay{position:fixed;inset:0;z-index:1050;background:rgba(0,0,0,0.6);backdrop-filter:blur(6px);opacity:0;pointer-events:none;transition:opacity .3s;}
.sb-overlay.show{opacity:1;pointer-events:all;}

/* ══════════════════════════════════
   NAVBAR
══════════════════════════════════ */
nav{
  position:fixed;top:0;left:0;right:0;z-index:1000;
  display:flex;justify-content:space-between;align-items:center;
  padding:0 72px;
  height:var(--nav-h);
  background:rgba(4,9,20,0.72);
  backdrop-filter:blur(28px);
  border-bottom:1px solid rgba(255,255,255,0.05);
  transition:background .4s;
}
nav.scrolled{background:rgba(4,9,20,0.96);}
.nav-left{display:flex;align-items:center;gap:14px;}
.hamburger{width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;cursor:pointer;transition:.25s;flex-shrink:0;}
.hamburger:hover{background:rgba(0,224,255,0.1);border-color:rgba(0,224,255,0.25);}
.hamburger span{display:block;width:18px;height:2px;background:rgba(255,255,255,0.7);border-radius:2px;transition:.3s;}
.hamburger.active span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.hamburger.active span:nth-child(2){opacity:0;transform:scaleX(0);}
.hamburger.active span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}
.logo{font-size:20px;font-weight:800;font-family:var(--font-head);letter-spacing:-.3px;display:flex;align-items:center;gap:7px;}
.logo-icon{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,var(--gold),var(--amber));display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;box-shadow:0 4px 14px rgba(245,200,66,0.35);}
.logo-text{background:linear-gradient(90deg,var(--gold),var(--amber),var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-size:200%;animation:gradAnim 5s linear infinite;}
@keyframes gradAnim{0%{background-position:0%;}100%{background-position:200%;}}
.nav-links{display:flex;align-items:center;gap:6px;list-style:none;}
.nav-links a{text-decoration:none;color:rgba(255,255,255,0.65);font-size:13.5px;font-weight:500;padding:7px 14px;border-radius:20px;transition:.25s;font-family:var(--font-body);}
.nav-links a:hover{color:white;background:rgba(255,255,255,0.07);}
.btn-nav{background:linear-gradient(90deg,var(--cyan),var(--green))!important;color:#000!important;font-weight:700!important;border-radius:20px!important;box-shadow:0 4px 20px rgba(0,224,255,0.28);}
.btn-nav:hover{transform:scale(1.05)!important;box-shadow:0 6px 28px rgba(0,224,255,0.5)!important;}
.btn-admin{background:linear-gradient(90deg,#a78bfa,#f472b6)!important;color:#fff!important;font-weight:700!important;}

/* ══════════════════════════════════
   TICKER
══════════════════════════════════ */
.ticker-wrap{
  position:fixed;
  top:var(--nav-h);
  left:0;right:0;
  z-index:999;
  height:var(--ticker-h);
  overflow:hidden;
  display:flex;align-items:center;
  background:linear-gradient(90deg,rgba(4,9,20,0.92),rgba(8,16,38,0.90),rgba(4,9,20,0.92));
  backdrop-filter:blur(20px);
  border-bottom:1px solid rgba(245,200,66,0.18);
  box-shadow:0 2px 24px rgba(245,200,66,0.07);
}
.ticker-label{
  flex-shrink:0;
  display:flex;align-items:center;gap:7px;
  padding:0 18px 0 20px;
  border-right:1px solid rgba(245,200,66,0.18);
  height:100%;
  background:rgba(245,200,66,0.06);
}
.ticker-label-dot{width:6px;height:6px;border-radius:50%;background:var(--gold);animation:pulse 2s infinite;flex-shrink:0;}
.ticker-label-text{font-size:10px;font-weight:700;color:var(--gold);font-family:var(--font-head);letter-spacing:1.5px;text-transform:uppercase;white-space:nowrap;}
.ticker-track{
  display:flex;white-space:nowrap;
  animation:tickerMove 36s linear infinite;
  flex:1;min-width:0;
}
.ticker-track:hover{animation-play-state:paused;}
.ticker-item{
  display:inline-flex;align-items:center;gap:10px;
  padding:0 32px;
  font-size:11.5px;font-weight:600;
  color:rgba(245,200,66,0.72);
  font-family:var(--font-head);letter-spacing:.9px;text-transform:uppercase;
  cursor:default;transition:color .2s;
}
.ticker-item:hover{color:var(--gold);}
.ticker-sep{color:rgba(245,200,66,0.28);font-size:9px;margin-left:10px;}
@keyframes tickerMove{0%{transform:translateX(0);}100%{transform:translateX(-50%);}}

/* ══════════════════════════════════
   HERO
══════════════════════════════════ */
.hero{
  position:relative;z-index:10;
  min-height:100vh;
  display:flex;align-items:center;
  padding:calc(var(--nav-h) + var(--ticker-h) + 48px) 88px 72px;
  gap:80px;
}
.hero-left{
  flex:1;
  display:flex;flex-direction:column;
  align-items:flex-start;
  max-width:560px;
  align-self:center;
}
.heritage-badge{
  display:inline-flex;align-items:center;gap:9px;
  background:rgba(245,200,66,0.08);border:1px solid rgba(245,200,66,0.28);
  padding:7px 18px;border-radius:30px;
  font-size:11px;font-weight:700;color:var(--gold);
  margin-bottom:24px;letter-spacing:1.2px;text-transform:uppercase;
  font-family:var(--font-head);animation:fadeUp .7s ease both;
}
.heritage-badge .pulse-dot{width:7px;height:7px;border-radius:50%;background:var(--gold);animation:pulse 2s infinite;flex-shrink:0;}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.3;transform:scale(1.6);}}
.hero-left h1{
  font-size:66px;font-weight:800;line-height:1.0;
  margin-bottom:20px;font-family:var(--font-head);
  animation:fadeUp .7s .1s ease both;
}
.hero-left h1 .line-serif{
  font-family:var(--font-serif);font-style:italic;font-weight:400;font-size:.82em;
  background:linear-gradient(90deg,var(--gold),var(--amber),#ff6b35);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-size:300%;animation:gradAnim 5s linear infinite;
  display:block;letter-spacing:-1px;
}
.hero-left h1 .line-tech{
  background:linear-gradient(90deg,var(--cyan),var(--green),var(--purple));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-size:300%;animation:gradAnim 5s 1.5s linear infinite;display:block;
}
.hero-left h1 .line-plain{display:block;color:rgba(255,255,255,.92);}
.hero-tagline{
  font-size:15.5px;max-width:480px;color:rgba(255,255,255,.60);
  line-height:1.82;margin-bottom:14px;
  animation:fadeUp .7s .2s ease both;font-weight:300;
}
.skt-accent{
  font-family:var(--font-serif);font-style:italic;
  font-size:13px;color:rgba(245,200,66,.50);
  margin-bottom:34px;letter-spacing:.3px;
  animation:fadeUp .7s .25s ease both;
  display:flex;align-items:center;gap:10px;
}
.skt-accent::before,.skt-accent::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(245,200,66,0.14),transparent);}
.hero-btns{display:flex;gap:16px;flex-wrap:wrap;animation:fadeUp .7s .3s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(26px);}to{opacity:1;transform:translateY(0);}}

/* ══════════════════════════════════
   HERO RIGHT
══════════════════════════════════ */
.hero-right{
  flex:0 0 500px;
  display:flex;flex-direction:column;
  align-items:center;gap:16px;
  animation:fadeUp .7s .35s ease both;
  position:relative;align-self:center;
}
.struct-glow{position:absolute;border-radius:50%;pointer-events:none;z-index:0;animation:structGlow 5s ease-in-out infinite;}
.struct-glow-1{width:370px;height:370px;border:1px solid rgba(245,200,66,0.11);top:37%;left:50%;transform:translate(-50%,-50%);}
.struct-glow-2{width:500px;height:500px;border:1px solid rgba(0,224,255,0.05);top:37%;left:50%;transform:translate(-50%,-50%);animation-delay:1.5s;}
@keyframes structGlow{0%,100%{opacity:.8;}50%{opacity:.3;}}

/* ── CAROUSEL ── */
.heritage-carousel{
  position:relative;width:100%;height:320px;
  border-radius:22px;overflow:hidden;z-index:12;
  box-shadow:0 0 0 1px rgba(245,200,66,0.26),0 0 55px rgba(245,200,66,0.11),0 30px 64px rgba(0,0,0,0.62);
}
.carousel-frame{position:absolute;inset:0;z-index:10;pointer-events:none;border-radius:22px;border:1px solid rgba(245,200,66,0.28);box-shadow:inset 0 0 50px rgba(0,0,0,0.22);}
.corner-tl,.corner-tr,.corner-bl,.corner-br{position:absolute;z-index:11;pointer-events:none;width:20px;height:20px;}
.corner-tl{top:11px;left:11px;border-top:2px solid var(--gold);border-left:2px solid var(--gold);border-radius:3px 0 0 0;}
.corner-tr{top:11px;right:11px;border-top:2px solid var(--gold);border-right:2px solid var(--gold);border-radius:0 3px 0 0;}
.corner-bl{bottom:11px;left:11px;border-bottom:2px solid var(--gold);border-left:2px solid var(--gold);border-radius:0 0 0 3px;}
.corner-br{bottom:11px;right:11px;border-bottom:2px solid var(--gold);border-right:2px solid var(--gold);border-radius:0 0 3px 0;}
.carousel-slides{position:relative;width:100%;height:100%;}
.c-slide{
  position:absolute;inset:0;background-size:cover;background-position:center;
  opacity:0;transition:opacity 1.4s cubic-bezier(.4,0,.2,1);
  animation:slideKen 14s ease-in-out infinite;
}
.c-slide.active{opacity:1;z-index:2;}
@keyframes slideKen{0%{transform:scale(1.07);}50%{transform:scale(1.0);}100%{transform:scale(1.07);}}
.c-slide:nth-child(1){background-image:url('https://images.unsplash.com/photo-1564507592333-c60657eea523?w=900&q=90');}
.c-slide:nth-child(2){background-image:url('https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=900&q=90');}
.c-slide:nth-child(3){background-image:url('https://images.unsplash.com/photo-1477587458883-47145ed94245?w=900&q=90');}
.c-slide:nth-child(4){background-image:url('https://images.unsplash.com/photo-1598091383021-15ddea10925d?w=900&q=90');}
.c-slide:nth-child(5){background-image:url('https://images.unsplash.com/photo-1610809027249-86c649feacd5?w=900&q=90');}
.c-slide:nth-child(6){background-image:url('https://images.unsplash.com/photo-1587474260584-136574528ed5?w=900&q=90');}
.slide-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(6,13,31,.06) 0%,transparent 30%,rgba(6,13,31,.6) 78%,rgba(6,13,31,.92) 100%);}
.slide-caption{position:absolute;bottom:42px;left:0;right:0;display:flex;align-items:center;justify-content:center;gap:8px;padding:0 16px;z-index:3;}
.cap-icon{font-size:16px;}
.cap-text{font-size:12.5px;font-weight:700;color:white;font-family:var(--font-head);letter-spacing:.4px;text-shadow:0 2px 12px rgba(0,0,0,.8);}
.carousel-dots{position:absolute;bottom:14px;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:12;}
.cdot{width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.28);border:1px solid rgba(255,255,255,.4);cursor:pointer;transition:.35s;}
.cdot.active{background:var(--gold);border-color:var(--gold);width:20px;border-radius:4px;}
.scan-beam{position:absolute;width:100%;height:1.5px;background:linear-gradient(90deg,transparent,rgba(0,224,255,0.55),transparent);top:8%;z-index:22;animation:scanBeam 4.5s ease-in-out infinite;pointer-events:none;}
@keyframes scanBeam{0%{top:5%;opacity:0;}10%{opacity:1;}85%{opacity:.7;}100%{top:57%;opacity:0;}}
.img-scan{position:absolute;inset:0;z-index:8;pointer-events:none;background:repeating-linear-gradient(to bottom,transparent 0px,transparent 3px,rgba(0,224,255,0.016) 3px,rgba(0,224,255,0.016) 4px);}
.ai-badge{position:absolute;top:12px;right:12px;z-index:14;display:flex;align-items:center;gap:6px;background:rgba(4,9,20,.88);border:1px solid rgba(0,224,255,.32);border-radius:20px;padding:5px 11px;backdrop-filter:blur(12px);}
.ai-dot{width:6px;height:6px;border-radius:50%;background:var(--cyan);animation:aiPulse 1.6s ease-in-out infinite;}
@keyframes aiPulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.3;transform:scale(1.8);}}
.ai-label{font-size:10.5px;font-weight:700;color:var(--cyan);font-family:var(--font-head);letter-spacing:1px;text-transform:uppercase;}
.stats-chip{position:absolute;top:12px;left:12px;z-index:14;background:rgba(4,9,20,.88);border:1px solid rgba(245,200,66,.28);border-radius:12px;padding:5px 11px;backdrop-filter:blur(12px);}
.chip-row{display:flex;align-items:baseline;gap:3px;}
.chip-num{font-size:17px;font-weight:800;color:var(--gold);font-family:var(--font-head);line-height:1;}
.chip-sub{font-size:9.5px;color:rgba(255,255,255,.4);font-family:var(--font-head);letter-spacing:1px;text-transform:uppercase;}
.carousel-btn{position:absolute;top:50%;z-index:14;transform:translateY(-50%);width:30px;height:30px;border-radius:50%;background:rgba(4,9,20,.8);border:1px solid rgba(245,200,66,.25);display:flex;align-items:center;justify-content:center;cursor:pointer;backdrop-filter:blur(8px);color:var(--gold);font-size:12px;opacity:0;transition:opacity .25s;}
.heritage-carousel:hover .carousel-btn{opacity:1;}
.carousel-btn:hover{background:rgba(245,200,66,.12);border-color:var(--gold);}
.carousel-btn.prev{left:9px;}
.carousel-btn.next{right:9px;}

/* SITE INFO PANEL */
.site-info-panel{
  width:100%;border-radius:18px;
  background:rgba(6,14,36,0.82);
  border:1px solid rgba(255,255,255,0.07);
  padding:18px 22px 16px;z-index:12;position:relative;
  backdrop-filter:blur(18px);
  box-shadow:0 8px 40px rgba(0,0,0,0.4);
}
.sip-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:10px;}
.sip-name{font-size:17px;font-weight:800;color:#fff;line-height:1.15;font-family:var(--font-head);}
.sip-loc{font-size:11.5px;color:rgba(255,255,255,.38);margin-top:3px;font-family:var(--font-body);}
.sip-era{background:rgba(245,200,66,.1);border:1px solid rgba(245,200,66,.26);border-radius:20px;padding:5px 12px;font-size:10.5px;font-weight:700;color:var(--gold);white-space:nowrap;font-family:var(--font-head);flex-shrink:0;letter-spacing:.4px;}
.sip-desc{font-size:12.5px;color:rgba(255,255,255,.46);line-height:1.68;margin-bottom:13px;font-family:var(--font-body);}
.sip-facts{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;}
.sip-fact{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:10px;padding:9px 8px;text-align:center;transition:.3s;}
.sip-fact:hover{background:rgba(0,224,255,.05);border-color:rgba(0,224,255,.18);}
.sif-v{font-size:14px;font-weight:800;color:var(--cyan);font-family:var(--font-head);line-height:1;}
.sif-l{font-size:9.5px;color:rgba(255,255,255,.26);margin-top:4px;letter-spacing:.8px;text-transform:uppercase;font-family:var(--font-head);}
.sip-styles{display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.05);}
.style-pill{font-size:9.5px;font-weight:700;letter-spacing:.5px;padding:4px 10px;border-radius:20px;font-family:var(--font-head);text-transform:uppercase;border:1px solid;}
.sp-gold{background:rgba(245,200,66,.08);border-color:rgba(245,200,66,.26);color:var(--gold);}
.sp-cyan{background:rgba(0,224,255,.07);border-color:rgba(0,224,255,.20);color:var(--cyan);}
.sp-purple{background:rgba(167,139,250,.08);border-color:rgba(167,139,250,.23);color:var(--purple);}
.sp-green{background:rgba(0,255,166,.07);border-color:rgba(0,255,166,.20);color:var(--green);}

/* BUTTONS */
.btn-hero{padding:13px 32px;border-radius:50px;font-size:15px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:9px;position:relative;overflow:hidden;font-family:var(--font-head);letter-spacing:.3px;transition:transform .3s cubic-bezier(.34,1.56,.64,1),box-shadow .3s;}
.btn-primary{background:linear-gradient(90deg,var(--gold),var(--amber),#ff6b35);background-size:200%;color:#000;box-shadow:0 5px 26px rgba(245,200,66,.32);animation:btnGoldShift 5s ease-in-out infinite;}
.btn-primary::before{content:'';position:absolute;top:0;left:-75%;width:50%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.38),transparent);transform:skewX(-20deg);animation:btnShimmer 3.5s 1.2s ease-in-out infinite;}
@keyframes btnGoldShift{0%,100%{background-position:0%;}50%{background-position:100%;}}
@keyframes btnShimmer{0%,100%{left:-75%;}50%{left:125%;}}
.btn-primary:hover{transform:translateY(-3px) scale(1.04);box-shadow:0 12px 40px rgba(245,200,66,.48);}
.btn-electric{border:1.5px solid rgba(0,224,255,.45);color:var(--cyan);background:rgba(0,224,255,.05);}
.btn-electric:hover{transform:translateY(-3px) scale(1.04);background:rgba(0,224,255,.1);border-color:var(--cyan);box-shadow:0 0 28px rgba(0,224,255,.35);}

/* ══════════════════════════════════
   STATS BAR
══════════════════════════════════ */
.stats-bar{position:relative;z-index:10;display:flex;justify-content:center;background:rgba(255,255,255,.025);border-top:1px solid rgba(255,255,255,.05);border-bottom:1px solid rgba(255,255,255,.05);padding:40px 0;flex-wrap:wrap;backdrop-filter:blur(12px);}
.stat-item{text-align:center;padding:0 58px;border-right:1px solid rgba(255,255,255,.07);}
.stat-item:last-child{border-right:none;}
.stat-num{font-size:46px;font-weight:800;display:block;background:linear-gradient(90deg,var(--gold),var(--amber));-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-family:var(--font-head);}
.stat-lbl{font-size:11px;opacity:.42;margin-top:6px;letter-spacing:1.8px;text-transform:uppercase;font-family:var(--font-head);}

/* ══════════════════════════════════
   SECTIONS
══════════════════════════════════ */
.section{position:relative;z-index:10;padding:100px 88px;}
.section-label{font-size:10.5px;font-weight:700;letter-spacing:3px;color:var(--gold);text-transform:uppercase;margin-bottom:12px;font-family:var(--font-head);display:flex;align-items:center;gap:10px;}
.section-label::before{content:'';width:24px;height:2px;background:var(--gold);border-radius:2px;}
.section-title{font-size:42px;font-weight:800;margin-bottom:16px;font-family:var(--font-head);line-height:1.1;}
.section-sub{font-size:15.5px;opacity:.55;max-width:560px;line-height:1.85;font-weight:300;}
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:56px;}
.feat-card{background:rgba(8,18,42,0.62);border:1px solid rgba(255,255,255,.065);border-radius:20px;padding:32px;transition:transform .4s cubic-bezier(.34,1.56,.64,1),border-color .3s,box-shadow .3s;position:relative;overflow:hidden;cursor:default;backdrop-filter:blur(10px);}
.feat-card::before{content:'';position:absolute;inset:0;border-radius:20px;opacity:0;transition:.4s;background:linear-gradient(135deg,rgba(245,200,66,.06),rgba(0,224,255,.03));}
.feat-card::after{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--gold),var(--cyan));transform:scaleX(0);transition:transform .4s ease;transform-origin:left;}
.feat-card:hover{transform:translateY(-8px);border-color:rgba(245,200,66,.22);box-shadow:0 22px 55px rgba(0,0,0,.4);}
.feat-card:hover::before{opacity:1;}
.feat-card:hover::after{transform:scaleX(1);}
.feat-icon{font-size:34px;margin-bottom:18px;display:flex;align-items:center;width:62px;height:62px;border-radius:16px;background:linear-gradient(135deg,rgba(245,200,66,.1),rgba(0,224,255,.06));justify-content:center;border:1px solid rgba(245,200,66,.12);transition:.3s;}
.feat-card:hover .feat-icon{transform:scale(1.08);}
.feat-card h3{font-size:17px;font-weight:700;margin-bottom:9px;font-family:var(--font-head);}
.feat-card p{font-size:13.5px;opacity:.53;line-height:1.75;font-weight:300;}

/* AI STRIP */
.ai-strip{position:relative;z-index:10;padding:60px 88px;background:linear-gradient(135deg,rgba(0,224,255,0.04),rgba(167,139,250,0.04),rgba(245,200,66,0.03));border-top:1px solid rgba(0,224,255,0.08);border-bottom:1px solid rgba(167,139,250,0.08);display:flex;align-items:center;gap:48px;overflow:hidden;}
.ai-strip-icon{font-size:56px;flex-shrink:0;filter:drop-shadow(0 0 20px rgba(0,224,255,0.3));}
.ai-strip-content{flex:1;}
.ai-strip-label{font-size:10px;font-weight:700;letter-spacing:2.5px;color:var(--cyan);text-transform:uppercase;font-family:var(--font-head);margin-bottom:8px;}
.ai-strip-title{font-size:26px;font-weight:800;font-family:var(--font-head);margin-bottom:6px;}
.ai-strip-sub{font-size:14px;color:rgba(255,255,255,.5);font-weight:300;line-height:1.7;}
.ai-strip-pills{display:flex;gap:8px;margin-top:16px;flex-wrap:wrap;}
.ai-pill{padding:6px 14px;border-radius:20px;font-size:11px;font-weight:700;font-family:var(--font-head);letter-spacing:.5px;border:1px solid;}
.ai-pill-1{background:rgba(0,224,255,.08);border-color:rgba(0,224,255,.28);color:var(--cyan);}
.ai-pill-2{background:rgba(167,139,250,.08);border-color:rgba(167,139,250,.28);color:var(--purple);}
.ai-pill-3{background:rgba(0,255,166,.07);border-color:rgba(0,255,166,.25);color:var(--green);}
.ai-strip-stat{flex:0 0 140px;text-align:center;padding:28px 20px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:18px;}
.ais-num{font-size:36px;font-weight:800;color:var(--gold);font-family:var(--font-head);display:block;}
.ais-label{font-size:10px;color:rgba(255,255,255,.35);letter-spacing:1.2px;text-transform:uppercase;font-family:var(--font-head);margin-top:4px;}

/* CONTENT GRID */
.content-section{position:relative;z-index:10;padding:80px 88px 100px;}
.content-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:42px;}
.content-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;}
.content-card{background:rgba(8,18,42,0.72);border:1px solid rgba(255,255,255,.065);border-radius:20px;overflow:hidden;transition:transform .4s cubic-bezier(.34,1.56,.64,1),border-color .3s,box-shadow .3s;backdrop-filter:blur(8px);}
.content-card:hover{transform:translateY(-8px);border-color:rgba(245,200,66,.28);box-shadow:0 22px 55px rgba(245,200,66,.09);}
.card-media{width:100%;height:180px;background:linear-gradient(135deg,rgba(245,200,66,.04),rgba(0,224,255,.03));display:flex;align-items:center;justify-content:center;overflow:hidden;}
.card-media img{width:100%;height:100%;object-fit:cover;transition:.5s;}
.content-card:hover .card-media img{transform:scale(1.06);}
.card-media video{width:100%;height:100%;object-fit:cover;}
.card-media .file-emoji{font-size:52px;}
.card-media audio{width:90%;}
.card-body{padding:20px;}
.card-cat{font-size:10.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gold);margin-bottom:9px;font-family:var(--font-head);}
.card-title{font-size:15.5px;font-weight:700;margin-bottom:8px;line-height:1.4;font-family:var(--font-head);}
.card-desc{font-size:13px;opacity:.5;line-height:1.65;margin-bottom:16px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.card-footer{display:flex;justify-content:space-between;align-items:center;padding-top:13px;border-top:1px solid rgba(255,255,255,.055);}
.card-author{font-size:11.5px;opacity:.42;}
.card-download{padding:7px 16px;border-radius:30px;font-size:11.5px;font-weight:700;background:linear-gradient(90deg,var(--gold),var(--amber));color:#000;text-decoration:none;transition:.3s;font-family:var(--font-head);box-shadow:0 3px 12px rgba(245,200,66,.28);}
.card-download:hover{transform:scale(1.07);box-shadow:0 5px 18px rgba(245,200,66,.46);}
.empty-state{grid-column:1/-1;text-align:center;padding:80px 20px;background:rgba(255,255,255,.025);border-radius:20px;border:1px solid rgba(255,255,255,.055);}
.empty-state .empty-icon{font-size:56px;margin-bottom:16px;}
.empty-state h3{font-size:20px;margin-bottom:8px;font-family:var(--font-head);}
.empty-state p{opacity:.42;font-size:14.5px;margin-bottom:24px;}

/* HOW IT WORKS */
.how-section{position:relative;z-index:10;padding:100px 88px;background:rgba(255,255,255,.012);border-top:1px solid rgba(255,255,255,.04);border-bottom:1px solid rgba(255,255,255,.04);}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:28px;margin-top:60px;}
.step{text-align:center;position:relative;}
.step-connector{position:absolute;top:30px;left:calc(50% + 34px);width:calc(100% - 68px);height:1px;background:linear-gradient(90deg,rgba(245,200,66,.28),transparent);}
.step:last-child .step-connector{display:none;}
.step-num{width:60px;height:60px;border-radius:50%;margin:0 auto 20px;background:linear-gradient(135deg,var(--gold),var(--amber));display:flex;align-items:center;justify-content:center;font-size:19px;font-weight:800;color:#000;font-family:var(--font-head);box-shadow:0 8px 28px rgba(245,200,66,.28);}
.step h3{font-size:16.5px;font-weight:700;margin-bottom:9px;font-family:var(--font-head);}
.step p{font-size:13px;opacity:.5;line-height:1.7;font-weight:300;}

/* CTA + FOOTER */
.cta-section{position:relative;z-index:10;text-align:center;padding:120px 20px;overflow:hidden;}
.cta-bg{position:absolute;inset:0;z-index:-1;background:linear-gradient(135deg,rgba(245,200,66,.04),rgba(0,224,255,.03),rgba(167,139,250,.04));}
.cta-bg::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1548013146-72479768bada?w=1400&q=50') center/cover;opacity:.04;}
.cta-section h2{font-size:54px;font-weight:800;margin-bottom:18px;font-family:var(--font-head);line-height:1.1;}
.cta-section p{font-size:17px;opacity:.58;margin-bottom:42px;font-weight:300;}
footer{position:relative;z-index:10;text-align:center;padding:30px;border-top:1px solid rgba(255,255,255,.04);font-size:12.5px;opacity:.35;font-family:var(--font-head);letter-spacing:.4px;}

/* REVEAL */
.reveal{opacity:0;transform:translateY(26px);transition:opacity .7s ease,transform .7s ease;}
.reveal.visible{opacity:1;transform:translateY(0);}

/* ══════════════════════════════════
   MAP SECTION
══════════════════════════════════ */
.map-section{
  position:relative;z-index:10;
  padding:100px 88px;
  background:rgba(255,255,255,.012);
  border-top:1px solid rgba(255,255,255,.04);
  border-bottom:1px solid rgba(255,255,255,.04);
}
.map-header{
  text-align:center;
  margin-bottom:52px;
  display:flex;
  flex-direction:column;
  align-items:center;
}
.map-header .section-label{justify-content:center;}
.map-header .section-label::before{display:none;}
.map-header .section-title{text-align:center;font-size:42px;margin-bottom:16px;}
.map-header .section-sub{text-align:center;max-width:600px;}
.map-title-accent{
  background:linear-gradient(90deg,var(--gold),var(--amber));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.map-shell{display:flex;gap:24px;align-items:stretch;min-height:580px;}

/* Info panel */
.map-info-panel{
  flex:0 0 300px;
  background:rgba(6,14,36,0.88);
  border:1px solid rgba(255,255,255,.07);
  border-radius:20px;overflow:hidden;
  backdrop-filter:blur(20px);
  display:flex;flex-direction:column;
  box-shadow:0 8px 40px rgba(0,0,0,.45);
}
.mip-default{
  display:flex;flex-direction:column;align-items:center;
  justify-content:center;text-align:center;
  padding:40px 24px;flex:1;
}
.mip-default-icon{font-size:52px;margin-bottom:16px;animation:floatGently 4s ease-in-out infinite;}
@keyframes floatGently{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
.mip-default-title{font-size:18px;font-weight:800;font-family:var(--font-head);color:#fff;margin-bottom:8px;}
.mip-default-sub{font-size:12.5px;color:rgba(255,255,255,.35);line-height:1.7;margin-bottom:22px;}
.mip-hint-row{display:flex;flex-direction:column;gap:7px;width:100%;}
.mip-hint{font-size:11.5px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:8px 14px;color:rgba(255,255,255,.38);font-family:var(--font-head);}
.mip-content{display:none;flex-direction:column;flex:1;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.06) transparent;}
.mip-content::-webkit-scrollbar{width:3px;}
.mip-content::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:2px;}

/* ════════════════════════════════
   STATE BANNER FIX — badge overflow fixed
════════════════════════════════ */
.mip-state-banner{
  padding:14px 12px 12px;
  background:linear-gradient(135deg,rgba(245,200,66,.08),rgba(0,224,255,.04));
  border-bottom:1px solid rgba(255,255,255,.05);
  display:flex;
  align-items:flex-start;
  gap:8px;
  /* CRITICAL FIX: overflow hidden + no flex-wrap so nothing spills outside */
  overflow:hidden;
  flex-wrap:nowrap;
  width:100%;
}
.mip-state-emoji{
  font-size:26px;
  flex-shrink:0;
  line-height:1.3;
  margin-top:2px;
}
.mip-state-info{
  flex:1;
  min-width:0;   /* CRITICAL: allows text-overflow:ellipsis inside flex child */
  overflow:hidden;
}
.mip-state-name{
  font-size:13px;
  font-weight:800;
  font-family:var(--font-head);
  color:#fff;
  line-height:1.25;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
/* Capital + region on one line, never wraps */
.mip-state-capital{
  font-size:9.5px;
  color:rgba(255,255,255,.36);
  margin-top:2px;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
/* BADGE: constrained width, shrinks but never overflows */
.mip-state-badge{
  font-size:7.5px;
  font-weight:700;
  font-family:var(--font-head);
  letter-spacing:.3px;
  background:rgba(245,200,66,.12);
  border:1px solid rgba(245,200,66,.3);
  color:var(--gold);
  padding:3px 6px;
  border-radius:20px;
  white-space:nowrap;
  text-transform:uppercase;
  align-self:flex-start;
  flex-shrink:0;
  /* CRITICAL FIX: hard cap so badge never pushes outside the panel */
  max-width:82px;
  overflow:hidden;
  text-overflow:ellipsis;
  text-align:center;
  line-height:1.5;
}

.mip-tagline{font-size:11px;font-style:italic;color:rgba(245,200,66,.52);font-family:var(--font-serif);padding:10px 18px 0;line-height:1.6;}
.mip-facts-row{display:grid;grid-template-columns:repeat(3,1fr);gap:7px;padding:10px 18px 6px;}
.mip-fact{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:10px;padding:8px 5px;text-align:center;}
.mif-v{font-size:12px;font-weight:800;color:var(--cyan);font-family:var(--font-head);line-height:1;}
.mif-l{font-size:8.5px;color:rgba(255,255,255,.24);margin-top:3px;letter-spacing:.5px;text-transform:uppercase;font-family:var(--font-head);}
.mip-block{padding:10px 18px 4px;}
.mip-block-label{font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.22);font-family:var(--font-head);margin-bottom:7px;}
.mip-sites{display:flex;flex-direction:column;gap:4px;}
.mip-site-item{font-size:11.5px;color:rgba(255,255,255,.58);padding:4px 0;border-bottom:1px solid rgba(255,255,255,.04);display:flex;align-items:center;gap:7px;}
.mip-site-item:last-child{border-bottom:none;}
.mip-pills{display:flex;flex-wrap:wrap;gap:5px;}
.mip-pill{font-size:9.5px;font-weight:700;font-family:var(--font-head);padding:4px 9px;border-radius:20px;letter-spacing:.3px;border:1px solid;}
.mp-gold{background:rgba(245,200,66,.08);border-color:rgba(245,200,66,.28);color:var(--gold);}
.mp-cyan{background:rgba(0,224,255,.07);border-color:rgba(0,224,255,.22);color:var(--cyan);}
.mp-purple{background:rgba(167,139,250,.08);border-color:rgba(167,139,250,.25);color:var(--purple);}
.mp-green{background:rgba(0,255,166,.07);border-color:rgba(0,255,166,.22);color:var(--green);}
.mp-pink{background:rgba(244,114,182,.07);border-color:rgba(244,114,182,.22);color:var(--pink);}
.mip-about{font-size:11.5px;color:rgba(255,255,255,.42);line-height:1.75;padding-bottom:6px;}
.mip-unesco{display:flex;flex-direction:column;gap:4px;padding-bottom:10px;}
.mip-unesco-item{font-size:11px;color:rgba(167,139,250,.8);display:flex;align-items:flex-start;gap:6px;padding:3px 0;line-height:1.4;}

/* Map wrap */
.map-container-wrap{
  flex:1;display:flex;flex-direction:column;
  border-radius:20px;overflow:hidden;
  border:1px solid rgba(255,255,255,.07);
  box-shadow:0 8px 40px rgba(0,0,0,.45);
  min-height:560px;
}
.map-top-bar{
  display:flex;justify-content:space-between;align-items:center;
  padding:11px 18px;
  background:rgba(4,9,20,.95);
  border-bottom:1px solid rgba(255,255,255,.06);
  flex-shrink:0;
}
.map-title-bar{display:flex;align-items:center;gap:8px;}
.map-live-dot{width:7px;height:7px;border-radius:50%;background:var(--gold);animation:pulse 2s infinite;flex-shrink:0;}
.map-title-text{font-size:12px;font-weight:700;font-family:var(--font-head);color:rgba(255,255,255,.6);letter-spacing:.4px;}
.map-legend{display:flex;align-items:center;gap:14px;}
.legend-item{display:flex;align-items:center;gap:5px;font-size:10px;color:rgba(255,255,255,.32);font-family:var(--font-head);}
.legend-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.ld-gold{background:rgba(245,200,66,.8);}
.ld-purple{background:rgba(167,139,250,.8);}
.ld-cyan{background:rgba(0,224,255,.9);}
#heritageMap{flex:1;min-height:510px;}

/* Leaflet overrides */
.leaflet-container{background:#060d1f !important;}
.leaflet-control-zoom a{background:rgba(6,14,36,.95) !important;color:rgba(255,255,255,.7) !important;border:1px solid rgba(255,255,255,.1) !important;font-family:var(--font-head) !important;}
.leaflet-control-zoom a:hover{background:rgba(245,200,66,.12) !important;color:var(--gold) !important;}
.leaflet-control-attribution{background:rgba(4,9,20,.8) !important;color:rgba(255,255,255,.18) !important;font-size:9px !important;}
.leaflet-popup-content-wrapper{background:rgba(4,9,20,.96) !important;border:1px solid rgba(245,200,66,.3) !important;border-radius:12px !important;box-shadow:0 8px 32px rgba(0,0,0,.6) !important;color:white !important;font-family:var(--font-head) !important;padding:0 !important;}
.leaflet-popup-content{margin:0 !important;padding:0 !important;}
.leaflet-popup-tip{background:rgba(4,9,20,.96) !important;}
.leaflet-popup-close-button{color:rgba(255,255,255,.4) !important;font-size:16px !important;top:8px !important;right:10px !important;}
.leaflet-popup-close-button:hover{color:var(--gold) !important;}
.map-popup{padding:14px 16px;}
.map-popup-name{font-size:14px;font-weight:800;color:#fff;margin-bottom:3px;}
.map-popup-sub{font-size:10.5px;color:rgba(255,255,255,.38);letter-spacing:.3px;}
.map-popup-sites{margin-top:8px;display:flex;flex-direction:column;gap:3px;}
.map-popup-site{font-size:11px;color:rgba(255,255,255,.55);display:flex;align-items:center;gap:6px;}

/* ══════════════════════════════════
   RESPONSIVE
══════════════════════════════════ */
@media(max-width:1280px){
  .hero{gap:60px;padding-left:60px;padding-right:60px;}
  .hero-right{flex:0 0 460px;}
  .hero-left h1{font-size:58px;}
  .section,.content-section,.how-section,.ai-strip{padding-left:60px;padding-right:60px;}
}
@media(max-width:1100px){
  .hero{gap:44px;padding-left:40px;padding-right:40px;}
  .hero-right{flex:0 0 400px;}
  .hero-left h1{font-size:50px;}
  .heritage-carousel{height:270px;}
  .section,.content-section,.how-section,.ai-strip{padding-left:40px;padding-right:40px;}
  .features-grid{grid-template-columns:repeat(2,1fr);}
  .steps{grid-template-columns:repeat(2,1fr);}
  .map-section{padding-left:40px;padding-right:40px;}
  .map-info-panel{flex:0 0 270px;}
}
@media(max-width:900px){
  nav{padding:0 28px;}
  .hero{flex-direction:column;padding:calc(var(--nav-h) + var(--ticker-h) + 36px) 28px 64px;gap:40px;align-items:flex-start;}
  .hero-left{max-width:100%;}
  .hero-left h1{font-size:44px;}
  .hero-right{flex:unset;width:100%;max-width:460px;align-self:center;}
  .section,.content-section,.how-section,.ai-strip{padding-left:28px;padding-right:28px;}
  .map-section{padding:72px 28px;}
  .map-shell{flex-direction:column;}
  .map-info-panel{flex:unset;}
  .mip-default{padding:28px 18px;}
  #heritageMap{min-height:400px;}
}
@media(max-width:640px){
  :root{--nav-h:56px;--ticker-h:34px;}
  nav ul{display:none;}
  .hero{padding:calc(var(--nav-h) + var(--ticker-h) + 28px) 18px 48px;}
  .hero-left h1{font-size:34px;}
  .hero-right{max-width:100%;}
  .heritage-carousel{height:230px;}
  .features-grid{grid-template-columns:1fr;}
  .steps{grid-template-columns:1fr;}
  .step-connector{display:none;}
  .stat-item{padding:20px 24px;}
  .content-header{flex-direction:column;align-items:flex-start;gap:14px;}
  .cta-section h2{font-size:32px;}
  .ai-strip{flex-direction:column;gap:18px;}
  .ticker-label-text{display:none;}
  .map-section{padding:56px 18px;}
  #heritageMap{min-height:320px;}
  .map-legend{display:none;}
}
</style>
</head>
<body>

<!-- ── BACKGROUND ── -->
<div class="hero-bg">
  <div class="slide active"></div>
  <div class="slide"></div>
  <div class="slide"></div>
  <div class="hero-bg-overlay"></div>
  <div class="hero-bg-noise"></div>
</div>
<div class="bg-orbs"><div class="orb orb1"></div><div class="orb orb2"></div><div class="orb orb3"></div></div>
<div class="aurora-layer"><div class="aurora-band aurora1"></div><div class="aurora-band aurora2"></div><div class="aurora-band aurora3"></div></div>
<canvas id="circuit-canvas"></canvas>
<div class="float-cultural">🕌</div>
<div class="float-cultural">🏺</div>
<div class="float-cultural">🗿</div>
<div class="float-cultural">🏯</div>
<div class="float-cultural">🛕</div>
<div class="float-cultural">⚗️</div>
<canvas id="particles-canvas"></canvas>
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- ═══════════════════════════════
     SIDEBAR
═══════════════════════════════ -->
<aside class="sidebar" id="sidebar">
  <div class="sb-logo">
    <div class="sb-logo-ico"><svg viewBox="0 0 24 24" fill="none"><rect x="3" y="11" width="18" height="10" rx="2" stroke="white" stroke-width="1.8"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="white" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16" r="1.5" fill="white"/></svg></div>
    <div><div class="sb-logo-name">HeritechAI</div><div class="sb-logo-sub">Community Platform</div></div>
  </div>
  <div class="sb-nav">
    <a href="index.php" class="sb-item active"><span class="sb-ico"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></span><span class="sb-lbl">Dashboard</span></a>
    <a href="gallery.php?type=photo" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></span><span class="sb-lbl">AI Photo</span></a>
    <a href="gallery.php?type=video" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></span><span class="sb-lbl">AI Video</span></a>
    <a href="gallery.php?type=characters" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4L12 14.4l-4.8 2.5.9-5.4L4.2 7.7l5.4-.8z"/></svg></span><span class="sb-lbl">AI Characters</span></a>
    <a href="brands.php" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span><span class="sb-lbl">Brands</span></a>
    <div class="sb-div"></div>
    <div class="sb-grp">Explore</div>
    <div class="sb-item has-sub" id="compToggle" onclick="toggleSub('compSub','compToggle')"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg></span><span class="sb-lbl">Competition</span><span class="sb-arr"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span></div>
    <div class="sb-sub" id="compSub"><a href="competition.php?tab=active" class="sb-si"><span class="sb-dot"></span>Active Events</a><a href="competition.php?tab=past" class="sb-si"><span class="sb-dot"></span>Past Events</a><a href="competition.php?tab=leaderboard" class="sb-si"><span class="sb-dot"></span>Leaderboard</a></div>
    <div class="sb-item has-sub" id="teamsToggle" onclick="toggleSub('teamsSub','teamsToggle')"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><span class="sb-lbl">Teams</span><span class="sb-arr"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span></div>
    <div class="sb-sub" id="teamsSub"><a href="teams.php?tab=my" class="sb-si"><span class="sb-dot"></span>My Team</a><a href="teams.php?tab=browse" class="sb-si"><span class="sb-dot"></span>Browse Teams</a><a href="teams.php?tab=create" class="sb-si"><span class="sb-dot"></span>Create Team</a></div>
    <div class="sb-div"></div>
    <div class="sb-grp">Community</div>
    <a href="collaborators.php" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M10.5 20H4a2 2 0 0 1-2-2v-1a5 5 0 0 1 5-5h2.5"/><circle cx="9" cy="7" r="4"/><path d="m17 13 2 2 4-4"/></svg></span><span class="sb-lbl">Our Collaborators</span></a>
    <a href="about.php" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span><span class="sb-lbl">About Community</span></a>
    <?php if(isset($_SESSION['user_id'])): ?>
    <div class="sb-div"></div>
    <div class="sb-grp">My Space</div>
    <a href="submit.php" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></span><span class="sb-lbl">Upload Content</span></a>
    <a href="dashboard.php" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span><span class="sb-lbl">My Dashboard</span></a>
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
    <a href="admin/admin_panel.php" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span><span class="sb-lbl">Admin Panel</span></a>
    <?php endif; ?>
    <a href="account.php" class="sb-item"><span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="sb-lbl">Account Settings</span></a>
    <?php endif; ?>
  </div>
  <div class="sb-foot">
    <?php if(!isset($_SESSION['user_id'])): ?>
      <a href="register.php" class="sb-cta"><svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>Start Generating &amp; Join</a>
    <?php else: ?>
      <a href="submit.php" class="sb-cta"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>Upload New Content</a>
    <?php endif; ?>
    <a href="contact.php" class="sb-help"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>Help &amp; Support</a>
    <div class="sb-copy">© <?php echo date("Y"); ?> <b>HeritechAI</b> · Built for Innovators</div>
  </div>
</aside>

<!-- ═══════════════════════════════
     NAVBAR
═══════════════════════════════ -->
<nav id="mainNav">
  <div class="nav-left">
    <div class="hamburger" id="hamburger" onclick="toggleSidebar()">
      <span></span><span></span><span></span>
    </div>
    <div class="logo">
      <span class="logo-text">HeritechAI</span>
    </div>
  </div>
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
      <li><a href="contact.php">Contact</a></li>
      <li><a href="login.php">Login</a></li>
      <li><a href="register.php" class="btn-nav">Register →</a></li>
    <?php endif; ?>
  </ul>
</nav>

<!-- ═══════════════════════════════
     TICKER
═══════════════════════════════ -->
<div class="ticker-wrap" id="tickerWrap">
  <div class="ticker-label">
    <span class="ticker-label-dot"></span>
  </div>
  <div class="ticker-track">
    <span class="ticker-item">🕌 Taj Mahal<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🛕 Hampi Ruins<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🏯 Hawa Mahal<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🗿 Ajanta Caves<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">☀️ Konark Sun Temple<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🗼 Qutub Minar<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🌊 Rani ki Vav<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🏔️ Khajuraho Temples<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🛕 Meenakshi Temple<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">⛩️ Red Fort, Delhi<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🏛️ Ellora Caves<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🌿 Sundarbans<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🕌 Taj Mahal<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🛕 Hampi Ruins<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🏯 Hawa Mahal<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🗿 Ajanta Caves<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">☀️ Konark Sun Temple<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🗼 Qutub Minar<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🌊 Rani ki Vav<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🏔️ Khajuraho Temples<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🛕 Meenakshi Temple<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">⛩️ Red Fort, Delhi<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🏛️ Ellora Caves<span class="ticker-sep">✦</span></span>
    <span class="ticker-item">🌿 Sundarbans<span class="ticker-sep">✦</span></span>
  </div>
</div>

<!-- ═══════════════════════════════
     HERO
═══════════════════════════════ -->
<section class="hero">
  <div class="hero-left">
    <div class="heritage-badge">
      <span class="pulse-dot"></span>
      🏛️ Heritage Meets Artificial Intelligence
    </div>
    <h1>
      <span class="line-serif">HERITAGE.</span>
      <span class="line-tech">Innovation.</span>
      <span class="line-plain">Future.</span>
    </h1>
    <p class="hero-tagline">Where ancient wisdom meets cutting-edge AI. Upload videos, images, audio, and research. Connect with innovators worldwide and shape the future where culture and technology converge.</p>
    <br>
    <div class="hero-btns">
      <?php if(!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn-hero btn-primary">🚀 Join the Community</a>
        <a href="login.php" class="btn-hero btn-electric">Login →</a>
      <?php else: ?>
        <a href="submit.php" class="btn-hero btn-primary">📤 Upload Content</a>
        <a href="dashboard.php" class="btn-hero btn-electric">My Dashboard →</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="hero-right">
    <div class="struct-glow struct-glow-1"></div>
    <div class="struct-glow struct-glow-2"></div>
    <div class="heritage-carousel">
      <div class="carousel-frame"></div>
      <div class="corner-tl"></div><div class="corner-tr"></div>
      <div class="corner-bl"></div><div class="corner-br"></div>
      <div class="scan-beam"></div>
      <div class="img-scan"></div>
      <div class="carousel-slides">
        <div class="c-slide active"><div class="slide-overlay"></div><div class="slide-caption"><span class="cap-icon">🕌</span><span class="cap-text">Taj Mahal — Agra, Uttar Pradesh</span></div></div>
        <div class="c-slide"><div class="slide-overlay"></div><div class="slide-caption"><span class="cap-icon">🛕</span><span class="cap-text">Hampi Ruins — Karnataka</span></div></div>
        <div class="c-slide"><div class="slide-overlay"></div><div class="slide-caption"><span class="cap-icon">🏯</span><span class="cap-text">Hawa Mahal — Jaipur, Rajasthan</span></div></div>
        <div class="c-slide"><div class="slide-overlay"></div><div class="slide-caption"><span class="cap-icon">🗿</span><span class="cap-text">Ajanta Caves — Chhatrapati Sambhaji Nagar, Maharashtra</span></div></div>
        <div class="c-slide"><div class="slide-overlay"></div><div class="slide-caption"><span class="cap-icon">☀️</span><span class="cap-text">Konark Sun Temple — Odisha</span></div></div>
        <div class="c-slide"><div class="slide-overlay"></div><div class="slide-caption"><span class="cap-icon">🗼</span><span class="cap-text">Qutub Minar — New Delhi</span></div></div>
      </div>
      <div class="carousel-btn prev" onclick="goSlide(currentSlide-1)">&#8592;</div>
      <div class="carousel-btn next" onclick="goSlide(currentSlide+1)">&#8594;</div>
      <div class="carousel-dots" id="dotsContainer">
        <span class="cdot active" onclick="goSlide(0)"></span>
        <span class="cdot" onclick="goSlide(1)"></span>
        <span class="cdot" onclick="goSlide(2)"></span>
        <span class="cdot" onclick="goSlide(3)"></span>
        <span class="cdot" onclick="goSlide(4)"></span>
        <span class="cdot" onclick="goSlide(5)"></span>
      </div>
      <div class="ai-badge"><span class="ai-dot"></span><span class="ai-label">AI Analyzing</span></div>
      <div class="stats-chip"><div class="chip-row"><span class="chip-num" id="carouselSiteNum">1</span><span class="chip-sub">&nbsp;/ 6 Sites</span></div></div>
    </div>

    <div class="site-info-panel" id="siteInfoPanel">
      <div class="sip-top">
        <div><div class="sip-name" id="sipName">Taj Mahal</div><div class="sip-loc" id="sipLoc">📍 Agra, Uttar Pradesh — India</div></div>
        <div class="sip-era" id="sipEra">1632–1653 CE</div>
      </div>
      <div class="sip-desc" id="sipDesc">Iconic white marble mausoleum built by Mughal emperor Shah Jahan for Mumtaz Mahal. One of the Seven Wonders of the World and India's most celebrated UNESCO World Heritage Site.</div>
      <div class="sip-facts" id="sipFacts">
        <div class="sip-fact"><div class="sif-v">73m</div><div class="sif-l">Height</div></div>
        <div class="sip-fact"><div class="sif-v">17ha</div><div class="sif-l">Complex</div></div>
        <div class="sip-fact"><div class="sif-v">UNESCO</div><div class="sif-l">Status</div></div>
      </div>
      <div class="sip-styles" id="sipStyles">
        <span class="style-pill sp-gold">Mughal</span>
        <span class="style-pill sp-cyan">Persian</span>
        <span class="style-pill sp-purple">Islamic</span>
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-bar">
  <div class="stat-item reveal"><span class="stat-num" data-count="<?php echo $total_users; ?>"><?php echo $total_users; ?>+</span><div class="stat-lbl">Community Members</div></div>
  <div class="stat-item reveal" style="transition-delay:.1s"><span class="stat-num" data-count="<?php echo $total_content; ?>"><?php echo $total_content; ?>+</span><div class="stat-lbl">Approved Works</div></div>
  <div class="stat-item reveal" style="transition-delay:.2s"><span class="stat-num">4+</span><div class="stat-lbl">Content Categories</div></div>
  <div class="stat-item reveal" style="transition-delay:.3s"><span class="stat-num">24/7</span><div class="stat-lbl">Admin Review</div></div>
</div>

<!-- FEATURES -->
<section class="section">
  <div class="section-label">What We Offer</div>
  <div class="section-title">Everything You Need<br>to Share AI Work</div>
  <div class="section-sub">From AI-generated art to research papers — share any format with our growing community of innovators preserving cultural heritage through AI.</div>
  <div class="features-grid">
    <div class="feat-card reveal" style="transition-delay:.05s"><span class="feat-icon">🎥</span><h3>Video &amp; Audio</h3><p>Share AI-generated videos, demos, tutorials, and audio projects exploring cultural narratives.</p></div>
    <div class="feat-card reveal" style="transition-delay:.1s"><span class="feat-icon">🖼️</span><h3>Images &amp; Art</h3><p>Showcase AI-generated artwork, heritage visualizations, and creative image projects.</p></div>
    <div class="feat-card reveal" style="transition-delay:.15s"><span class="feat-icon">📄</span><h3>Documents &amp; Research</h3><p>Upload PDFs, presentations, and research papers on AI and cultural innovation.</p></div>
    <div class="feat-card reveal" style="transition-delay:.2s"><span class="feat-icon">🛡️</span><h3>Admin Reviewed</h3><p>Every upload is reviewed by our admin team to ensure quality content for the community.</p></div>
    <div class="feat-card reveal" style="transition-delay:.25s"><span class="feat-icon">🌍</span><h3>Global Network</h3><p>Connect with AI enthusiasts, researchers, and heritage innovators from around the world.</p></div>
    <div class="feat-card reveal" style="transition-delay:.3s"><span class="feat-icon">⚡</span><h3>Instant Access</h3><p>Download and access all approved community content instantly with no restrictions.</p></div>
  </div>
</section>

<!-- COMMUNITY CONTENT -->
<section class="content-section">
  <div class="content-header">
    <div>
      <div class="section-label">Community Uploads</div>
      <div class="section-title" style="font-size:32px;margin-bottom:0;">Latest Approved Content</div>
    </div>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="submit.php" class="btn-hero btn-primary" style="padding:11px 26px;font-size:13.5px;">+ Upload Yours</a>
    <?php else: ?>
      <a href="register.php" class="btn-hero btn-primary" style="padding:11px 26px;font-size:13.5px;">Join to Upload</a>
    <?php endif; ?>
  </div>
  <div class="content-grid">
  <?php
  $img_exts=['jpg','jpeg','png','gif'];$video_exts=['mp4','avi','mov','mkv'];$audio_exts=['mp3','wav'];
  $icons=['pdf'=>'📄','doc'=>'📝','docx'=>'📝','ppt'=>'📊','pptx'=>'📊','xls'=>'📋','xlsx'=>'📋','zip'=>'🗜️','rar'=>'🗜️','txt'=>'📃'];
  if($approved_content && $approved_content->num_rows > 0):
    $delay=0;
    while($item=$approved_content->fetch_assoc()):
      $ext=strtolower(pathinfo($item['file_path'],PATHINFO_EXTENSION));
      $delay+=0.05;
  ?>
    <div class="content-card reveal" style="transition-delay:<?php echo $delay;?>s">
      <div class="card-media">
        <?php if(in_array($ext,$img_exts)): ?><img src="<?php echo htmlspecialchars($item['file_path']);?>" alt="<?php echo htmlspecialchars($item['title']);?>" loading="lazy">
        <?php elseif(in_array($ext,$video_exts)): ?><video controls preload="none"><source src="<?php echo htmlspecialchars($item['file_path']);?>"></video>
        <?php elseif(in_array($ext,$audio_exts)): ?><audio controls><source src="<?php echo htmlspecialchars($item['file_path']);?>"></audio>
        <?php else: ?><div class="file-emoji"><?php echo $icons[$ext]??'📁';?></div><?php endif;?>
      </div>
      <div class="card-body">
        <div class="card-cat"><?php echo htmlspecialchars($item['category']);?></div>
        <div class="card-title"><?php echo htmlspecialchars($item['title']);?></div>
        <div class="card-desc"><?php echo htmlspecialchars($item['description']);?></div>
        <div class="card-footer">
          <span class="card-author">by <?php echo htmlspecialchars($item['uploader_name']);?></span>
          <a class="card-download" href="<?php echo htmlspecialchars($item['file_path']);?>" download>⬇ Download</a>
        </div>
      </div>
    </div>
  <?php endwhile; else: ?>
    <div class="empty-state">
      <div class="empty-icon">🏛️</div><h3>No Content Yet</h3>
      <p>Be the first to upload heritage AI content to the community!</p>
      <a href="<?php echo isset($_SESSION['user_id'])?'submit.php':'register.php';?>" class="btn-hero btn-primary">
        <?php echo isset($_SESSION['user_id'])?'📤 Upload Now':'🚀 Join &amp; Upload';?>
      </a>
    </div>
  <?php endif;?>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     INDIA HERITAGE MAP
══════════════════════════════════════════════════════ -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<section class="map-section" id="heritageMapSection">
  <div class="map-header reveal">
    <div class="section-label">Explore India</div>
    <div class="section-title">Interactive <span class="map-title-accent">Heritage Map</span></div>
    <p class="section-sub">Click any state marker to discover its iconic heritage sites, UNESCO monuments, and cultural highlights.</p>
  </div>

  <div class="map-shell">
    <!-- LEFT: Info Panel -->
    <div class="map-info-panel" id="mapInfoPanel">
      <div class="mip-default" id="mipDefault">
        <div class="mip-default-icon">🗺️</div>
        <div class="mip-default-title">Explore Heritage</div>
        <div class="mip-default-sub">Click any glowing marker on the map to discover that state's history, culture and heritage sites.</div>
        <div class="mip-hint-row">
          <span class="mip-hint">🕌 Monuments</span>
          <span class="mip-hint">🏛️ UNESCO Sites</span>
          <span class="mip-hint">🎨 Art Forms</span>
        </div>
      </div>
      <div class="mip-content" id="mipContent" style="display:none;flex-direction:column;flex:1;overflow-y:auto;">
        <div class="mip-state-banner">
          <div class="mip-state-emoji" id="mipEmoji">🏛️</div>
          <div class="mip-state-info">
            <div class="mip-state-name" id="mipName">State</div>
            <div class="mip-state-capital" id="mipCapital">Capital</div>
          </div>
          <div class="mip-state-badge" id="mipBadge">Heritage</div>
        </div>
        <div class="mip-tagline" id="mipTagline"></div>
        <div class="mip-facts-row" id="mipFactsRow"></div>
        <div class="mip-block">
          <div class="mip-block-label">⭐ Famous Heritage Sites</div>
          <div class="mip-sites" id="mipSites"></div>
        </div>
        <div class="mip-block">
          <div class="mip-block-label">🎨 Cultural Highlights</div>
          <div class="mip-pills" id="mipPills"></div>
        </div>
        <div class="mip-block">
          <div class="mip-block-label">📖 About</div>
          <div class="mip-about" id="mipAbout"></div>
        </div>
        <div class="mip-block" id="mipUNESCOBlock">
          <div class="mip-block-label">🏆 UNESCO World Heritage</div>
          <div class="mip-unesco" id="mipUNESCO"></div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Map -->
    <div class="map-container-wrap">
      <div class="map-top-bar">
        <div class="map-title-bar">
          <span class="map-live-dot"></span>
          <span class="map-title-text">India — Heritage Explorer · 29 States & 8 UTs</span>
        </div>
        <div class="map-legend">
          <span class="legend-item"><span class="legend-dot ld-gold"></span>Heritage State</span>
          <span class="legend-item"><span class="legend-dot ld-purple"></span>UNESCO Rich</span>
          <span class="legend-item"><span class="legend-dot ld-cyan"></span>Selected</span>
        </div>
      </div>
      <div id="heritageMap"></div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section">
  <div style="text-align:center;">
    <div class="section-label" style="justify-content:center;">How It Works</div>
    <div class="section-title" style="font-size:38px;text-align:center;">4 Simple Steps</div>
  </div>
  <div class="steps">
    <div class="step reveal"><div class="step-connector"></div><div class="step-num">1</div><h3>Register</h3><p>Create your free account and verify your email to join the heritage AI community.</p></div>
    <div class="step reveal" style="transition-delay:.1s"><div class="step-connector"></div><div class="step-num">2</div><h3>Upload</h3><p>Share your AI projects — videos, images, audio, documents and more.</p></div>
    <div class="step reveal" style="transition-delay:.2s"><div class="step-connector"></div><div class="step-num">3</div><h3>Review</h3><p>Our admin team reviews your content to maintain quality standards.</p></div>
    <div class="step reveal" style="transition-delay:.3s"><div class="step-connector"></div><div class="step-num">4</div><h3>Go Live</h3><p>Approved content appears on the homepage for the world to see!</p></div>
  </div>
</section>

<?php if(!isset($_SESSION['user_id'])): ?>
<section class="cta-section">
  <div class="cta-bg"></div>
  <h2>Ready to <span style="background:linear-gradient(90deg,var(--gold),var(--amber));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Join?</span></h2>
  <p>Join thousands of AI innovators and heritage enthusiasts sharing their work every day.</p>
  <a href="register.php" class="btn-hero btn-primary" style="font-size:17px;padding:15px 44px;">Get Started for Free →</a>
</section>
<?php endif;?>

<footer><p>© <?php echo date("Y");?> HeritechAI Community — Where Heritage Meets Innovation 🏛️🤖</p></footer>

<!-- ═══════════════════════════════
     SCRIPTS
═══════════════════════════════ -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
/* ════════════════════════════════════════════
   STATE DATA — all states including new NE additions
════════════════════════════════════════════ */
var STATES = [
  {name:"Uttar Pradesh",lat:26.8,lng:80.9,emoji:"🕌",capital:"Lucknow · North India",badge:"Spiritual Heartland",tagline:'"Taj Mahal, Varanasi ghats & the birthplace of Ram"',facts:[{v:"3",l:"UNESCO Sites"},{v:"1632",l:"Taj Mahal"},{v:"5000yr",l:"Varanasi"}],sites:[{i:"🕌",n:"Taj Mahal, Agra"},{i:"🛕",n:"Varanasi Ghats"},{i:"☸️",n:"Sarnath — Buddha's First Sermon"},{i:"🏯",n:"Agra Fort"}],pills:[{c:"mp-gold",t:"Mughal Heritage"},{c:"mp-cyan",t:"Chikankari Craft"},{c:"mp-purple",t:"Kathak Dance"},{c:"mp-green",t:"Hindu Pilgrimage"}],about:"Varanasi is one of the world's oldest continuously inhabited cities — 3,000 years old. Sarnath is where the Buddha gave his first sermon after enlightenment.",unesco:["Taj Mahal","Agra Fort","Fatehpur Sikri"],color:"gold"},
  {name:"Rajasthan",lat:27.0,lng:74.2,emoji:"🏯",capital:"Jaipur · North India",badge:"Land of Maharajas",tagline:'"Desert forts, painted havelis & the royal heritage of Rajputana"',facts:[{v:"6",l:"UNESCO Sites"},{v:"1000+",l:"Forts & Palaces"},{v:"15th C",l:"Golden Era"}],sites:[{i:"🏯",n:"Amber Fort, Jaipur"},{i:"🌊",n:"Lake Palace, Udaipur"},{i:"🏔️",n:"Jaisalmer Desert Fort"},{i:"🛕",n:"Dilwara Jain Temples"}],pills:[{c:"mp-gold",t:"Rajput Heritage"},{c:"mp-cyan",t:"Blue Pottery"},{c:"mp-purple",t:"Ghoomar Dance"},{c:"mp-green",t:"Block Printing"}],about:"Rajasthan is home to the largest number of forts and palaces in the world. The Hill Forts of Rajasthan UNESCO inscription includes Chittorgarh, Kumbhalgarh, Amber, Jaisalmer and more.",unesco:["Hill Forts of Rajasthan","Jantar Mantar — Jaipur","Keoladeo National Park"],color:"gold"},
  {name:"Maharashtra",lat:19.7,lng:75.7,emoji:"🏯",capital:"Mumbai · West India",badge:"Maratha Empire",tagline:'"Ajanta caves, Shivaji forts & the city of dreams"',facts:[{v:"5",l:"UNESCO Sites"},{v:"2nd C BCE",l:"Ajanta Art"},{v:"350+",l:"Forts"}],sites:[{i:"🗿",n:"Ajanta Caves"},{i:"⛩️",n:"Ellora Caves"},{i:"🏝️",n:"Elephanta Caves, Mumbai"},{i:"🏯",n:"Raigad Fort"}],pills:[{c:"mp-gold",t:"Maratha Heritage"},{c:"mp-cyan",t:"Lavani Dance"},{c:"mp-purple",t:"Buddhist Rock Art"},{c:"mp-green",t:"Warli Painting"}],about:"Maharashtra's Ajanta frescoes are the finest surviving examples of ancient Indian art. The Maratha Empire under Shivaji created India's first naval force and 350+ hillforts.",unesco:["Ajanta Caves","Ellora Caves","Elephanta Caves","Victorian & Art Deco Mumbai"],color:"purple"},
  {name:"Tamil Nadu",lat:11.1,lng:78.6,emoji:"🛕",capital:"Chennai · South India",badge:"Dravidian Civilization",tagline:'"Gopurams to the sky, Carnatic music & Sangam poetry"',facts:[{v:"2",l:"UNESCO Sites"},{v:"3rd C BCE",l:"Sangam Age"},{v:"33000+",l:"Temples"}],sites:[{i:"🛕",n:"Brihadeeswarar Temple, Thanjavur"},{i:"🏛️",n:"Mahabalipuram Shore Temple"},{i:"🌊",n:"Marina Beach"},{i:"🎭",n:"Bharatanatyam Origin"}],pills:[{c:"mp-gold",t:"Dravidian Architecture"},{c:"mp-cyan",t:"Carnatic Music"},{c:"mp-purple",t:"Bharatanatyam"},{c:"mp-green",t:"Silk Weaving"}],about:"Tamil Nadu's Sangam poetry from 300 BCE is the oldest surviving Indian literature. Bharatanatyam, performed for 2,000 years in temple courts, is the world's most widely practised classical dance.",unesco:["Mahabalipuram","Great Living Chola Temples"],color:"gold"},
  {name:"Karnataka",lat:15.3,lng:75.7,emoji:"🛕",capital:"Bengaluru · South India",badge:"Vijayanagara Empire",tagline:'"Temples of stone, silk of gold & ruins of a great empire"',facts:[{v:"3",l:"UNESCO Sites"},{v:"14th C",l:"Vijayanagara"},{v:"850+",l:"Monuments"}],sites:[{i:"🛕",n:"Hampi — Vijayanagara Ruins"},{i:"🏯",n:"Mysore Palace"},{i:"🌊",n:"Jog Falls"},{i:"⛩️",n:"Badami Cave Temples"}],pills:[{c:"mp-gold",t:"Dravidian Architecture"},{c:"mp-cyan",t:"Carnatic Music"},{c:"mp-purple",t:"Mysore Silk"},{c:"mp-green",t:"Yakshagana"}],about:"Hampi was the capital of the Vijayanagara Empire — once the second-largest city in the world. Mysore Dasara is one of India's most celebrated royal festivals.",unesco:["Monuments at Hampi","Monuments at Pattadakal"],color:"purple"},
  {name:"Gujarat",lat:22.2,lng:71.1,emoji:"🌊",capital:"Gandhinagar · West India",badge:"Cradle of Gandhi",tagline:'"Stepwells, salt flats & birthplace of the Mahatma"',facts:[{v:"3",l:"UNESCO Sites"},{v:"3rd C BCE",l:"Ashoka Edicts"},{v:"1600km",l:"Coastline"}],sites:[{i:"🌊",n:"Rani ki Vav Stepwell, Patan"},{i:"🦁",n:"Gir Forest — Last Asiatic Lions"},{i:"🏛️",n:"Dholavira — Harappan City"},{i:"🛕",n:"Somnath Temple"}],pills:[{c:"mp-gold",t:"Harappan Civilisation"},{c:"mp-cyan",t:"Gujarati Craft"},{c:"mp-purple",t:"Patola Weaving"},{c:"mp-green",t:"Garba Dance"}],about:"Gujarat's Dholavira is one of the largest Harappan cities — 5,000 years old. Rani ki Vav is a UNESCO stepwell masterpiece. The Rann of Kutch salt flats are among Earth's most surreal landscapes.",unesco:["Rani-ki-Vav (Queen's Stepwell)","Champaner-Pavagadh","Dholavira: A Harappan City"],color:"purple"},
  {name:"Madhya Pradesh",lat:23.4,lng:77.4,emoji:"🐯",capital:"Bhopal · Central India",badge:"Heart of India",tagline:'"Sanchi stupa, Khajuraho temples & tiger reserves"',facts:[{v:"3",l:"UNESCO Sites"},{v:"3rd C BCE",l:"Buddhist Heritage"},{v:"10th–11th C",l:"Khajuraho"}],sites:[{i:"🏛️",n:"Khajuraho Temples"},{i:"☸️",n:"Sanchi Stupa"},{i:"🐯",n:"Kanha Tiger Reserve"},{i:"🗿",n:"Bhimbetka Rock Shelters"}],pills:[{c:"mp-gold",t:"Chandela Architecture"},{c:"mp-cyan",t:"Buddhist Heritage"},{c:"mp-purple",t:"Gond Tribal Art"},{c:"mp-green",t:"Classical Dance"}],about:"MP has the highest number of national parks after Andaman. Bhimbetka's rock art dates back 30,000 years. Sanchi is where Ashoka built the first Buddhist stupa after his conversion.",unesco:["Khajuraho Group of Monuments","Buddhist Monuments at Sanchi","Rock Shelters of Bhimbetka"],color:"purple"},
  {name:"Kerala",lat:10.8,lng:76.2,emoji:"🌴",capital:"Thiruvananthapuram · South India",badge:"God's Own Country",tagline:'"Backwaters, spice coast & a living tradition of arts"',facts:[{v:"1",l:"UNESCO Site"},{v:"100%",l:"Literacy"},{v:"Ancient",l:"Spice Trade"}],sites:[{i:"🌴",n:"Kerala Backwaters, Alleppey"},{i:"🛕",n:"Padmanabhaswamy Temple"},{i:"🌿",n:"Silent Valley National Park"},{i:"🎨",n:"Thrissur Pooram Festival"}],pills:[{c:"mp-gold",t:"Kathakali Dance"},{c:"mp-cyan",t:"Ayurveda"},{c:"mp-purple",t:"Kalaripayattu"},{c:"mp-green",t:"Spice Trade History"}],about:"Kalaripayattu is considered the world's oldest martial art. Kathakali dance-drama and Mohiniyattam are UNESCO-recognised performing arts. The spice trade made Kerala's coast the most traded route in medieval history.",unesco:["Western Ghats (shared)"],color:"gold"},
  {name:"West Bengal",lat:22.9,lng:87.8,emoji:"🎭",capital:"Kolkata · East India",badge:"Cultural Capital",tagline:'"Tagore, Durga Puja & the Sundarbans mangroves"',facts:[{v:"1",l:"UNESCO Site"},{v:"1913",l:"Nobel Prize"},{v:"Victorian",l:"Colonial Heritage"}],sites:[{i:"🌿",n:"Sundarbans National Park"},{i:"🏛️",n:"Victoria Memorial, Kolkata"},{i:"🛕",n:"Dakshineswar Kali Temple"},{i:"🎭",n:"Tagore's Jorasanko"}],pills:[{c:"mp-gold",t:"Bengali Literature"},{c:"mp-cyan",t:"Durga Puja"},{c:"mp-purple",t:"Baul Music"},{c:"mp-green",t:"Terracotta Temples"}],about:"Rabindranath Tagore composed both India's and Bangladesh's national anthems. The Durga Puja of Kolkata is a UNESCO Intangible Cultural Heritage. Sundarbans is the world's largest mangrove forest.",unesco:["Sundarbans National Park"],color:"gold"},
  {name:"Punjab",lat:31.1,lng:75.3,emoji:"⚔️",capital:"Chandigarh · North India",badge:"Land of Five Rivers",tagline:'"Golden Temple, warrior saints & heartland of Sikhism"',facts:[{v:"1604 CE",l:"Golden Temple"},{v:"5",l:"Rivers"},{v:"Sikh",l:"Heartland"}],sites:[{i:"⛪",n:"Harmandir Sahib — Golden Temple"},{i:"⚔️",n:"Wagah Border Ceremony"},{i:"🏯",n:"Gobindgarh Fort"},{i:"🛕",n:"Anandpur Sahib"}],pills:[{c:"mp-gold",t:"Sikh Heritage"},{c:"mp-cyan",t:"Bhangra Dance"},{c:"mp-purple",t:"Phulkari Craft"},{c:"mp-green",t:"Punjabi Folk Music"}],about:"The Golden Temple serves a free meal to over 100,000 visitors daily — the world's largest community kitchen. The first Sikh Guru, Guru Nanak Dev Ji, was born in this region in 1469.",unesco:[],color:"gold"},
  {name:"Odisha",lat:20.9,lng:85.0,emoji:"☀️",capital:"Bhubaneswar · East India",badge:"Temple City of India",tagline:'"Sun temples, classical dance & Bay of Bengal shores"',facts:[{v:"1",l:"UNESCO Site"},{v:"8th–12th C",l:"Temple Period"},{v:"5000+",l:"Temples"}],sites:[{i:"☀️",n:"Konark Sun Temple"},{i:"🛕",n:"Jagannath Temple, Puri"},{i:"🌊",n:"Chilika Lake"},{i:"🏯",n:"Udayagiri & Khandagiri Caves"}],pills:[{c:"mp-gold",t:"Odissi Dance"},{c:"mp-cyan",t:"Kalinga Architecture"},{c:"mp-purple",t:"Pattachitra Painting"},{c:"mp-green",t:"Sambalpuri Weaving"}],about:"Bhubaneswar has over 600 temples, earning it 'Temple City of India'. Odissi is one of the oldest surviving dance forms in the world. The Rath Yatra at Puri draws millions each year.",unesco:["Sun Temple, Konark"],color:"gold"},
  {name:"Bihar",lat:25.0,lng:85.3,emoji:"☸️",capital:"Patna · East India",badge:"Cradle of Civilization",tagline:'"Where Buddha attained enlightenment & Mahavira was born"',facts:[{v:"1",l:"UNESCO Site"},{v:"5th C BCE",l:"Nalanda"},{v:"3rd C BCE",l:"Ashoka"}],sites:[{i:"☸️",n:"Bodh Gaya"},{i:"🏛️",n:"Nalanda University Ruins"},{i:"🛕",n:"Mahabodhi Temple"},{i:"⚱️",n:"Patna Museum"}],pills:[{c:"mp-gold",t:"Buddhist"},{c:"mp-cyan",t:"Jain Heritage"},{c:"mp-purple",t:"Mauryan Empire"},{c:"mp-green",t:"Madhubani Art"}],about:"Bihar is the seat of ancient civilizations — home to Magadha, Mauryas, and Guptas. Nalanda was the world's first residential university. Madhubani painting is living UNESCO cultural heritage.",unesco:["Mahabodhi Temple Complex at Bodh Gaya"],color:"purple"},
  {name:"Himachal Pradesh",lat:31.1,lng:77.1,emoji:"🏔️",capital:"Shimla · North India",badge:"Hill Stations & Temples",tagline:'"Where deodar forests meet ancient temple kingdoms"',facts:[{v:"1",l:"UNESCO Site"},{v:"2000+",l:"Temples"},{v:"3978m",l:"Altitude"}],sites:[{i:"🏔️",n:"Great Himalayan National Park"},{i:"🛕",n:"Hidimba Devi Temple, Manali"},{i:"🏯",n:"Kangra Fort"},{i:"🎨",n:"Spiti Valley Monasteries"}],pills:[{c:"mp-gold",t:"Buddhist Monasteries"},{c:"mp-cyan",t:"Pahari Painting"},{c:"mp-purple",t:"Hill Architecture"},{c:"mp-green",t:"Kullu Dussehra"}],about:"Kullu Dussehra is declared a Festival of National Importance. Spiti Valley contains ancient Buddhist monasteries that predate Tibetan Buddhism. The Pahari painting school produced exquisite miniatures.",unesco:["Great Himalayan National Park"],color:"gold"},
  {name:"Uttarakhand",lat:30.0,lng:79.3,emoji:"🏔️",capital:"Dehradun · North India",badge:"Dev Bhoomi",tagline:'"Char Dham, Valley of Flowers & Yoga homeland"',facts:[{v:"2",l:"UNESCO Sites"},{v:"5000m+",l:"Himalayan Peaks"},{v:"Ancient",l:"Vedic Traditions"}],sites:[{i:"🛕",n:"Kedarnath Temple"},{i:"🌸",n:"Valley of Flowers"},{i:"🐯",n:"Jim Corbett National Park"},{i:"🏔️",n:"Nanda Devi National Park"}],pills:[{c:"mp-gold",t:"Char Dham Pilgrimage"},{c:"mp-cyan",t:"Yoga Heritage"},{c:"mp-purple",t:"Kumaoni Painting"},{c:"mp-green",t:"Himalayan Ecology"}],about:"Uttarakhand is Dev Bhoomi (Land of Gods) — home to Char Dham. Rishikesh is the yoga capital of the world. The Valley of Flowers blooms with hundreds of alpine wildflower species.",unesco:["Nanda Devi & Valley of Flowers"],color:"gold"},
  {name:"Delhi",lat:28.6,lng:77.2,emoji:"⛩️",capital:"New Delhi · Capital Territory",badge:"Capital Heritage",tagline:'"Seven cities, Mughal grandeur & seat of democracy"',facts:[{v:"3",l:"UNESCO Sites"},{v:"7",l:"Historic Cities"},{v:"3rd C BCE",l:"Oldest Settlement"}],sites:[{i:"⛩️",n:"Red Fort"},{i:"🗼",n:"Qutub Minar"},{i:"🕌",n:"Humayun's Tomb"},{i:"🏛️",n:"India Gate"}],pills:[{c:"mp-gold",t:"Mughal Heritage"},{c:"mp-cyan",t:"Chandni Chowk"},{c:"mp-purple",t:"Lutyens Architecture"},{c:"mp-green",t:"Street Food Heritage"}],about:"Delhi has been the capital of at least eight major empires. Humayun's Tomb directly inspired the Taj Mahal's design built 90 years later. The city hosts three UNESCO World Heritage Sites.",unesco:["Red Fort Complex","Humayun's Tomb","Qutub Minar & Monuments"],color:"purple"},
  {name:"Goa",lat:15.2,lng:74.0,emoji:"⛪",capital:"Panaji · West India",badge:"Portuguese Heritage",tagline:'"Spice routes, baroque churches & golden beaches"',facts:[{v:"1",l:"UNESCO Site"},{v:"450 Yrs",l:"Portuguese Rule"},{v:"1961",l:"Liberation"}],sites:[{i:"⛪",n:"Basilica of Bom Jesus"},{i:"🏯",n:"Chapora Fort"},{i:"🛕",n:"Shri Mangeshi Temple"},{i:"🌊",n:"Old Goa Churches"}],pills:[{c:"mp-gold",t:"Portuguese Baroque"},{c:"mp-cyan",t:"Konkani Culture"},{c:"mp-purple",t:"Spice Heritage"},{c:"mp-green",t:"Colonial Architecture"}],about:"Goa was the capital of Portuguese India for 450 years. Its baroque churches, spice plantations, and Konkani culture make it a UNESCO-recognised heritage destination.",unesco:["Churches and Convents of Goa"],color:"gold"},
  {name:"Assam",lat:26.2,lng:92.9,emoji:"🐘",capital:"Dispur · Northeast India",badge:"Tea & Wildlife",tagline:'"Where rhinos roam and silk is woven in gold"',facts:[{v:"2",l:"UNESCO Sites"},{v:"1 Horn",l:"Rhino Park"},{v:"6th C",l:"History"}],sites:[{i:"🦏",n:"Kaziranga National Park"},{i:"🛕",n:"Kamakhya Temple"},{i:"🌿",n:"Manas Wildlife Sanctuary"},{i:"🏯",n:"Sibasagar Ahom Monuments"}],pills:[{c:"mp-gold",t:"Bihu Dance"},{c:"mp-cyan",t:"Muga Silk"},{c:"mp-purple",t:"Ahom Kingdom"},{c:"mp-green",t:"Tea Heritage"}],about:"Assam's Ahom kingdom ruled for 600 years — one of Asia's longest-surviving dynasties. Kaziranga is home to two-thirds of the world's one-horned rhinoceroses.",unesco:["Kaziranga National Park","Manas Wildlife Sanctuary"],color:"purple"},
  {name:"Andhra Pradesh",lat:15.9,lng:79.7,emoji:"🏛️",capital:"Amaravati · South India",badge:"Ancient Kingdoms",tagline:'"Land of Telugu people — rivers, temples & rock art"',facts:[{v:"3",l:"UNESCO Sites"},{v:"13th C",l:"Heritage"},{v:"Telugu",l:"Classical Language"}],sites:[{i:"🏯",n:"Charminar, Hyderabad"},{i:"🛕",n:"Tirupati Balaji Temple"},{i:"⛰️",n:"Undavalli Caves"},{i:"🌊",n:"Borra Caves"}],pills:[{c:"mp-gold",t:"Dravidian"},{c:"mp-cyan",t:"Buddhist Heritage"},{c:"mp-purple",t:"Telugu Culture"},{c:"mp-green",t:"Kuchipudi Dance"}],about:"Ancient land ruled by Satavahanas, Kakatiyas & Vijayanagara empire. Tirupati Balaji is the world's most visited pilgrimage site, receiving over 50,000 devotees daily.",unesco:[],color:"gold"},
  {name:"Telangana",lat:17.4,lng:78.5,emoji:"🏯",capital:"Hyderabad · South India",badge:"Nizam's Kingdom",tagline:'"Golconda forts, biryani & diamond mines of the Deccan"',facts:[{v:"1",l:"UNESCO Site"},{v:"16th C",l:"Golconda Era"},{v:"Kohinoor",l:"Birthplace"}],sites:[{i:"🏯",n:"Golconda Fort"},{i:"🕌",n:"Charminar"},{i:"🛕",n:"Ramappa Temple"},{i:"🌊",n:"Nagarjunasagar"}],pills:[{c:"mp-gold",t:"Nizami Culture"},{c:"mp-cyan",t:"Bidri Craft"},{c:"mp-purple",t:"Kuchipudi Dance"},{c:"mp-green",t:"Pochampally Silk"}],about:"Golconda was the world's only known source of diamonds until the 18th century — the Kohinoor and Hope Diamond both came from here. Ramappa Temple, built in 1213 CE, is a UNESCO site.",unesco:["Ramappa Temple"],color:"gold"},
  {name:"Sikkim",lat:27.5,lng:88.5,emoji:"🌸",capital:"Gangtok · Northeast India",badge:"Himalayan Kingdom",tagline:'"Buddhist monasteries, Kanchenjunga & mountain kingdoms"',facts:[{v:"1",l:"UNESCO Site"},{v:"8586m",l:"Kanchenjunga"},{v:"200+",l:"Monasteries"}],sites:[{i:"⛰️",n:"Kanchenjunga National Park"},{i:"🛕",n:"Rumtek Monastery"},{i:"🌸",n:"Yumthang Valley"},{i:"🏯",n:"Pemayangtse Monastery"}],pills:[{c:"mp-gold",t:"Buddhist Heritage"},{c:"mp-cyan",t:"Tibetan Culture"},{c:"mp-purple",t:"Thangka Painting"},{c:"mp-green",t:"Mountain Ecology"}],about:"Sikkim was an independent Buddhist kingdom until 1975. Its monasteries preserve some of the finest Tibetan Buddhist art outside Tibet, including the seat of the Kagyu lineage at Rumtek.",unesco:["Khangchendzonga National Park"],color:"purple"},
  {name:"Jammu and Kashmir",lat:33.7,lng:75.0,emoji:"🌹",capital:"Srinagar · North India",badge:"Paradise on Earth",tagline:'"Dal Lake, Mughal gardens & the Kashmir Valley of Saffron"',facts:[{v:"12th C",l:"Kashmir Shaivism"},{v:"1586",l:"Mughal Gardens"},{v:"Ancient",l:"Buddhist Heritage"}],sites:[{i:"🌹",n:"Dal Lake & Houseboats"},{i:"🌿",n:"Shalimar Bagh — Mughal Garden"},{i:"🛕",n:"Vaishno Devi Temple"},{i:"☸️",n:"Hemis Monastery"}],pills:[{c:"mp-gold",t:"Kashmiri Craft"},{c:"mp-cyan",t:"Pashmina Shawls"},{c:"mp-purple",t:"Sufi Heritage"},{c:"mp-green",t:"Mughal Gardens"}],about:"Kashmir's Pashmina shawls are hand-woven from the undercoat of the Changthangi goat. The Mughal emperors called Kashmir 'If there is paradise on earth, it is this'.",unesco:[],color:"gold"},
  {name:"Ladakh",lat:34.2,lng:77.5,emoji:"⛰️",capital:"Leh · High-Altitude UT",badge:"Roof of the World",tagline:'"Ancient Buddhist kingdoms, Pangong lake & sky highways"',facts:[{v:"3500m",l:"Avg Altitude"},{v:"200+",l:"Monasteries"},{v:"Ancient",l:"Silk Road Route"}],sites:[{i:"⛰️",n:"Thiksey Monastery"},{i:"🌊",n:"Pangong Tso Lake"},{i:"🏯",n:"Leh Palace"},{i:"🛕",n:"Lamayuru Monastery"}],pills:[{c:"mp-gold",t:"Buddhist Heritage"},{c:"mp-cyan",t:"Silk Road History"},{c:"mp-purple",t:"Thangka Art"},{c:"mp-green",t:"High-Altitude Culture"}],about:"Ladakh was an independent Buddhist kingdom and key node on the ancient Silk Road for 1,000 years. Its monasteries preserve some of the finest examples of Tibetan Buddhist art.",unesco:[],color:"gold"},
  {name:"Chhattisgarh",lat:21.2,lng:81.6,emoji:"🌿",capital:"Raipur · Central India",badge:"Land of Tribal Art",tagline:'"36 forts, ancient temples & living tribal traditions"',facts:[{v:"44%",l:"Forest Cover"},{v:"7th C",l:"Temples"},{v:"42",l:"Tribes"}],sites:[{i:"🛕",n:"Sirpur Temples"},{i:"🌊",n:"Chitrakote Waterfalls"},{i:"⛰️",n:"Bastar Dussehra Site"},{i:"🏯",n:"Ratanpur Fort"}],pills:[{c:"mp-gold",t:"Tribal Art"},{c:"mp-cyan",t:"Bastar Culture"},{c:"mp-purple",t:"Ancient Temples"},{c:"mp-green",t:"Dhokra Craft"}],about:"One of India's newest states, rich with ancient Shaiva temples at Sirpur, tribal art forms, and the Bastar Dussehra — the world's longest festival spanning 75 days.",unesco:[],color:"gold"},
  {name:"Jharkhand",lat:23.6,lng:85.2,emoji:"🌿",capital:"Ranchi · East India",badge:"Tribal Homeland",tagline:'"Forests, waterfalls & the living culture of Jharkhand tribes"',facts:[{v:"32",l:"Tribes"},{v:"29%",l:"Forest Cover"},{v:"Ancient",l:"Rock Art"}],sites:[{i:"🌊",n:"Hundru & Dassam Falls"},{i:"🛕",n:"Baidyanath Temple, Deoghar"},{i:"⛰️",n:"Parasnath Hill (Jain)"},{i:"🎨",n:"Hazaribagh Rock Paintings"}],pills:[{c:"mp-gold",t:"Santhali Culture"},{c:"mp-cyan",t:"Chhau Dance"},{c:"mp-purple",t:"Jain Heritage"},{c:"mp-green",t:"Tribal Crafts"}],about:"Jharkhand's forests are home to the Santhal, Munda, and Ho tribes. Chhau dance has UNESCO Intangible Cultural Heritage status. Parasnath is the most sacred pilgrimage site for Jains.",unesco:[],color:"gold"},
  {name:"Haryana",lat:29.0,lng:76.0,emoji:"⚔️",capital:"Chandigarh · North India",badge:"Land of Mahabharata",tagline:'"Where the epic war of Kurukshetra was fought"',facts:[{v:"3000 BCE",l:"Kurukshetra"},{v:"Vedic",l:"Origins"},{v:"Ancient",l:"Battlefields"}],sites:[{i:"⚔️",n:"Kurukshetra Battlefield"},{i:"🛕",n:"Brahma Sarovar"},{i:"🏛️",n:"Rakhigarhi — Harappan Site"},{i:"⚱️",n:"Panipat Battlefields"}],pills:[{c:"mp-gold",t:"Vedic Heritage"},{c:"mp-cyan",t:"Phulkari Embroidery"},{c:"mp-purple",t:"Ancient Battlefields"},{c:"mp-green",t:"Folk Traditions"}],about:"Haryana gave the world the Bhagavad Gita, spoken on the plains of Kurukshetra. Rakhigarhi is one of the largest Harappan civilisation sites ever found — larger than Mohenjo-daro.",unesco:[],color:"gold"},
  {name:"Arunachal Pradesh",lat:28.2,lng:94.7,emoji:"🏔️",capital:"Itanagar · Northeast India",badge:"Frontier State",tagline:'"Land of the Dawn-lit Mountains"',facts:[{v:"25",l:"Tribes"},{v:"800+",l:"Species"},{v:"Ancient",l:"Monasteries"}],sites:[{i:"🏯",n:"Tawang Monastery"},{i:"⛰️",n:"Sela Pass"},{i:"🌿",n:"Namdapha National Park"},{i:"🛕",n:"Bhismaknagar Fort"}],pills:[{c:"mp-gold",t:"Buddhist"},{c:"mp-cyan",t:"Tribal Culture"},{c:"mp-purple",t:"Himalayan Art"},{c:"mp-green",t:"Nature Heritage"}],about:"Northernmost state bordering China, Bhutan & Myanmar. Tawang Monastery is the world's second largest Buddhist monastery. Home to 26 major tribes with rich oral traditions and thangka paintings.",unesco:[],color:"gold"},

  /* ── NEW STATES ADDED ── */
  {name:"Telangana",lat:17.4,lng:78.5,emoji:"🏯",capital:"Hyderabad · South India",badge:"Nizam's Kingdom",tagline:'"Golconda forts, biryani & diamond mines of the Deccan"',facts:[{v:"1",l:"UNESCO Site"},{v:"16th C",l:"Golconda Era"},{v:"Kohinoor",l:"Birthplace"}],sites:[{i:"🏯",n:"Golconda Fort"},{i:"🕌",n:"Charminar"},{i:"🛕",n:"Ramappa Temple"},{i:"🌊",n:"Nagarjunasagar"}],pills:[{c:"mp-gold",t:"Nizami Culture"},{c:"mp-cyan",t:"Bidri Craft"},{c:"mp-purple",t:"Kuchipudi Dance"},{c:"mp-green",t:"Pochampally Silk"}],about:"Golconda was the world's only known source of diamonds until the 18th century — the Kohinoor and Hope Diamond both came from here. Ramappa Temple, built in 1213 CE, is a UNESCO site.",unesco:["Ramappa Temple"],color:"gold"},

  {name:"Meghalaya",lat:25.4,lng:91.3,emoji:"🌧️",capital:"Shillong · Northeast India",badge:"Abode of Clouds",tagline:'"Living root bridges, wettest place on Earth & matrilineal tribes"',facts:[{v:"3",l:"Matrilineal Tribes"},{v:"11000mm",l:"Annual Rainfall"},{v:"Ancient",l:"Megaliths"}],sites:[{i:"🌉",n:"Living Root Bridges, Cherrapunji"},{i:"🌊",n:"Nohkalikai Falls"},{i:"🏔️",n:"Dawki Crystal River"},{i:"🗿",n:"Mawphlang Sacred Forest"}],pills:[{c:"mp-gold",t:"Khasi Culture"},{c:"mp-cyan",t:"Nongkrem Dance"},{c:"mp-purple",t:"Bamboo Craft"},{c:"mp-green",t:"Megaliths"}],about:"Meghalaya is home to the world's only living root bridges, grown by the Khasi people over 500 years using rubber tree roots. Cherrapunji was once the wettest place on Earth. Unique matrilineal societies pass property and lineage through women.",unesco:[],color:"gold"},

  {name:"Manipur",lat:24.6,lng:93.9,emoji:"🎭",capital:"Imphal · Northeast India",badge:"Jewel of the East",tagline:'"Polo birthplace, Manipuri dance & Loktak lake"',facts:[{v:"Ancient",l:"Polo Origin"},{v:"Manipuri",l:"Classical Dance"},{v:"Loktak",l:"Floating Lake"}],sites:[{i:"⛩️",n:"Kangla Fort, Imphal"},{i:"🌊",n:"Loktak Lake & Phumdis"},{i:"🛕",n:"Shree Govindajee Temple"},{i:"🎭",n:"Manipuri Ras Leela Stages"}],pills:[{c:"mp-gold",t:"Manipuri Dance"},{c:"mp-cyan",t:"Polo Heritage"},{c:"mp-purple",t:"Meitei Culture"},{c:"mp-green",t:"Handloom Weaving"}],about:"Modern polo was born in Manipur — the ancient game of Sagol Kangjei was played here 1,500 years ago. Manipuri classical dance, known for its lyrical grace and Ras Leela performances, is one of India's eight classical dance forms. Loktak Lake is Asia's largest freshwater lake with unique floating biomass islands called phumdis.",unesco:[],color:"purple"},

  {name:"Nagaland",lat:26.1,lng:94.5,emoji:"🦅",capital:"Kohima · Northeast India",badge:"Land of Warriors",tagline:'"16 tribes, Hornbill Festival & warrior heritage of the Nagas"',facts:[{v:"16",l:"Tribes"},{v:"1879",l:"Kohima Battle"},{v:"Hornbill",l:"Festival"}],sites:[{i:"⚔️",n:"Kohima War Cemetery"},{i:"🎪",n:"Hornbill Festival Ground"},{i:"🌿",n:"Dzükou Valley"},{i:"🏯",n:"Khonoma Green Village"}],pills:[{c:"mp-gold",t:"Naga Warrior Heritage"},{c:"mp-cyan",t:"Hornbill Festival"},{c:"mp-purple",t:"Tribal Textiles"},{c:"mp-green",t:"Eco-Tourism"}],about:"The Hornbill Festival, held every December, showcases all 16 Naga tribes together — a living museum of indigenous culture. Khonoma is India's first green village. The Battle of Kohima in WWII was called the 'Stalingrad of the East'. Naga shawls are distinct to each tribe and carry encoded cultural identity.",unesco:[],color:"gold"},

  {name:"Mizoram",lat:23.1,lng:92.9,emoji:"🌿",capital:"Aizawl · Northeast India",badge:"Land of Blue Mountains",tagline:'"Bamboo civilization, Cheraw dance & pristine forest highlands"',facts:[{v:"91%",l:"Forest Cover"},{v:"Mizo",l:"People"},{v:"Cheraw",l:"UNESCO Dance"}],sites:[{i:"🌿",n:"Phawngpui Blue Mountain"},{i:"🏔️",n:"Reiek Heritage Village"},{i:"🌊",n:"Tam Dil Lake"},{i:"🎭",n:"Cheraw Dance Festival"}],pills:[{c:"mp-gold",t:"Mizo Heritage"},{c:"mp-cyan",t:"Cheraw Dance"},{c:"mp-purple",t:"Bamboo Craft"},{c:"mp-green",t:"Hmars Tribe Culture"}],about:"Mizoram has one of India's highest literacy rates. The Cheraw, or bamboo dance, is internationally recognised for its intricate choreography between bamboo poles. Phawngpui — the Blue Mountain — is the highest peak in Mizoram, shrouded in legend. The state has 91% forest cover, making it among India's greenest.",unesco:[],color:"gold"},

  {name:"Tripura",lat:23.7,lng:91.3,emoji:"🏯",capital:"Agartala · Northeast India",badge:"Royal Bengal Kingdom",tagline:'"Manikya dynasty, bamboo heritage & Bengali-tribal confluence"',facts:[{v:"14th C",l:"Manikya Dynasty"},{v:"19",l:"Tribes"},{v:"Buddhist",l:"Monastic Heritage"}],sites:[{i:"🏯",n:"Ujjayanta Palace, Agartala"},{i:"🛕",n:"Tripura Sundari Temple"},{i:"🗿",n:"Unakoti Rock Carvings"},{i:"🌿",n:"Sepahijala Wildlife Sanctuary"}],pills:[{c:"mp-gold",t:"Manikya Heritage"},{c:"mp-cyan",t:"Bamboo Craft"},{c:"mp-purple",t:"Rignai Weaving"},{c:"mp-green",t:"Buddhist Culture"}],about:"Unakoti — meaning 'one less than a crore' — holds millions of ancient rock-cut and sculptured images of Hindu gods, dating back to the 7th–9th century CE. The Tripura Sundari temple is one of India's 51 Shakti Peethas. Ujjayanta Palace, built by the Manikya kings in 1901, blends Mughal and Bengali architecture.",unesco:[],color:"purple"}
];

/* ════════════════════════════════════════════
   INIT MAP
════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function(){

  var map = L.map('heritageMap', {
    center: [22.5, 82.0],
    zoom: 5,
    zoomControl: true,
    minZoom: 4,
    maxZoom: 10,
    scrollWheelZoom: false
  });

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_only_labels/{z}/{x}/{y}{r}.png', {
    subdomains: 'abcd', maxZoom: 19, pane: 'shadowPane'
  }).addTo(map);

  var activeMarker = null;

  function makeIcon(state, selected) {
    var colorMap = {
      gold:   selected ? '#00e0ff' : '#f5c842',
      purple: selected ? '#00e0ff' : '#a78bfa'
    };
    var col = colorMap[state.color] || '#f5c842';
    var size = selected ? 20 : 14;
    var glow = selected ? '0 0 18px ' + col : '0 0 8px ' + col;
    return L.divIcon({
      className: '',
      html: '<div style="width:' + size + 'px;height:' + size + 'px;border-radius:50%;background:' + col + ';border:2px solid rgba(255,255,255,0.6);box-shadow:' + glow + ';transition:all .3s;cursor:pointer;"></div>',
      iconSize: [size, size],
      iconAnchor: [size/2, size/2]
    });
  }

  function showPanel(state) {
    var def  = document.getElementById('mipDefault');
    var cont = document.getElementById('mipContent');
    def.style.display  = 'none';
    cont.style.display = 'flex';

    document.getElementById('mipEmoji').textContent   = state.emoji;
    document.getElementById('mipName').textContent    = state.name;
    document.getElementById('mipCapital').textContent = state.capital;
    document.getElementById('mipBadge').textContent   = state.badge;
    document.getElementById('mipTagline').textContent = state.tagline;

    document.getElementById('mipFactsRow').innerHTML = state.facts.map(function(f){
      return '<div class="mip-fact"><div class="mif-v">'+f.v+'</div><div class="mif-l">'+f.l+'</div></div>';
    }).join('');

    document.getElementById('mipSites').innerHTML = state.sites.map(function(s){
      return '<div class="mip-site-item"><span>'+s.i+'</span>'+s.n+'</div>';
    }).join('');

    document.getElementById('mipPills').innerHTML = state.pills.map(function(p){
      return '<span class="mip-pill '+p.c+'">'+p.t+'</span>';
    }).join('');

    document.getElementById('mipAbout').textContent = state.about;

    var uBlock = document.getElementById('mipUNESCOBlock');
    var uList  = document.getElementById('mipUNESCO');
    if (state.unesco && state.unesco.length) {
      uBlock.style.display = 'block';
      uList.innerHTML = state.unesco.map(function(u){
        return '<div class="mip-unesco-item"><span>🏆</span>'+u+'</div>';
      }).join('');
    } else {
      uBlock.style.display = 'none';
    }

    cont.style.opacity   = '0';
    cont.style.transform = 'translateY(10px)';
    requestAnimationFrame(function(){
      cont.style.transition = 'opacity .35s, transform .35s';
      cont.style.opacity    = '1';
      cont.style.transform  = 'translateY(0)';
    });
  }

  /* Deduplicate states by name before placing markers */
  var seenNames = {};
  var uniqueStates = STATES.filter(function(s){
    if(seenNames[s.name]) return false;
    seenNames[s.name] = true;
    return true;
  });

  uniqueStates.forEach(function(state){
    var marker = L.marker([state.lat, state.lng], {
      icon: makeIcon(state, false)
    }).addTo(map);

    marker.bindTooltip('<b style="font-family:\'Syne\',sans-serif;font-size:12px;">' + state.emoji + ' ' + state.name + '</b>', {
      className: '',
      direction: 'top',
      offset: [0, -10]
    });

    marker.on('click', function(){
      if (activeMarker && activeMarker._state) {
        activeMarker.setIcon(makeIcon(activeMarker._state, false));
      }
      marker.setIcon(makeIcon(state, true));
      activeMarker = marker;
      activeMarker._state = state;
      showPanel(state);
      map.setView([state.lat, state.lng], 6, {animate: true, duration: 0.8});
    });

    marker._state = state;
  });

  map.on('focus', function(){ map.scrollWheelZoom.enable(); });
  map.on('blur',  function(){ map.scrollWheelZoom.disable(); });
  document.getElementById('heritageMap').addEventListener('click', function(){
    map.scrollWheelZoom.enable();
  });

});

/* ════════════════════════════════════════════
   SITE PANEL DATA
════════════════════════════════════════════ */
const indiaSites = [
  {img:'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=900&q=90',name:'Taj Mahal',loc:'📍 Agra, Uttar Pradesh — India',era:'1632–1653 CE',desc:'Iconic white marble mausoleum built by Mughal emperor Shah Jahan for Mumtaz Mahal. One of the Seven Wonders of the World and India\'s most celebrated UNESCO Heritage Site.',facts:[{v:'73m',l:'Height'},{v:'17ha',l:'Complex'},{v:'UNESCO',l:'Status'}],styles:[{cls:'sp-gold',t:'Mughal'},{cls:'sp-cyan',t:'Persian'},{cls:'sp-purple',t:'Islamic'}]},
  {img:'https://www.stayvista.com/blog/wp-content/uploads/2024/09/Hampi_karnataka.jpg',name:'Hampi Ruins',loc:'📍 Bellary District, Karnataka — India',era:'14th–16th Century CE',desc:'Capital of the Vijayanagara Empire — once among the world\'s richest cities. Over 1,600 surviving temples, royal enclosures, and market streets across a dramatic boulder landscape.',facts:[{v:'1600+',l:'Monuments'},{v:'4187ha',l:'Site Area'},{v:'UNESCO',l:'Status'}],styles:[{cls:'sp-gold',t:'Dravidian'},{cls:'sp-green',t:'Vijayanagara'},{cls:'sp-cyan',t:'Indo-Saracenic'}]},
  {img:'https://miro.medium.com/1*fYA-b-KA9UUqPL2OsDYkQw.png',name:'Hawa Mahal',loc:'📍 Jaipur, Rajasthan — India',era:'1799 CE',desc:'Palace of Winds — a five-story pink sandstone façade with 953 small jharokha windows. Built by Maharaja Sawai Pratap Singh to allow royal women to observe street festivities in purdah.',facts:[{v:'953',l:'Windows'},{v:'5',l:'Floors'},{v:'Pink City',l:'Region'}],styles:[{cls:'sp-gold',t:'Rajput'},{cls:'sp-purple',t:'Mughal'},{cls:'sp-cyan',t:'Indo-Islamic'}]},
  {img:'https://maharashtratourism.gov.in/wp-content/uploads/2024/11/ELLORA-CAVES-2.jpg',name:'Ajanta Caves',loc:'📍 Sambhaji Nagar, Maharashtra — India',era:'2nd Century BCE – 6th CE',desc:'Thirty rock-cut Buddhist cave monuments featuring the finest examples of ancient Indian art and sculpture, depicting Jataka tales and the life of Buddha across eight centuries.',facts:[{v:'30',l:'Caves'},{v:'2nd BCE',l:'Oldest'},{v:'UNESCO',l:'Status'}],styles:[{cls:'sp-purple',t:'Buddhist'},{cls:'sp-gold',t:'Rock-Cut'},{cls:'sp-green',t:'Ancient'}]},
  {img:'https://www.dailyartmagazine.com/wp-content/uploads/2024/06/Cover-Photo-768x464.jpg',name:'Konark Sun Temple',loc:'📍 Konark, Odisha — India',era:'13th Century CE',desc:'Magnificent 13th-century temple dedicated to Sun God Surya, designed as a colossal stone chariot with 12 pairs of elaborately carved wheels drawn by seven horses.',facts:[{v:'38m',l:'Height'},{v:'1250 CE',l:'Built'},{v:'UNESCO',l:'Status'}],styles:[{cls:'sp-gold',t:'Kalinga'},{cls:'sp-cyan',t:'Hindu'},{cls:'sp-green',t:'Odishan'}]},
  {img:'https://akm-img-a-in.tosshub.com/sites/media2/indiatoday/images/stories/2017April/qutub1_042717100950.jpg',name:'Qutub Minar',loc:'📍 Mehrauli, New Delhi — India',era:'1193–1368 CE',desc:'Tallest brick minaret in the world at 72.5m, begun by Qutb ud-Din Aibak. The Qutb complex includes ancient Hindu and Jain temple ruins alongside Islamic architecture.',facts:[{v:'72.5m',l:'Height'},{v:'379',l:'Steps'},{v:'UNESCO',l:'Status'}],styles:[{cls:'sp-gold',t:'Indo-Islamic'},{cls:'sp-purple',t:'Afghan'},{cls:'sp-cyan',t:'Delhi Sultanate'}]}
];

/* preload slide images */
(function(){
  const slides=document.querySelectorAll('.c-slide');
  indiaSites.forEach((s,i)=>{
    if(!slides[i])return;
    const img=new Image();
    img.onload=()=>{slides[i].style.backgroundImage=`url('${s.img}')`;};
    img.onerror=()=>{slides[i].style.background='linear-gradient(135deg,rgba(245,200,66,.1),rgba(0,224,255,.07))';};
    img.src=s.img;
  });
})();

/* sidebar */
const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('sbOverlay'),hamburger=document.getElementById('hamburger');
function toggleSidebar(){sidebar.classList.contains('open')?closeSidebar():openSidebar();}
function openSidebar(){sidebar.classList.add('open');overlay.classList.add('show');hamburger.classList.add('active');document.body.style.overflow='hidden';}
function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('show');hamburger.classList.remove('active');document.body.style.overflow='';}
function toggleSub(s,t){const sub=document.getElementById(s),tog=document.getElementById(t),open=sub.classList.contains('open');document.querySelectorAll('.sb-sub').forEach(x=>x.classList.remove('open'));document.querySelectorAll('.sb-item.has-sub').forEach(x=>x.classList.remove('expanded'));if(!open){sub.classList.add('open');tog.classList.add('expanded');}}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeSidebar();});

/* nav scroll */
window.addEventListener('scroll',()=>{document.getElementById('mainNav').classList.toggle('scrolled',window.scrollY>60);});

/* bg slideshow */
const bgSlides=document.querySelectorAll('.hero-bg .slide');let bgCur=0;
setInterval(()=>{bgSlides[bgCur].classList.remove('active');bgCur=(bgCur+1)%bgSlides.length;bgSlides[bgCur].classList.add('active');},7000);

/* carousel */
let currentSlide=0,carouselTimer;
(function(){
  const slides=document.querySelectorAll('.c-slide');
  const dots=document.querySelectorAll('.cdot');
  const numEl=document.getElementById('carouselSiteNum');
  function updatePanel(n){
    const s=indiaSites[n];
    document.getElementById('sipName').textContent=s.name;
    document.getElementById('sipLoc').textContent=s.loc;
    document.getElementById('sipEra').textContent=s.era;
    document.getElementById('sipDesc').textContent=s.desc;
    document.getElementById('sipFacts').innerHTML=s.facts.map(f=>`<div class="sip-fact"><div class="sif-v">${f.v}</div><div class="sif-l">${f.l}</div></div>`).join('');
    document.getElementById('sipStyles').innerHTML=s.styles.map(p=>`<span class="style-pill ${p.cls}">${p.t}</span>`).join('');
    const panel=document.getElementById('siteInfoPanel');
    panel.style.opacity='0';panel.style.transform='translateY(8px)';
    requestAnimationFrame(()=>{panel.style.transition='opacity .4s,transform .4s';panel.style.opacity='1';panel.style.transform='translateY(0)';});
  }
  window.goSlide=function(n){
    slides[currentSlide].classList.remove('active');dots[currentSlide].classList.remove('active');
    currentSlide=((n%slides.length)+slides.length)%slides.length;
    slides[currentSlide].classList.add('active');dots[currentSlide].classList.add('active');
    if(numEl)numEl.textContent=currentSlide+1;
    updatePanel(currentSlide);
    clearInterval(carouselTimer);carouselTimer=setInterval(()=>goSlide(currentSlide+1),5500);
  };
  const car=document.querySelector('.heritage-carousel');
  let tx=0;
  car.addEventListener('touchstart',e=>{tx=e.touches[0].clientX;},{passive:true});
  car.addEventListener('touchend',e=>{const dx=e.changedTouches[0].clientX-tx;if(Math.abs(dx)>40)goSlide(currentSlide+(dx<0?1:-1));});
  carouselTimer=setInterval(()=>goSlide(currentSlide+1),5500);
})();

/* particles */
(function(){
  const cv=document.getElementById('particles-canvas'),ctx=cv.getContext('2d');
  let W,H,pts=[];
  function rs(){W=cv.width=window.innerWidth;H=cv.height=window.innerHeight;}
  rs();window.addEventListener('resize',rs);
  const C=['rgba(245,200,66,','rgba(0,224,255,','rgba(167,139,250,','rgba(0,255,166,'];
  function P(){this.r=function(){this.x=Math.random()*W;this.y=Math.random()*H;this.rad=Math.random()*1.8+.3;this.vx=(Math.random()-.5)*.35;this.vy=-Math.random()*.55-.15;this.a=Math.random()*.45+.08;this.c=C[Math.floor(Math.random()*C.length)];this.l=0;this.ml=Math.random()*200+100;};this.r();this.y=Math.random()*H;}
  for(let i=0;i<70;i++)pts.push(new P());
  function draw(){ctx.clearRect(0,0,W,H);pts.forEach(p=>{p.x+=p.vx;p.y+=p.vy;p.l++;const f=p.l<30?p.l/30:p.l>p.ml-30?(p.ml-p.l)/30:1;ctx.beginPath();ctx.arc(p.x,p.y,p.rad,0,Math.PI*2);ctx.fillStyle=p.c+(p.a*f)+')';ctx.fill();if(p.l>=p.ml||p.y<-10)p.r();});requestAnimationFrame(draw);}
  draw();
})();

/* circuit */
(function(){
  const cv=document.getElementById('circuit-canvas');if(!cv)return;
  const cx=cv.getContext('2d');let W,H;
  function rs(){W=cv.width=window.innerWidth;H=cv.height=window.innerHeight;}
  rs();window.addEventListener('resize',()=>{rs();bn();});
  const G=80;let nodes=[];
  function bn(){nodes=[];for(let x=G;x<W;x+=G)for(let y=G;y<H;y+=G)if(Math.random()>.65)nodes.push({x,y,ph:Math.random()*Math.PI*2,sp:.008+Math.random()*.014});}
  bn();
  function draw(){
    cx.clearRect(0,0,W,H);
    cx.strokeStyle='rgba(0,224,255,0.018)';cx.lineWidth=.4;
    for(let x=G;x<W;x+=G){cx.beginPath();cx.moveTo(x,0);cx.lineTo(x,H);cx.stroke();}
    for(let y=G;y<H;y+=G){cx.beginPath();cx.moveTo(0,y);cx.lineTo(W,y);cx.stroke();}
    nodes.forEach(n=>{
      n.ph+=n.sp;const a=(Math.sin(n.ph)*.5+.5)*.26;
      cx.beginPath();cx.arc(n.x,n.y,1.5,0,Math.PI*2);cx.fillStyle=`rgba(245,200,66,${a})`;cx.fill();
      if(Math.sin(n.ph*.4)>.68&&n.x+G<W){cx.beginPath();cx.moveTo(n.x,n.y);cx.lineTo(n.x+G,n.y);cx.strokeStyle=`rgba(0,224,255,${a*.4})`;cx.lineWidth=.4;cx.stroke();}
      if(Math.cos(n.ph*.3)>.74&&n.y+G<H){cx.beginPath();cx.moveTo(n.x,n.y);cx.lineTo(n.x,n.y+G);cx.strokeStyle=`rgba(167,139,250,${a*.32})`;cx.lineWidth=.4;cx.stroke();}
    });
    requestAnimationFrame(draw);
  }
  draw();
})();

/* reveal */
const obs=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting)e.target.classList.add('visible');});},{threshold:.1});
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));

/* counter */
function animCount(el,t){let s=0;const d=1800;const f=ts=>{if(!s)s=ts;const p=Math.min((ts-s)/d,1);el.textContent=Math.floor((1-Math.pow(1-p,3))*t)+'+';if(p<1)requestAnimationFrame(f);};requestAnimationFrame(f);}
const co=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){const t=parseInt(e.target.dataset.count);if(!isNaN(t)&&t>0)animCount(e.target,t);co.unobserve(e.target);}});},{threshold:.5});
document.querySelectorAll('.stat-num[data-count]').forEach(el=>co.observe(el));
</script>
</body>
</html>
