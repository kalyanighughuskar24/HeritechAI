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
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════
   ROOT TOKENS
══════════════════════════════════ */
:root{
  --cyan:#00e0ff; --green:#00ffa6; --purple:#a78bfa; --pink:#f472b6;
  --gold:#f5c842; --amber:#ff9d00;
  --dark:#060d1f; --darker:#030810;
  --card-bg:rgba(255,255,255,0.05);
  --sw:272px;
  --font-head:'Syne',sans-serif;
  --font-body:'DM Sans',sans-serif;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{background:var(--dark);color:white;overflow-x:hidden;font-family:var(--font-body);}

/* ══════════════════════════════════
   HERO BACKGROUND — Heritage Imagery
   (Uses Unsplash public CDN images of
    heritage architecture + AI overlay)
══════════════════════════════════ */
.hero-bg{
  position:fixed;inset:0;z-index:0;
  background:var(--darker);
  overflow:hidden;
}

/* Slideshow of heritage place images */
.hero-bg .slide{
  position:absolute;inset:0;
  background-size:cover;background-position:center;
  opacity:0;transition:opacity 2s ease;
  transform:scale(1.08);
  animation:heroScale 18s ease-in-out infinite;
}
.hero-bg .slide.active{opacity:1;}
.hero-bg .slide:nth-child(1){
  background-image:url('https://images.unsplash.com/photo-1548013146-72479768bada?w=1920&q=80');
  animation-delay:0s;
}
.hero-bg .slide:nth-child(2){
  background-image:url('https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1920&q=80');
  animation-delay:6s;
}
.hero-bg .slide:nth-child(3){
  background-image:url('https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?w=1920&q=80');
  animation-delay:12s;
}


@keyframes heroScale{
  0%{transform:scale(1.08);}
  50%{transform:scale(1.0);}
  100%{transform:scale(1.08);}
}

/* Deep overlay so text is legible */
.hero-bg-overlay{
  position:absolute;inset:0;
  background:
    linear-gradient(180deg, rgba(3,8,16,0.55) 0%, rgba(6,13,31,0.4) 40%, rgba(6,13,31,0.75) 75%, var(--dark) 100%),
    linear-gradient(90deg, rgba(0,224,255,0.08) 0%, transparent 60%);
  z-index:1;
}

/* Digital scan-line grain */
.hero-bg-noise{
  position:absolute;inset:0;z-index:2;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
  opacity:.5;pointer-events:none;
}

/* Animated scan line */
.scan-line{
  position:absolute;inset:0;z-index:3;pointer-events:none;
  background:linear-gradient(to bottom, transparent 49.5%, rgba(0,224,255,0.04) 50%, transparent 50.5%);
  background-size:100% 4px;
  animation:scanMove 8s linear infinite;
  opacity:.5;
}
@keyframes scanMove{0%{background-position:0 0;}100%{background-position:0 400px;}}

/* Floating orbs (kept but behind BG) */
.bg-orbs{position:fixed;inset:0;z-index:4;pointer-events:none;overflow:hidden;}
.orb{position:absolute;border-radius:50%;filter:blur(90px);animation:orbFloat 14s infinite ease-in-out;}
.orb1{width:600px;height:600px;background:rgba(0,224,255,0.12);top:-150px;left:-150px;}
.orb2{width:500px;height:500px;background:rgba(167,139,250,0.1);top:35%;right:-150px;animation-delay:4s;}
.orb3{width:450px;height:450px;background:rgba(245,200,66,0.07);bottom:-100px;left:25%;animation-delay:8s;}
@keyframes orbFloat{0%,100%{transform:translate(0,0);}33%{transform:translate(40px,-40px);}66%{transform:translate(-30px,30px);}}

/* ══════════════════════════════════
   ANIMATED PARTICLES
══════════════════════════════════ */
#particles-canvas{
  position:fixed;inset:0;z-index:5;pointer-events:none;
}

/* ══════════════════════════════════
   SIDEBAR
══════════════════════════════════ */
.sidebar{
  position:fixed;top:0;left:0;width:var(--sw);height:100vh;z-index:1100;
  background:rgba(4,9,20,0.97);backdrop-filter:blur(32px);
  border-right:1px solid rgba(255,255,255,0.06);
  display:flex;flex-direction:column;
  transform:translateX(calc(-1 * var(--sw)));
  transition:transform .4s cubic-bezier(.4,0,.2,1);
}
.sidebar.open{transform:translateX(0);box-shadow:12px 0 80px rgba(0,0,0,0.8);}

.sb-logo{
  display:flex;align-items:center;gap:12px;
  padding:22px 16px 18px;
  border-bottom:1px solid rgba(255,255,255,0.06);flex-shrink:0;
}
.sb-logo-ico{
  width:46px;height:46px;border-radius:14px;flex-shrink:0;
  background:linear-gradient(135deg,#f472b6,#a78bfa);
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 6px 20px rgba(244,114,182,0.35);
}
.sb-logo-ico svg{width:22px;height:22px;}
.sb-logo-name{font-size:15px;font-weight:700;font-family:var(--font-head);}
.sb-logo-sub{font-size:10px;color:rgba(255,255,255,0.3);margin-top:2px;letter-spacing:1px;text-transform:uppercase;}

.sb-nav{
  flex:1;overflow-y:auto;padding:10px 8px;
  scrollbar-width:thin;scrollbar-color:rgba(255,255,255,0.06) transparent;
}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.07);border-radius:2px;}

.sb-item{
  display:flex;align-items:center;gap:10px;
  padding:9px 11px;border-radius:12px;
  color:rgba(255,255,255,0.5);font-size:13.5px;font-weight:500;
  text-decoration:none;cursor:pointer;margin-bottom:2px;
  border:1px solid transparent;background:none;width:100%;text-align:left;
  transition:all .2s;font-family:var(--font-body);
}
.sb-item:hover{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.9);}
.sb-item.active{
  background:linear-gradient(90deg,rgba(0,224,255,0.13),rgba(167,139,250,0.08));
  color:#fff;border-color:rgba(0,224,255,0.18);
}
.sb-ico{
  width:34px;height:34px;border-radius:9px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,0.05);transition:.2s;
}
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
.sb-cta{
  display:flex;align-items:center;justify-content:center;gap:8px;
  padding:13px 14px;border-radius:13px;
  background:linear-gradient(90deg,#a78bfa,#f472b6);
  color:#fff;font-size:13px;font-weight:700;
  text-decoration:none;transition:.25s;
  box-shadow:0 4px 24px rgba(167,139,250,0.32);
  font-family:var(--font-head);
}
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
  padding:16px 80px;
  background:rgba(4,9,20,0.75);backdrop-filter:blur(24px);
  border-bottom:1px solid rgba(255,255,255,0.05);
  transition:padding .4s,background .4s;
}
nav.scrolled{padding:10px 80px;background:rgba(4,9,20,0.92);}
.nav-left{display:flex;align-items:center;gap:14px;}
.hamburger{
  width:42px;height:42px;border-radius:11px;
  background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.09);
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;
  cursor:pointer;transition:.25s;flex-shrink:0;
}
.hamburger:hover{background:rgba(0,224,255,0.12);border-color:rgba(0,224,255,0.3);}
.hamburger span{display:block;width:18px;height:2px;background:rgba(255,255,255,0.75);border-radius:2px;transition:.3s;}
.hamburger.active span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.hamburger.active span:nth-child(2){opacity:0;transform:scaleX(0);}
.hamburger.active span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}

.logo{font-size:22px;font-weight:800;font-family:var(--font-head);letter-spacing:-0.5px;}
.logo span{
  background:linear-gradient(90deg,var(--gold),var(--amber),var(--cyan));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-size:200%;animation:gradAnim 4s linear infinite;
}
@keyframes gradAnim{0%{background-position:0%;}100%{background-position:200%;}}

.nav-links{display:flex;align-items:center;gap:8px;list-style:none;}
.nav-links a{
  text-decoration:none;color:rgba(255,255,255,0.7);font-size:14px;
  font-weight:500;padding:8px 16px;border-radius:20px;transition:.25s;
  font-family:var(--font-body);position:relative;overflow:hidden;
}
.nav-links a::before{
  content:'';position:absolute;bottom:0;left:50%;right:50%;height:1px;
  background:var(--cyan);transition:left .3s,right .3s;
}
.nav-links a:hover::before{left:16px;right:16px;}
.nav-links a:hover{color:white;}

/* Animated nav button */
.btn-nav{
  background:linear-gradient(90deg,var(--cyan),var(--green))!important;
  color:#000!important;font-weight:700!important;border-radius:20px!important;
  box-shadow:0 0 0 0 rgba(0,224,255,0.4);
  animation:navPulse 3s ease-in-out infinite;
  position:relative;overflow:hidden;
}
.btn-nav::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,0.25),transparent);
  transform:translateX(-100%);transition:transform .5s;
}
.btn-nav:hover::after{transform:translateX(100%);}
.btn-nav:hover{transform:scale(1.06)!important;box-shadow:0 0 28px rgba(0,224,255,0.5)!important;}
@keyframes navPulse{0%,100%{box-shadow:0 0 0 0 rgba(0,224,255,0.4);}50%{box-shadow:0 0 0 6px rgba(0,224,255,0);}}
.btn-admin{background:linear-gradient(90deg,#a78bfa,#f472b6)!important;color:#fff!important;font-weight:700!important;}

/* ══════════════════════════════════
   HERO SECTION
══════════════════════════════════ */
.hero{
  position:relative;z-index:10;
  min-height:100vh;display:flex;flex-direction:column;
  align-items:center;justify-content:center;text-align:center;
  padding:120px 20px 80px;
}

/* Heritage badge */
.heritage-badge{
  display:inline-flex;align-items:center;gap:10px;
  background:rgba(245,200,66,0.1);
  border:1px solid rgba(245,200,66,0.35);
  padding:8px 20px;border-radius:30px;
  font-size:12px;font-weight:600;color:var(--gold);
  margin-bottom:28px;
  animation:fadeUp .8s ease both, badgeGlow 3s ease-in-out infinite;
  letter-spacing:1px;text-transform:uppercase;font-family:var(--font-head);
}
.heritage-badge .pulse-dot{
  width:8px;height:8px;border-radius:50%;background:var(--gold);
  animation:pulse 2s infinite;flex-shrink:0;
}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(1.5);}}
@keyframes badgeGlow{0%,100%{box-shadow:0 0 0 0 rgba(245,200,66,0.2);}50%{box-shadow:0 0 20px rgba(245,200,66,0.15);}}

/* Main heading */
.hero h1{
  font-size:80px;font-weight:800;line-height:1.0;
  margin-bottom:24px;font-family:var(--font-head);
  animation:fadeUp .8s .1s ease both;
}
.hero h1 .grad-gold{
  background:linear-gradient(90deg,var(--gold),var(--amber),var(--pink));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-size:300%;animation:gradAnim 5s linear infinite;
  display:block;
}
.hero h1 .grad-cyan{
  background:linear-gradient(90deg,var(--cyan),var(--green),var(--purple));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-size:300%;animation:gradAnim 5s 1s linear infinite;
  display:block;
}

.hero p{
  font-size:19px;max-width:660px;opacity:.72;line-height:1.75;
  margin-bottom:46px;animation:fadeUp .8s .2s ease both;
  font-family:var(--font-body);font-weight:300;
}

/* ── ANIMATED BUTTONS ── */
.hero-btns{
  display:flex;gap:18px;flex-wrap:wrap;justify-content:center;
  animation:fadeUp .8s .3s ease both;
}

.btn-hero{
  padding:15px 36px;border-radius:50px;font-size:16px;
  font-weight:700;text-decoration:none;
  display:inline-flex;align-items:center;gap:10px;
  position:relative;overflow:hidden;
  font-family:var(--font-head);letter-spacing:0.3px;
  transition:transform .3s cubic-bezier(.34,1.56,.64,1), box-shadow .3s;
}

/* Primary — golden shimmer */
.btn-primary{
  background:linear-gradient(90deg,var(--gold),var(--amber),#ff6b35);
  background-size:200%;
  color:#000;
  box-shadow:0 6px 30px rgba(245,200,66,0.35);
  animation:btnGoldShift 4s ease-in-out infinite;
}
.btn-primary::before{
  content:'';position:absolute;top:0;left:-75%;width:50%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,0.4),transparent);
  transform:skewX(-20deg);
  animation:btnShimmer 3s 1s ease-in-out infinite;
}
@keyframes btnGoldShift{0%,100%{background-position:0%;}50%{background-position:100%;}}
@keyframes btnShimmer{0%,100%{left:-75%;}50%{left:125%;}}
.btn-primary:hover{transform:translateY(-4px) scale(1.04);box-shadow:0 14px 44px rgba(245,200,66,0.5);}

/* Secondary — electric outline */
.btn-electric{
  border:2px solid rgba(0,224,255,0.5);color:var(--cyan);
  background:rgba(0,224,255,0.06);
  box-shadow:0 0 0 0 rgba(0,224,255,0.3), inset 0 0 20px rgba(0,224,255,0.05);
  animation:electricPulse 3s ease-in-out infinite;
}
.btn-electric::after{
  content:'';position:absolute;inset:-2px;border-radius:50px;
  border:2px solid var(--cyan);opacity:0;
  animation:electricRing 3s ease-in-out infinite;
}
@keyframes electricPulse{0%,100%{box-shadow:0 0 0 0 rgba(0,224,255,0.3),inset 0 0 20px rgba(0,224,255,0.05);}50%{box-shadow:0 0 20px rgba(0,224,255,0.2),inset 0 0 30px rgba(0,224,255,0.08);}}
@keyframes electricRing{0%{transform:scale(1);opacity:.7;}100%{transform:scale(1.3);opacity:0;}}
.btn-electric:hover{transform:translateY(-4px) scale(1.04);background:rgba(0,224,255,0.12);border-color:var(--cyan);box-shadow:0 0 32px rgba(0,224,255,0.4);}

/* ── HERITAGE SCROLL INDICATOR ── */
.scroll-hint{
  margin-top:56px;display:flex;flex-direction:column;align-items:center;gap:8px;
  opacity:.45;font-size:11px;letter-spacing:2px;text-transform:uppercase;
  animation:fadeUp .8s .6s ease both;font-family:var(--font-head);
}
.scroll-temple{font-size:20px;margin-bottom:4px;animation:templeFloat 3s ease-in-out infinite;}
@keyframes templeFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
.scroll-line{width:1px;height:48px;background:linear-gradient(to bottom,var(--gold),transparent);animation:scrollAnim 2s infinite;}
@keyframes scrollAnim{0%{transform:scaleY(0);transform-origin:top;}50%{transform:scaleY(1);}100%{transform:scaleY(0);transform-origin:bottom;}}

@keyframes fadeUp{from{opacity:0;transform:translateY(32px);}to{opacity:1;transform:translateY(0);}}

/* ══════════════════════════════════
   STATS BAR
══════════════════════════════════ */
.stats-bar{
  position:relative;z-index:10;
  display:flex;justify-content:center;
  background:rgba(255,255,255,0.03);
  border-top:1px solid rgba(255,255,255,0.05);
  border-bottom:1px solid rgba(255,255,255,0.05);
  padding:36px 0;flex-wrap:wrap;
  backdrop-filter:blur(10px);
}
.stat-item{
  text-align:center;padding:0 60px;
  border-right:1px solid rgba(255,255,255,0.07);
  position:relative;
}
.stat-item:last-child{border-right:none;}
.stat-num{
  font-size:48px;font-weight:800;display:block;
  background:linear-gradient(90deg,var(--gold),var(--amber));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  font-family:var(--font-head);
}
.stat-lbl{font-size:12px;opacity:.45;margin-top:6px;letter-spacing:1.5px;text-transform:uppercase;font-family:var(--font-head);}

/* ══════════════════════════════════
   SECTION STYLES
══════════════════════════════════ */
.section{position:relative;z-index:10;padding:110px 80px;}
.section-label{
  font-size:11px;font-weight:700;letter-spacing:3px;
  color:var(--gold);text-transform:uppercase;margin-bottom:14px;
  font-family:var(--font-head);display:flex;align-items:center;gap:10px;
}
.section-label::before{content:'';width:28px;height:2px;background:var(--gold);border-radius:2px;}
.section-title{font-size:44px;font-weight:800;margin-bottom:18px;font-family:var(--font-head);line-height:1.1;}
.section-sub{font-size:16px;opacity:.58;max-width:580px;line-height:1.8;font-weight:300;}

/* ══════════════════════════════════
   FEATURE CARDS
══════════════════════════════════ */
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:60px;}
.feat-card{
  background:rgba(255,255,255,0.04);
  border:1px solid rgba(255,255,255,0.07);
  border-radius:22px;padding:34px;
  transition:transform .4s cubic-bezier(.34,1.56,.64,1),border-color .3s,box-shadow .3s;
  position:relative;overflow:hidden;cursor:default;
}
.feat-card::before{
  content:'';position:absolute;inset:0;border-radius:22px;opacity:0;transition:.4s;
  background:linear-gradient(135deg,rgba(245,200,66,0.07),rgba(0,224,255,0.04));
}
.feat-card::after{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,var(--gold),var(--cyan));
  transform:scaleX(0);transition:transform .4s ease;
  transform-origin:left;
}
.feat-card:hover{transform:translateY(-10px);border-color:rgba(245,200,66,0.25);box-shadow:0 24px 60px rgba(0,0,0,0.4);}
.feat-card:hover::before{opacity:1;}
.feat-card:hover::after{transform:scaleX(1);}
.feat-icon{
  font-size:36px;margin-bottom:20px;display:flex;align-items:center;
  width:66px;height:66px;border-radius:18px;
  background:linear-gradient(135deg,rgba(245,200,66,0.12),rgba(0,224,255,0.07));
  justify-content:center;
  border:1px solid rgba(245,200,66,0.15);
  transition:.3s;
}
.feat-card:hover .feat-icon{background:linear-gradient(135deg,rgba(245,200,66,0.2),rgba(0,224,255,0.12));transform:scale(1.08);}
.feat-card h3{font-size:18px;font-weight:700;margin-bottom:10px;font-family:var(--font-head);}
.feat-card p{font-size:14px;opacity:.56;line-height:1.75;font-weight:300;}

/* ══════════════════════════════════
   CONTENT GRID
══════════════════════════════════ */
.content-section{position:relative;z-index:10;padding:80px 80px 110px;}
.content-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:44px;}
.content-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:26px;}
.content-card{
  background:rgba(255,255,255,0.04);
  border:1px solid rgba(255,255,255,0.07);
  border-radius:22px;overflow:hidden;
  transition:transform .4s cubic-bezier(.34,1.56,.64,1),border-color .3s,box-shadow .3s;
}
.content-card:hover{transform:translateY(-10px);border-color:rgba(245,200,66,0.3);box-shadow:0 24px 60px rgba(245,200,66,0.1);}
.card-media{width:100%;height:188px;background:linear-gradient(135deg,rgba(245,200,66,0.05),rgba(0,224,255,0.04));display:flex;align-items:center;justify-content:center;overflow:hidden;}
.card-media img{width:100%;height:100%;object-fit:cover;transition:.5s;}
.content-card:hover .card-media img{transform:scale(1.07);}
.card-media video{width:100%;height:100%;object-fit:cover;}
.card-media .file-emoji{font-size:54px;}
.card-media audio{width:90%;}
.card-body{padding:22px;}
.card-cat{font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gold);margin-bottom:10px;font-family:var(--font-head);}
.card-title{font-size:16px;font-weight:700;margin-bottom:8px;line-height:1.4;font-family:var(--font-head);}
.card-desc{font-size:13px;opacity:.52;line-height:1.65;margin-bottom:18px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.card-footer{display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid rgba(255,255,255,0.06);}
.card-author{font-size:12px;opacity:.45;}
.card-download{
  padding:8px 18px;border-radius:30px;font-size:12px;font-weight:700;
  background:linear-gradient(90deg,var(--gold),var(--amber));
  color:#000;text-decoration:none;transition:.3s;
  font-family:var(--font-head);
  box-shadow:0 4px 14px rgba(245,200,66,0.3);
  position:relative;overflow:hidden;
}
.card-download::before{
  content:'';position:absolute;top:0;left:-60%;width:40%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,0.35),transparent);
  transform:skewX(-20deg);transition:left .4s;
}
.card-download:hover{transform:scale(1.07);box-shadow:0 6px 20px rgba(245,200,66,0.5);}
.card-download:hover::before{left:120%;}

.empty-state{
  grid-column:1/-1;text-align:center;padding:90px 20px;
  background:rgba(255,255,255,0.03);border-radius:22px;
  border:1px solid rgba(255,255,255,0.06);
}
.empty-state .empty-icon{font-size:60px;margin-bottom:18px;}
.empty-state h3{font-size:22px;margin-bottom:10px;font-family:var(--font-head);}
.empty-state p{opacity:.45;font-size:15px;margin-bottom:26px;}

/* ══════════════════════════════════
   HOW IT WORKS
══════════════════════════════════ */
.how-section{
  position:relative;z-index:10;padding:110px 80px;
  background:rgba(255,255,255,0.015);
  border-top:1px solid rgba(255,255,255,0.05);
  border-bottom:1px solid rgba(255,255,255,0.05);
}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:32px;margin-top:64px;}
.step{text-align:center;position:relative;}
.step-connector{
  position:absolute;top:30px;left:calc(50% + 36px);
  width:calc(100% - 72px);height:1px;
  background:linear-gradient(90deg,rgba(245,200,66,0.3),transparent);
}
.step:last-child .step-connector{display:none;}
.step-num{
  width:62px;height:62px;border-radius:50%;margin:0 auto 22px;
  background:linear-gradient(135deg,var(--gold),var(--amber));
  display:flex;align-items:center;justify-content:center;
  font-size:20px;font-weight:800;color:#000;
  font-family:var(--font-head);
  box-shadow:0 0 0 0 rgba(245,200,66,0.4);
  transition:.3s;
  animation:stepPulse 4s ease-in-out infinite;
}
.step:nth-child(1) .step-num{animation-delay:0s;}
.step:nth-child(2) .step-num{animation-delay:1s;}
.step:nth-child(3) .step-num{animation-delay:2s;}
.step:nth-child(4) .step-num{animation-delay:3s;}
@keyframes stepPulse{0%,100%{box-shadow:0 0 0 0 rgba(245,200,66,0.4);}50%{box-shadow:0 0 0 10px rgba(245,200,66,0);}}
.step h3{font-size:17px;font-weight:700;margin-bottom:10px;font-family:var(--font-head);}
.step p{font-size:13px;opacity:.52;line-height:1.7;font-weight:300;}

/* ══════════════════════════════════
   CTA SECTION
══════════════════════════════════ */
.cta-section{
  position:relative;z-index:10;text-align:center;padding:130px 20px;
  overflow:hidden;
}
.cta-bg{
  position:absolute;inset:0;z-index:-1;
  background:linear-gradient(135deg,rgba(245,200,66,0.05),rgba(0,224,255,0.04),rgba(167,139,250,0.05));
}
.cta-bg::before{
  content:'';position:absolute;inset:0;
  background:url('https://images.unsplash.com/photo-1548013146-72479768bada?w=1400&q=60') center/cover;
  opacity:.05;
}
.cta-section h2{font-size:56px;font-weight:800;margin-bottom:20px;font-family:var(--font-head);line-height:1.1;}
.cta-section p{font-size:18px;opacity:.6;margin-bottom:44px;font-weight:300;}

/* ══════════════════════════════════
   FOOTER
══════════════════════════════════ */
footer{
  position:relative;z-index:10;text-align:center;
  padding:32px;border-top:1px solid rgba(255,255,255,0.05);
  font-size:13px;opacity:.38;font-family:var(--font-head);letter-spacing:0.5px;
}

/* ══════════════════════════════════
   FLOATING HERITAGE ELEMENTS
══════════════════════════════════ */
.float-elem{
  position:fixed;z-index:6;pointer-events:none;
  font-size:28px;opacity:0.07;
  animation:floatElem 20s linear infinite;
}
.float-elem:nth-child(1){top:15%;left:5%;animation-duration:18s;font-size:24px;}
.float-elem:nth-child(2){top:45%;right:4%;animation-duration:24s;animation-delay:4s;}
.float-elem:nth-child(3){bottom:25%;left:8%;animation-duration:20s;animation-delay:8s;font-size:22px;}
.float-elem:nth-child(4){top:70%;right:7%;animation-duration:22s;animation-delay:2s;font-size:20px;}
@keyframes floatElem{
  0%{transform:translateY(0) rotate(0deg);}
  33%{transform:translateY(-30px) rotate(5deg);}
  66%{transform:translateY(20px) rotate(-5deg);}
  100%{transform:translateY(0) rotate(0deg);}
}

/* ══════════════════════════════════
   RESPONSIVE
══════════════════════════════════ */
@media(max-width:1024px){
  nav{padding:14px 30px;}
  nav.scrolled{padding:10px 30px;}
  .hero h1{font-size:56px;}
  .features-grid{grid-template-columns:repeat(2,1fr);}
  .steps{grid-template-columns:repeat(2,1fr);}
  .section,.content-section,.how-section{padding:70px 30px;}
}
@media(max-width:768px){
  nav ul{display:none;}
  .hero h1{font-size:38px;}
  .hero p{font-size:15px;}
  .features-grid{grid-template-columns:1fr;}
  .steps{grid-template-columns:1fr;}
  .step-connector{display:none;}
  .stat-item{padding:22px 28px;}
  .content-header{flex-direction:column;align-items:flex-start;gap:16px;}
  .cta-section h2{font-size:36px;}
}

/* ── REVEAL ANIMATIONS (Intersection Observer) ── */
.reveal{opacity:0;transform:translateY(28px);transition:opacity .7s ease,transform .7s ease;}
.reveal.visible{opacity:1;transform:translateY(0);}
.reveal-left{opacity:0;transform:translateX(-28px);transition:opacity .7s ease,transform .7s ease;}
.reveal-left.visible{opacity:1;transform:translateX(0);}
.reveal-scale{opacity:0;transform:scale(.92);transition:opacity .6s ease,transform .6s ease;}
.reveal-scale.visible{opacity:1;transform:scale(1);}
</style>
</head>
<body>

<!-- Heritage Background Slideshow -->
<div class="hero-bg">
  <div class="slide active"></div>
  <div class="slide"></div>
  <div class="slide"></div>
  <div class="hero-bg-overlay"></div>
  <div class="hero-bg-noise"></div>
  <div class="scan-line"></div>
</div>

<!-- Orbs -->
<div class="bg-orbs">
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>
</div>

<!-- Floating Heritage Symbols -->
<div class="float-elem">🏛️</div>
<div class="float-elem">⚙️</div>
<div class="float-elem">🕌</div>
<div class="float-elem">🤖</div>

<!-- Particle Canvas -->
<canvas id="particles-canvas"></canvas>

<!-- Overlay -->
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- ═══════════════════════════════
     SIDEBAR
═══════════════════════════════ -->
<aside class="sidebar" id="sidebar">
  <div class="sb-logo">
    <div class="sb-logo-ico">
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="3" y="11" width="18" height="10" rx="2" stroke="white" stroke-width="1.8"/>
        <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
        <circle cx="12" cy="16" r="1.5" fill="white"/>
      </svg>
    </div>
    <div>
      <div class="sb-logo-name">HeritechAI</div>
      <div class="sb-logo-sub">Community Platform</div>
    </div>
  </div>
  <div class="sb-nav">
    <a href="index.php" class="sb-item active">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></span>
      <span class="sb-lbl">Dashboard</span>
    </a>
    <a href="gallery.php?type=photo" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></span>
      <span class="sb-lbl">AI Photo</span>
    </a>
    <a href="gallery.php?type=video" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></span>
      <span class="sb-lbl">AI Video</span>
    </a>
    <a href="gallery.php?type=characters" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4L12 14.4l-4.8 2.5.9-5.4L4.2 7.7l5.4-.8z"/></svg></span>
      <span class="sb-lbl">AI Characters</span>
    </a>
    <a href="brands.php" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
      <span class="sb-lbl">Brands</span>
    </a>
    <div class="sb-div"></div>
    <div class="sb-grp">Explore</div>
    <div class="sb-item has-sub" id="compToggle" onclick="toggleSub('compSub','compToggle')">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/></svg></span>
      <span class="sb-lbl">Competition</span>
      <span class="sb-arr"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
    </div>
    <div class="sb-sub" id="compSub">
      <a href="competition.php?tab=active" class="sb-si"><span class="sb-dot"></span>Active Events</a>
      <a href="competition.php?tab=past" class="sb-si"><span class="sb-dot"></span>Past Events</a>
      <a href="competition.php?tab=leaderboard" class="sb-si"><span class="sb-dot"></span>Leaderboard</a>
    </div>
    <div class="sb-item has-sub" id="teamsToggle" onclick="toggleSub('teamsSub','teamsToggle')">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
      <span class="sb-lbl">Teams</span>
      <span class="sb-arr"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
    </div>
    <div class="sb-sub" id="teamsSub">
      <a href="teams.php?tab=my" class="sb-si"><span class="sb-dot"></span>My Team</a>
      <a href="teams.php?tab=browse" class="sb-si"><span class="sb-dot"></span>Browse Teams</a>
      <a href="teams.php?tab=create" class="sb-si"><span class="sb-dot"></span>Create Team</a>
    </div>
    <div class="sb-div"></div>
    <div class="sb-grp">Community</div>
    <a href="collaborators.php" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M10.5 20H4a2 2 0 0 1-2-2v-1a5 5 0 0 1 5-5h2.5"/><circle cx="9" cy="7" r="4"/><path d="m17 13 2 2 4-4"/></svg></span>
      <span class="sb-lbl">Our Collaborators</span>
    </a>
    <a href="about.php" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
      <span class="sb-lbl">About Community</span>
    </a>
    <?php if(isset($_SESSION['user_id'])): ?>
    <div class="sb-div"></div>
    <div class="sb-grp">My Space</div>
    <a href="submit.php" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></span>
      <span class="sb-lbl">Upload Content</span>
    </a>
    <a href="dashboard.php" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
      <span class="sb-lbl">My Dashboard</span>
    </a>
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
    <a href="admin/admin_panel.php" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
      <span class="sb-lbl">Admin Panel</span>
    </a>
    <?php endif; ?>
    <a href="account.php" class="sb-item">
      <span class="sb-ico"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
      <span class="sb-lbl">Account Settings</span>
    </a>
    <?php endif; ?>
  </div>
  <div class="sb-foot">
    <?php if(!isset($_SESSION['user_id'])): ?>
      <a href="register.php" class="sb-cta">
        <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Start Generating &amp; Join
      </a>
    <?php else: ?>
      <a href="submit.php" class="sb-cta">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Upload New Content
      </a>
    <?php endif; ?>
    <a href="contact.php" class="sb-help">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      Help &amp; Support
    </a>
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
    <div class="logo">🏛️ Heri<span>tech</span>AI</div>
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
      <li><a href="contact.php">Contact Us</a></li>
      <li><a href="login.php">Login</a></li>
      <li><a href="register.php" class="btn-nav">Register Now →</a></li>
    <?php endif; ?>
  </ul>
</nav>

<!-- ═══════════════════════════════
     HERO
═══════════════════════════════ -->
<section class="hero">
  <div class="heritage-badge">
    <span class="pulse-dot"></span>
    🏛️ Heritage Meets Artificial Intelligence
  </div>
  <h1>
    <span class="grad-gold">Heritage.</span>
    <span class="grad-cyan">Innovation.</span>
    Future.
  </h1>
  <p>Where ancient wisdom meets cutting-edge AI. Upload videos, images, audio, and research. Connect with innovators worldwide and shape the future where culture and technology converge.</p>
  <div class="hero-btns">
    <?php if(!isset($_SESSION['user_id'])): ?>
      <a href="register.php" class="btn-hero btn-primary">🚀 Join the Community</a>
      <a href="login.php" class="btn-hero btn-electric">Login →</a>
    <?php else: ?>
      <a href="submit.php" class="btn-hero btn-primary">📤 Upload Content</a>
      <a href="dashboard.php" class="btn-hero btn-electric">My Dashboard →</a>
    <?php endif; ?>
  </div>
  <div class="scroll-hint">
    <div class="scroll-temple">🏛️</div>
    <div class="scroll-line"></div>
    Explore Below
  </div>
</section>

<!-- STATS -->
<div class="stats-bar">
  <div class="stat-item reveal">
    <span class="stat-num" data-count="<?php echo $total_users; ?>"><?php echo $total_users; ?>+</span>
    <div class="stat-lbl">Community Members</div>
  </div>
  <div class="stat-item reveal" style="transition-delay:.1s">
    <span class="stat-num" data-count="<?php echo $total_content; ?>"><?php echo $total_content; ?>+</span>
    <div class="stat-lbl">Approved Works</div>
  </div>
  <div class="stat-item reveal" style="transition-delay:.2s">
    <span class="stat-num">4+</span>
    <div class="stat-lbl">Content Categories</div>
  </div>
  <div class="stat-item reveal" style="transition-delay:.3s">
    <span class="stat-num">24/7</span>
    <div class="stat-lbl">Admin Review</div>
  </div>
</div>

<!-- FEATURES -->
<section class="section">
  <div class="section-label">What We Offer</div>
  <div class="section-title">Everything You Need<br>to Share AI Work</div>
  <div class="section-sub">From AI-generated art to research papers — share any format with our growing community of innovators preserving cultural heritage through AI.</div>
  <div class="features-grid">
    <div class="feat-card reveal" style="transition-delay:.05s"><span class="feat-icon">🎥</span><h3>Video & Audio</h3><p>Share AI-generated videos, demos, tutorials, and audio projects exploring cultural narratives.</p></div>
    <div class="feat-card reveal" style="transition-delay:.1s"><span class="feat-icon">🖼️</span><h3>Images & Art</h3><p>Showcase AI-generated artwork, heritage visualizations, and creative image projects.</p></div>
    <div class="feat-card reveal" style="transition-delay:.15s"><span class="feat-icon">📄</span><h3>Documents & Research</h3><p>Upload PDFs, presentations, and research papers on AI and cultural innovation.</p></div>
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
      <div class="section-title" style="font-size:34px;margin-bottom:0;">Latest Approved Content</div>
    </div>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="submit.php" class="btn-hero btn-primary" style="padding:13px 30px;font-size:14px;">+ Upload Yours</a>
    <?php else: ?>
      <a href="register.php" class="btn-hero btn-primary" style="padding:13px 30px;font-size:14px;">Join to Upload</a>
    <?php endif; ?>
  </div>
  <div class="content-grid">
  <?php
  $img_exts=['jpg','jpeg','png','gif'];$video_exts=['mp4','avi','mov','mkv'];$audio_exts=['mp3','wav'];
  $icons=['pdf'=>'📄','doc'=>'📝','docx'=>'📝','ppt'=>'📊','pptx'=>'📊','xls'=>'📋','xlsx'=>'📋','zip'=>'🗜️','rar'=>'🗜️','txt'=>'📃'];
  if($approved_content && $approved_content->num_rows > 0):
    $delay=0;
    while($item = $approved_content->fetch_assoc()):
      $ext = strtolower(pathinfo($item['file_path'], PATHINFO_EXTENSION));
      $delay+=0.05;
  ?>
    <div class="content-card reveal" style="transition-delay:<?php echo $delay; ?>s">
      <div class="card-media">
        <?php if(in_array($ext,$img_exts)): ?><img src="<?php echo htmlspecialchars($item['file_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
        <?php elseif(in_array($ext,$video_exts)): ?><video controls preload="none"><source src="<?php echo htmlspecialchars($item['file_path']); ?>"></video>
        <?php elseif(in_array($ext,$audio_exts)): ?><audio controls><source src="<?php echo htmlspecialchars($item['file_path']); ?>"></audio>
        <?php else: ?><div class="file-emoji"><?php echo $icons[$ext] ?? '📁'; ?></div><?php endif; ?>
      </div>
      <div class="card-body">
        <div class="card-cat"><?php echo htmlspecialchars($item['category']); ?></div>
        <div class="card-title"><?php echo htmlspecialchars($item['title']); ?></div>
        <div class="card-desc"><?php echo htmlspecialchars($item['description']); ?></div>
        <div class="card-footer">
          <span class="card-author">by <?php echo htmlspecialchars($item['uploader_name']); ?></span>
          <a class="card-download" href="<?php echo htmlspecialchars($item['file_path']); ?>" download>⬇ Download</a>
        </div>
      </div>
    </div>
  <?php endwhile; else: ?>
    <div class="empty-state">
      <div class="empty-icon">🏛️</div><h3>No Content Yet</h3>
      <p>Be the first to upload heritage AI content to the community!</p>
      <a href="<?php echo isset($_SESSION['user_id']) ? 'submit.php' : 'register.php'; ?>" class="btn-hero btn-primary">
        <?php echo isset($_SESSION['user_id']) ? '📤 Upload Now' : '🚀 Join & Upload'; ?>
      </a>
    </div>
  <?php endif; ?>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section">
  <div style="text-align:center;">
    <div class="section-label" style="justify-content:center;">How It Works</div>
    <div class="section-title" style="font-size:40px;text-align:center;">4 Simple Steps</div>
  </div>
  <div class="steps">
    <div class="step reveal">
      <div class="step-connector"></div>
      <div class="step-num">1</div>
      <h3>Register</h3>
      <p>Create your free account and verify your email to join the heritage AI community.</p>
    </div>
    <div class="step reveal" style="transition-delay:.1s">
      <div class="step-connector"></div>
      <div class="step-num">2</div>
      <h3>Upload</h3>
      <p>Share your AI projects — videos, images, audio, documents and more.</p>
    </div>
    <div class="step reveal" style="transition-delay:.2s">
      <div class="step-connector"></div>
      <div class="step-num">3</div>
      <h3>Review</h3>
      <p>Our admin team reviews your content to maintain quality standards.</p>
    </div>
    <div class="step reveal" style="transition-delay:.3s">
      <div class="step-connector"></div>
      <div class="step-num">4</div>
      <h3>Go Live</h3>
      <p>Approved content appears on the homepage for the world to see!</p>
    </div>
  </div>
</section>

<!-- CTA -->
<?php if(!isset($_SESSION['user_id'])): ?>
<section class="cta-section">
  <div class="cta-bg"></div>
  <h2>Ready to <span style="background:linear-gradient(90deg,var(--gold),var(--amber));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Join?</span></h2>
  <p>Join thousands of AI innovators and heritage enthusiasts sharing their work every day.</p>
  <a href="register.php" class="btn-hero btn-primary" style="font-size:18px;padding:17px 46px;">Get Started for Free →</a>
</section>
<?php endif; ?>

<footer><p>© <?php echo date("Y"); ?> HeritechAI Community — Where Heritage Meets Innovation 🏛️🤖</p></footer>

<!-- ═══════════════════════════════
     SCRIPTS
═══════════════════════════════ -->
<script>
/* ── Sidebar ── */
const sidebar   = document.getElementById('sidebar');
const overlay   = document.getElementById('sbOverlay');
const hamburger = document.getElementById('hamburger');
function toggleSidebar(){ sidebar.classList.contains('open') ? closeSidebar() : openSidebar(); }
function openSidebar(){sidebar.classList.add('open');overlay.classList.add('show');hamburger.classList.add('active');document.body.style.overflow='hidden';}
function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('show');hamburger.classList.remove('active');document.body.style.overflow='';}
function toggleSub(subId, toggleId){
  const sub=document.getElementById(subId), toggle=document.getElementById(toggleId);
  const isOpen=sub.classList.contains('open');
  document.querySelectorAll('.sb-sub').forEach(s=>s.classList.remove('open'));
  document.querySelectorAll('.sb-item.has-sub').forEach(t=>t.classList.remove('expanded'));
  if(!isOpen){sub.classList.add('open');toggle.classList.add('expanded');}
}
document.addEventListener('keydown',e=>{if(e.key==='Escape') closeSidebar();});

/* ── Navbar scroll shrink ── */
window.addEventListener('scroll',()=>{
  document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 60);
});

/* ── Background slideshow ── */
const slides = document.querySelectorAll('.hero-bg .slide');
let current = 0;
setInterval(() => {
  slides[current].classList.remove('active');
  current = (current + 1) % slides.length;
  slides[current].classList.add('active');
}, 6000);

/* ── Particle system ── */
(function(){
  const canvas = document.getElementById('particles-canvas');
  const ctx = canvas.getContext('2d');
  let W, H, particles = [];
  function resize(){ W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
  resize();
  window.addEventListener('resize', resize);
  const COLORS = ['rgba(245,200,66,', 'rgba(0,224,255,', 'rgba(167,139,250,', 'rgba(0,255,166,'];
  function Particle(){
    this.reset = function(){
      this.x = Math.random() * W;
      this.y = Math.random() * H;
      this.r = Math.random() * 2 + 0.3;
      this.vx = (Math.random() - 0.5) * 0.4;
      this.vy = -Math.random() * 0.6 - 0.2;
      this.alpha = Math.random() * 0.5 + 0.1;
      this.color = COLORS[Math.floor(Math.random()*COLORS.length)];
      this.life = 0; this.maxLife = Math.random() * 200 + 100;
    };
    this.reset();
    this.y = Math.random() * H; // distribute on load
  }
  for(let i=0;i<80;i++) particles.push(new Particle());
  function draw(){
    ctx.clearRect(0,0,W,H);
    particles.forEach(p=>{
      p.x += p.vx; p.y += p.vy; p.life++;
      const fade = p.life < 30 ? p.life/30 : p.life > p.maxLife-30 ? (p.maxLife-p.life)/30 : 1;
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
      ctx.fillStyle = p.color + (p.alpha * fade) + ')';
      ctx.fill();
      if(p.life >= p.maxLife || p.y < -10) p.reset();
    });
    requestAnimationFrame(draw);
  }
  draw();
})();

/* ── Intersection Observer for reveals ── */
const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-scale');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.12 });
revealEls.forEach(el => observer.observe(el));

/* ── Counter animation ── */
function animateCounter(el, target){
  let start = 0; const dur = 1800;
  const step = timestamp => {
    if(!start) start = timestamp;
    const progress = Math.min((timestamp - start)/dur, 1);
    const ease = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(ease * target) + '+';
    if(progress < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}
const counterObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if(e.isIntersecting){
      const target = parseInt(e.target.dataset.count);
      if(!isNaN(target) && target > 0) animateCounter(e.target, target);
      counterObs.unobserve(e.target);
    }
  });
}, {threshold:0.5});
document.querySelectorAll('.stat-num[data-count]').forEach(el => counterObs.observe(el));
</script>
</body>
</html>