<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ✅ FIXED: all queries use "contents" table not "content"
$total_pending  = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE status='pending'")->fetch_assoc()['c'];
$total_approved = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE status='approved'")->fetch_assoc()['c'];
$total_rejected = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE status='rejected'")->fetch_assoc()['c'];
$total_users    = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='user'")->fetch_assoc()['c'];
$total_logs     = $conn->query("SELECT COUNT(*) AS c FROM admin_logs")->fetch_assoc()['c'];

// ✅ FIXED: JOIN on "contents" table
$pending  = $conn->query("SELECT c.*, u.name AS uploader_name, u.email AS uploader_email FROM contents c JOIN users u ON c.creator_id=u.id WHERE c.status='pending'  ORDER BY c.id DESC");
$approved = $conn->query("SELECT c.*, u.name AS uploader_name, u.email AS uploader_email FROM contents c JOIN users u ON c.creator_id=u.id WHERE c.status='approved' ORDER BY c.id DESC");
$rejected = $conn->query("SELECT c.*, u.name AS uploader_name, u.email AS uploader_email FROM contents c JOIN users u ON c.creator_id=u.id WHERE c.status='rejected' ORDER BY c.id DESC");
$users    = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM contents WHERE creator_id=u.id) AS uploads FROM users u WHERE u.role='user' ORDER BY u.id DESC");
$logs     = $conn->query("SELECT * FROM admin_logs ORDER BY acted_at DESC");

function fileIcon($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $map = ['jpg'=>'🖼️','jpeg'=>'🖼️','png'=>'🖼️','gif'=>'🖼️',
            'mp4'=>'🎥','avi'=>'🎥','mov'=>'🎥','mkv'=>'🎥',
            'mp3'=>'🎵','wav'=>'🎵','pdf'=>'📄',
            'doc'=>'📝','docx'=>'📝','ppt'=>'📊','pptx'=>'📊',
            'xls'=>'📋','xlsx'=>'📋','zip'=>'🗜️','rar'=>'🗜️','txt'=>'📃'];
    return $map[$ext] ?? '📁';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel | AI Community</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);color:white;min-height:100vh;}

.navbar{
  display:flex;justify-content:space-between;align-items:center;
  padding:16px 50px;background:rgba(255,255,255,0.05);
  backdrop-filter:blur(10px);border-bottom:1px solid rgba(255,255,255,0.1);
  position:sticky;top:0;z-index:100;
}
.logo{font-size:22px;font-weight:700;}
.logo span{color:#00e0ff;}
.admin-badge {
  padding: 2px 8px; border-radius: 5px;
  background: rgba(79,140,255,0.12);
  border: 1px solid rgba(79,140,255,0.25);
  font-size: 10px; font-weight: 700;
  color: var(--accent); letter-spacing: 0.8px;
  text-transform: uppercase;
}

.nav-right{display:flex;align-items:center;gap:10px;}
.nav-right a{
  color:white;text-decoration:none;font-size:13px;
  padding:7px 16px;border-radius:20px;background:rgba(255,255,255,0.08);transition:.3s;
}
.nav-right a:hover{background:rgba(0,224,255,0.15);color:#00e0ff;}
.nav-right a.logout{background:linear-gradient(45deg,#ff416c,#ff4b2b);}

.stats-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;padding:30px 50px 10px;}
.stat-card{
  background:rgba(255,255,255,0.07);border-radius:14px;padding:20px;
  text-align:center;border:1px solid rgba(255,255,255,0.1);
  backdrop-filter:blur(10px);transition:.3s;cursor:pointer;
}
.stat-card:hover{transform:translateY(-4px);box-shadow:0 10px 25px rgba(0,224,255,0.15);}
.stat-card .num{font-size:34px;font-weight:700;display:block;margin-bottom:4px;}
.stat-card .lbl{font-size:12px;opacity:.65;}
.s-pending .num{color:#f0c040;}
.s-approved .num{color:#00ffa6;}
.s-rejected .num{color:#ff416c;}
.s-users .num{color:#a78bfa;}
.s-logs .num{color:#38bdf8;}

.tabs{display:flex;gap:8px;padding:24px 50px 8px;flex-wrap:wrap;}
.tab{
  padding:9px 22px;border-radius:20px;cursor:pointer;
  font-size:13px;font-weight:500;border:none;
  background:rgba(255,255,255,0.08);color:white;transition:.3s;
}
.tab.active{background:linear-gradient(90deg,#00e0ff,#00ffa6);color:#000;font-weight:600;}
.tab:hover:not(.active){background:rgba(255,255,255,0.15);}

.section{padding:8px 50px 50px;display:none;}
.section.active{display:block;}
.section-title{
  font-size:17px;font-weight:600;margin-bottom:16px;
  padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.08);
}

.tbl-wrap{overflow-x:auto;border-radius:14px;border:1px solid rgba(255,255,255,0.1);}
table{width:100%;border-collapse:collapse;background:rgba(255,255,255,0.04);}
thead th{
  padding:13px 14px;text-align:left;font-size:12px;
  background:rgba(255,255,255,0.08);color:#00e0ff;font-weight:600;white-space:nowrap;
}
tbody tr{border-top:1px solid rgba(255,255,255,0.05);transition:.2s;}
tbody tr:hover{background:rgba(255,255,255,0.04);}
tbody td{padding:11px 14px;font-size:13px;vertical-align:middle;}
.no-data{text-align:center;padding:50px;opacity:.45;font-size:15px;}

.badge{padding:4px 11px;border-radius:20px;font-size:11px;font-weight:600;display:inline-block;}
.badge.pending{background:rgba(240,192,64,0.2);color:#f0c040;}
.badge.approved{background:rgba(0,255,166,0.2);color:#00ffa6;}
.badge.rejected{background:rgba(255,65,108,0.2);color:#ff416c;}

.actions{display:flex;gap:6px;flex-wrap:wrap;}
.btn-approve,.btn-reject,.btn-view,.btn-remove{
  padding:5px 13px;border:none;border-radius:20px;
  cursor:pointer;font-size:12px;font-weight:600;
  transition:.3s;text-decoration:none;display:inline-block;
}
.btn-approve{background:linear-gradient(45deg,#00ffa6,#00c6ff);color:#000;}
.btn-reject{background:linear-gradient(45deg,#ff416c,#ff4b2b);color:white;}
.btn-view{background:rgba(255,255,255,0.12);color:white;}
.btn-remove{background:rgba(255,65,108,0.2);color:#ff416c;border:1px solid #ff416c;}
.btn-approve:hover,.btn-reject:hover,.btn-view:hover,.btn-remove:hover{transform:scale(1.06);}

.alert{margin:8px 50px;padding:11px 18px;border-radius:10px;font-size:13px;}
.alert.success{background:rgba(0,255,166,0.12);border:1px solid #00ffa6;color:#00ffa6;}
.alert.error{background:rgba(255,65,108,0.12);border:1px solid #ff416c;color:#ff416c;}

.modal-bg{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,.75);backdrop-filter:blur(5px);
  z-index:999;align-items:center;justify-content:center;
}
.modal-bg.open{display:flex;}
.modal{
  background:#1a2a35;border-radius:20px;padding:30px;
  width:440px;max-width:92%;border:1px solid rgba(255,255,255,0.1);
  animation:mUp .3s ease;
}
@keyframes mUp{from{opacity:0;transform:translateY(28px);}to{opacity:1;transform:translateY(0);}}
.modal h3{margin-bottom:6px;font-size:18px;}
.modal p{font-size:13px;opacity:.6;margin-bottom:14px;}
.modal textarea{
  width:100%;background:rgba(255,255,255,0.07);
  border:1px solid rgba(255,255,255,0.15);border-radius:10px;
  color:white;padding:12px;font-size:14px;resize:none;
  height:110px;outline:none;font-family:inherit;
}
.modal textarea:focus{border-color:#00e0ff;}
.modal-actions{display:flex;gap:10px;margin-top:14px;justify-content:flex-end;}
.modal-actions button{
  padding:8px 20px;border:none;border-radius:20px;
  cursor:pointer;font-size:14px;font-weight:500;
}
.btn-cancel{background:rgba(255,255,255,0.1);color:white;}
.btn-do-reject{background:linear-gradient(45deg,#ff416c,#ff4b2b);color:white;}

.log-action-approved{color:#00ffa6;font-weight:600;}
.log-action-rejected{color:#ff416c;font-weight:600;}
.log-action-deleted{color:#9ca3af;font-weight:600;}
.log-reason{font-size:12px;opacity:.6;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.log-time{font-size:12px;opacity:.55;white-space:nowrap;}

@media(max-width:900px){
  .stats-grid{grid-template-columns:repeat(2,1fr);}
  .tabs,.section,.stats-grid,.alert{padding-left:16px;padding-right:16px;}
  .navbar{padding:14px 16px;}
}
</style>
</head>
<body>

<div class="navbar">
  <div class="logo">🤖  HeritechAI <span class="admin-badge">ADMIN</span></div>
  <div class="nav-right">
    <a href="../index.php">🌐 View Site</a>
    <a href="admin_logout.php" class="logout">🚪 Logout</a>
  </div>
</div>

<?php if(isset($_GET['msg'])): ?>
  <div class="alert success">✅ <?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>
<?php if(isset($_GET['err'])): ?>
  <div class="alert error">⚠️ <?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<div class="stats-grid">
  <div class="stat-card s-pending"  onclick="showTab('pending')">
    <span class="num"><?php echo $total_pending; ?></span>
    <div class="lbl">⏳ Pending</div>
  </div>
  <div class="stat-card s-approved" onclick="showTab('approved')">
    <span class="num"><?php echo $total_approved; ?></span>
    <div class="lbl">✅ Approved</div>
  </div>
  <div class="stat-card s-rejected" onclick="showTab('rejected')">
    <span class="num"><?php echo $total_rejected; ?></span>
    <div class="lbl">❌ Rejected</div>
  </div>
  <div class="stat-card s-users" onclick="showTab('users')">
    <span class="num"><?php echo $total_users; ?></span>
    <div class="lbl">👥 Users</div>
  </div>
  <div class="stat-card s-logs" onclick="showTab('logs')">
    <span class="num"><?php echo $total_logs; ?></span>
    <div class="lbl">📋 Admin Logs</div>
  </div>
</div>

<div class="tabs">
  <button class="tab active" id="tab-pending"  onclick="showTab('pending')">⏳ Pending (<?php echo $total_pending; ?>)</button>
  <button class="tab"        id="tab-approved" onclick="showTab('approved')">✅ Approved (<?php echo $total_approved; ?>)</button>
  <button class="tab"        id="tab-rejected" onclick="showTab('rejected')">❌ Rejected (<?php echo $total_rejected; ?>)</button>
  <button class="tab"        id="tab-users"    onclick="showTab('users')">👥 Users (<?php echo $total_users; ?>)</button>
  <button class="tab"        id="tab-logs"     onclick="showTab('logs')">📋 Admin Logs (<?php echo $total_logs; ?>)</button>
</div>

<!-- PENDING TAB -->
<div class="section active" id="sec-pending">
  <div class="section-title">⏳ Pending Content — Waiting for Your Review</div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Type</th><th>Title</th><th>Description</th>
          <th>Category</th><th>Uploaded By</th><th>Email</th>
          <th>Date</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if($pending && $pending->num_rows > 0): $i=1; while($r=$pending->fetch_assoc()): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td style="font-size:22px;"><?php echo fileIcon($r['file_path']); ?></td>
          <td><strong><?php echo htmlspecialchars($r['title']); ?></strong></td>
          <td style="max-width:150px;font-size:12px;opacity:.7;">
            <?php echo htmlspecialchars(mb_strimwidth($r['description'],0,60,'...')); ?>
          </td>
          <td><?php echo htmlspecialchars($r['category']); ?></td>
          <td><?php echo htmlspecialchars($r['uploader_name']); ?></td>
          <td style="font-size:11px;opacity:.6;"><?php echo htmlspecialchars($r['uploader_email']); ?></td>
          <td class="log-time"><?php echo isset($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : '—'; ?></td>
          <td><span class="badge pending">Pending</span></td>
          <td>
            <div class="actions">
              <a class="btn-view" href="../<?php echo htmlspecialchars($r['file_path']); ?>" target="_blank">👁 View</a>
              <a class="btn-approve" href="admin_action.php?action=approve&id=<?php echo $r['id']; ?>">✅ Approve</a>
              <button class="btn-reject" onclick="openReject(<?php echo $r['id']; ?>,'<?php echo addslashes($r['uploader_email']); ?>','<?php echo addslashes($r['title']); ?>','<?php echo addslashes($r['uploader_name']); ?>')">❌ Reject</button>
            </div>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="10"><div class="no-data">🎉 No pending content — all clear!</div></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- APPROVED TAB -->
<div class="section" id="sec-approved">
  <div class="section-title">✅ Approved Content — Live on Homepage</div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Type</th><th>Title</th><th>Category</th>
          <th>Uploaded By</th><th>Email</th><th>Date</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if($approved && $approved->num_rows > 0): $i=1; while($r=$approved->fetch_assoc()): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td style="font-size:22px;"><?php echo fileIcon($r['file_path']); ?></td>
          <td><strong><?php echo htmlspecialchars($r['title']); ?></strong></td>
          <td><?php echo htmlspecialchars($r['category']); ?></td>
          <td><?php echo htmlspecialchars($r['uploader_name']); ?></td>
          <td style="font-size:11px;opacity:.6;"><?php echo htmlspecialchars($r['uploader_email']); ?></td>
          <td class="log-time"><?php echo isset($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : '—'; ?></td>
          <td><span class="badge approved">Approved</span></td>
          <td>
            <div class="actions">
              <a class="btn-view" href="../<?php echo htmlspecialchars($r['file_path']); ?>" target="_blank">👁 View</a>
              <a class="btn-remove" href="admin_action.php?action=remove&id=<?php echo $r['id']; ?>"
                 onclick="return confirm('Move back to pending?')">↩ Remove</a>
            </div>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="9"><div class="no-data">No approved content yet.</div></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- REJECTED TAB -->
<div class="section" id="sec-rejected">
  <div class="section-title">❌ Rejected Content</div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Type</th><th>Title</th><th>Category</th>
          <th>Uploaded By</th><th>Email</th><th>Reject Reason</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if($rejected && $rejected->num_rows > 0): $i=1; while($r=$rejected->fetch_assoc()): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td style="font-size:22px;"><?php echo fileIcon($r['file_path']); ?></td>
          <td><strong><?php echo htmlspecialchars($r['title']); ?></strong></td>
          <td><?php echo htmlspecialchars($r['category']); ?></td>
          <td><?php echo htmlspecialchars($r['uploader_name']); ?></td>
          <td style="font-size:11px;opacity:.6;"><?php echo htmlspecialchars($r['uploader_email']); ?></td>
          <td class="log-reason"><?php echo htmlspecialchars($r['reject_reason'] ?? '—'); ?></td>
          <td><span class="badge rejected">Rejected</span></td>
          <td>
            <div class="actions">
              <a class="btn-approve" href="admin_action.php?action=approve&id=<?php echo $r['id']; ?>">♻️ Re-Approve</a>
            </div>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="9"><div class="no-data">No rejected content.</div></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- USERS TAB -->
<div class="section" id="sec-users">
  <div class="section-title">👥 Registered Users</div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Name</th><th>Email</th><th>Verified</th>
          <th>Total</th><th>Pending</th><th>Approved</th><th>Rejected</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if($users && $users->num_rows > 0): $i=1; while($r=$users->fetch_assoc()):
        $uid = $r['id'];
        $up = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE creator_id=$uid AND status='pending'")->fetch_assoc()['c'];
        $ua = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE creator_id=$uid AND status='approved'")->fetch_assoc()['c'];
        $ur = $conn->query("SELECT COUNT(*) AS c FROM contents WHERE creator_id=$uid AND status='rejected'")->fetch_assoc()['c'];
      ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><?php echo htmlspecialchars($r['name']); ?></td>
          <td><?php echo htmlspecialchars($r['email']); ?></td>
          <td><?php echo $r['is_verified'] ? '<span class="badge approved">✅ Yes</span>' : '<span class="badge pending">⏳ No</span>'; ?></td>
          <td style="text-align:center;"><?php echo $r['uploads']; ?></td>
          <td style="text-align:center;color:#f0c040;"><?php echo $up; ?></td>
          <td style="text-align:center;color:#00ffa6;"><?php echo $ua; ?></td>
          <td style="text-align:center;color:#ff416c;"><?php echo $ur; ?></td>
          <td>
            <a class="btn-remove" href="admin_action.php?action=delete_user&id=<?php echo $r['id']; ?>"
               onclick="return confirm('Delete this user and all their content permanently?')">🗑 Delete</a>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="9"><div class="no-data">No users found.</div></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ADMIN LOGS TAB -->
<div class="section" id="sec-logs">
  <div class="section-title">📋 Admin Action Logs — Stored in admin_logs Table (Separate from Users)</div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Content Title</th><th>Uploader Name</th>
          <th>Uploader Email</th><th>Action</th><th>Reason</th><th>Date & Time</th>
        </tr>
      </thead>
      <tbody>
      <?php if($logs && $logs->num_rows > 0): $i=1; while($r=$logs->fetch_assoc()): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><strong><?php echo htmlspecialchars($r['content_title']); ?></strong></td>
          <td><?php echo htmlspecialchars($r['user_name']); ?></td>
          <td style="font-size:11px;opacity:.65;"><?php echo htmlspecialchars($r['user_email']); ?></td>
          <td>
            <?php
            $a = $r['action'];
            $labels = ['approved'=>'✅ Approved','rejected'=>'❌ Rejected','deleted'=>'🗑 Deleted'];
            $cls    = ['approved'=>'log-action-approved','rejected'=>'log-action-rejected','deleted'=>'log-action-deleted'];
            echo "<span class='{$cls[$a]}'>{$labels[$a]}</span>";
            ?>
          </td>
          <td class="log-reason"><?php echo htmlspecialchars($r['reason'] ?? '—'); ?></td>
          <td class="log-time"><?php echo date('d M Y, h:i A', strtotime($r['acted_at'])); ?></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="7"><div class="no-data">📋 No admin actions yet. Approve or reject content to see logs here.</div></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- REJECT MODAL -->
<div class="modal-bg" id="rejectModal">
  <div class="modal">
    <h3>❌ Reject Content</h3>
    <p>Enter reason — it will be emailed to uploader and saved in Admin Logs table.</p>
    <form action="admin_action.php" method="POST">
      <input type="hidden" name="action"        value="reject">
      <input type="hidden" name="id"            id="r_id">
      <input type="hidden" name="user_email"    id="r_email">
      <input type="hidden" name="content_title" id="r_title">
      <input type="hidden" name="user_name"     id="r_name">
      <textarea name="reason" placeholder="e.g. Content violates community guidelines..." required></textarea>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeReject()">Cancel</button>
        <button type="submit" class="btn-do-reject">❌ Reject & Send Email</button>
      </div>
    </form>
  </div>
</div>

<script>
function showTab(tab) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.getElementById('sec-' + tab).classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
}
function openReject(id, email, title, name) {
  document.getElementById('r_id').value    = id;
  document.getElementById('r_email').value = email;
  document.getElementById('r_title').value = title;
  document.getElementById('r_name').value  = name;
  document.getElementById('rejectModal').classList.add('open');
}
function closeReject() {
  document.getElementById('rejectModal').classList.remove('open');
}
document.getElementById('rejectModal').addEventListener('click', function(e){
  if(e.target === this) closeReject();
});
</script>
</body>
</html>