<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ FIXED: uses "contents" table
$my_content = $conn->query("SELECT * FROM contents WHERE creator_id=$user_id ORDER BY id DESC");
$total    = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE creator_id=$user_id")->fetch_assoc()['c'];
$pending  = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE creator_id=$user_id AND status='pending'")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE creator_id=$user_id AND status='approved'")->fetch_assoc()['c'];
$rejected = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE creator_id=$user_id AND status='rejected'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | AI Community</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{min-height:100vh;background:linear-gradient(135deg,#0a1628,#0f2027,#203a43);color:white;}

.bg-orb{
  position:fixed;border-radius:50%;filter:blur(100px);opacity:.1;pointer-events:none;
}
.orb1{width:400px;height:400px;background:#00e0ff;top:-100px;right:-100px;}
.orb2{width:300px;height:300px;background:#a78bfa;bottom:-50px;left:-50px;}

nav{
  display:flex;justify-content:space-between;align-items:center;
  padding:18px 60px;background:rgba(255,255,255,0.04);
  backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.06);
  position:sticky;top:0;z-index:100;
}

.logo{font-size:22px;font-weight:700;}
.logo span{color:#00e0ff;}

}
.nav-links{display:flex;gap:10px;}
.nav-links a{
  text-decoration:none;color:rgba(255,255,255,.7);font-size:14px;
  padding:8px 18px;border-radius:20px;background:rgba(255,255,255,.06);transition:.3s;
}
.nav-links a:hover{color:white;background:rgba(255,255,255,.12);}
.nav-links a.logout{background:linear-gradient(45deg,#ff416c,#ff4b2b);color:white;}
.nav-links a.upload-btn{background:linear-gradient(90deg,#00e0ff,#00ffa6);color:#000;font-weight:700;}

.container{max-width:900px;margin:50px auto;padding:0 20px;}

.welcome-card{
  background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);
  border-radius:24px;padding:36px;margin-bottom:30px;
  display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;
  animation:fadeUp .6s ease;
}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.welcome-text h2{font-size:26px;font-weight:700;margin-bottom:6px;}
.welcome-text p{opacity:.55;font-size:14px;}
.welcome-actions{display:flex;gap:10px;flex-wrap:wrap;}
.btn{
  padding:10px 24px;border-radius:20px;font-size:14px;font-weight:600;
  text-decoration:none;transition:.3s;border:none;cursor:pointer;
}
.btn-upload{background:linear-gradient(90deg,#00e0ff,#00ffa6);color:#000;}
.btn-upload:hover{transform:scale(1.05);box-shadow:0 0 20px rgba(0,224,255,0.3);}
.btn-logout{background:linear-gradient(45deg,#ff416c,#ff4b2b);color:white;}
.btn-logout:hover{transform:scale(1.05);}

.mini-stats{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:16px;margin-bottom:30px;animation:fadeUp .6s .1s ease both;
}
.mini-stat{
  background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);
  border-radius:16px;padding:20px;text-align:center;
}
.mini-stat .n{font-size:30px;font-weight:800;display:block;}
.mini-stat .l{font-size:12px;opacity:.5;margin-top:4px;}
.n-total{color:#38bdf8;}
.n-pending{color:#f0c040;}
.n-approved{color:#00ffa6;}
.n-rejected{color:#ff416c;}

.uploads-section{animation:fadeUp .6s .2s ease both;}
.sec-title{
  font-size:18px;font-weight:700;margin-bottom:20px;
  display:flex;align-items:center;gap:10px;
}

.upload-list{display:flex;flex-direction:column;gap:12px;}
.upload-item{
  background:rgba(255,255,255,0.05);
  border:1px solid rgba(255,255,255,0.07);
  border-radius:14px;padding:18px 22px;
  display:flex;justify-content:space-between;align-items:center;
  transition:.3s;flex-wrap:wrap;gap:12px;
}
.upload-item:hover{background:rgba(255,255,255,0.09);border-color:rgba(0,224,255,0.2);}
.item-left{display:flex;align-items:center;gap:14px;flex:1;}
.item-icon{font-size:30px;width:50px;height:50px;display:flex;align-items:center;
  justify-content:center;background:rgba(255,255,255,0.06);border-radius:12px;}
.item-info .title{font-size:15px;font-weight:600;margin-bottom:4px;}
.item-info .meta{font-size:12px;opacity:.45;}

.badge{padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;display:inline-block;}
.badge.pending{background:rgba(240,192,64,0.15);color:#f0c040;border:1px solid rgba(240,192,64,0.3);}
.badge.approved{background:rgba(0,255,166,0.15);color:#00ffa6;border:1px solid rgba(0,255,166,0.3);}
.badge.rejected{background:rgba(255,65,108,0.15);color:#ff416c;border:1px solid rgba(255,65,108,0.3);}

.reject-reason{
  font-size:12px;color:#ff416c;margin-top:6px;
  background:rgba(255,65,108,0.08);border-radius:8px;padding:6px 10px;
  border-left:2px solid #ff416c;
}

.empty{
  text-align:center;padding:60px;
  background:rgba(255,255,255,0.04);border-radius:16px;
  border:1px solid rgba(255,255,255,0.06);
}
.empty .e-icon{font-size:50px;margin-bottom:14px;}
.empty p{opacity:.5;font-size:15px;margin-bottom:20px;}

@media(max-width:700px){
  nav{padding:14px 20px;}
  .mini-stats{grid-template-columns:repeat(2,1fr);}
  .container{padding:0 14px;}
}
</style>
</head>
<body>

<div class="bg-orb orb1"></div>
<div class="bg-orb orb2"></div>

<nav>
  <div class="logo">🤖 HeritechAI</div>
  <div class="nav-links">
    <a href="index.php">🏠 Home</a>
    <a href="submit.php" class="upload-btn">📤 Upload</a>
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
      <a href="admin/admin_panel.php" style="background:linear-gradient(90deg,#a78bfa,#f472b6);color:#000;font-weight:700;">⚙️ Admin</a>
    <?php endif; ?>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>

<div class="container">

  <!-- WELCOME -->
  <div class="welcome-card">
    <div class="welcome-text">
      <h2>Welcome back 👋</h2>
      <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
    </div>
    <div class="welcome-actions">
      <a href="submit.php" class="btn btn-upload">📤 Upload Content</a>
      <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>
  </div>

  <!-- MINI STATS -->
  <div class="mini-stats">
    <div class="mini-stat">
      <span class="n n-total"><?php echo $total; ?></span>
      <div class="l">Total Uploads</div>
    </div>
    <div class="mini-stat">
      <span class="n n-pending"><?php echo $pending; ?></span>
      <div class="l">⏳ Pending</div>
    </div>
    <div class="mini-stat">
      <span class="n n-approved"><?php echo $approved; ?></span>
      <div class="l">✅ Approved</div>
    </div>
    <div class="mini-stat">
      <span class="n n-rejected"><?php echo $rejected; ?></span>
      <div class="l">❌ Rejected</div>
    </div>
  </div>

  <!-- UPLOADS LIST -->
  <div class="uploads-section">
    <div class="sec-title">📁 My Uploads</div>

    <?php
    $icons = ['jpg'=>'🖼️','jpeg'=>'🖼️','png'=>'🖼️','gif'=>'🖼️',
              'mp4'=>'🎥','avi'=>'🎥','mov'=>'🎥','mkv'=>'🎥',
              'mp3'=>'🎵','wav'=>'🎵','pdf'=>'📄',
              'doc'=>'📝','docx'=>'📝','ppt'=>'📊','pptx'=>'📊',
              'xls'=>'📋','xlsx'=>'📋','zip'=>'🗜️','rar'=>'🗜️','txt'=>'📃'];
    $labels = ['pending'=>'⏳ Pending','approved'=>'✅ Approved','rejected'=>'❌ Rejected'];
    ?>

    <div class="upload-list">
    <?php if($my_content && $my_content->num_rows > 0):
      while($item = $my_content->fetch_assoc()):
        $ext = strtolower(pathinfo($item['file_path'], PATHINFO_EXTENSION));
        $icon = $icons[$ext] ?? '📁';
    ?>
      <div class="upload-item">
        <div class="item-left">
          <div class="item-icon"><?php echo $icon; ?></div>
          <div class="item-info">
            <div class="title"><?php echo htmlspecialchars($item['title']); ?></div>
            <div class="meta">
              <?php echo htmlspecialchars($item['category']); ?> •
              <?php echo isset($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : 'Uploaded'; ?>
            </div>
            <?php if($item['status']==='rejected' && !empty($item['reject_reason'])): ?>
              <div class="reject-reason">Reason: <?php echo htmlspecialchars($item['reject_reason']); ?></div>
            <?php endif; ?>
          </div>
        </div>
        <span class="badge <?php echo $item['status']; ?>">
          <?php echo $labels[$item['status']] ?? $item['status']; ?>
        </span>
      </div>
    <?php endwhile; else: ?>
      <div class="empty">
        <div class="e-icon">📭</div>
        <p>You haven't uploaded anything yet.</p>
        <a href="submit.php" class="btn btn-upload">📤 Upload Now</a>
      </div>
    <?php endif; ?>
    </div>
  </div>

</div>
</body>
</html>