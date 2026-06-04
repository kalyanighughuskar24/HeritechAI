<?php
session_start();
require_once "../config.php";

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_panel.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND is_verified=1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($user['role'] === 'admin') {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role']       = $user['role'];
                header("Location: admin_panel.php");
                exit();
            } else {
                $error = "Access denied. Admin accounts only.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | AI Community</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #080c14;
    --surface: #0e1420;
    --border: rgba(255,255,255,0.07);
    --accent: #4f8cff;
    --accent-glow: rgba(79,140,255,0.25);
    --accent2: #00e5b0;
    --text: #eef2ff;
    --muted: rgba(238,242,255,0.38);
    --error: #ff4f6a;
    --error-bg: rgba(255,79,106,0.08);
  }

  *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: var(--text);
  }

  /* Ambient background */
  .bg-glow {
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
  }
  .bg-glow::before {
    content: '';
    position: absolute;
    top: -20%; left: 50%; transform: translateX(-50%);
    width: 700px; height: 500px;
    background: radial-gradient(ellipse, rgba(79,140,255,0.12) 0%, transparent 70%);
    filter: blur(40px);
  }
  .bg-glow::after {
    content: '';
    position: absolute;
    bottom: -10%; right: 10%;
    width: 400px; height: 400px;
    background: radial-gradient(ellipse, rgba(0,229,176,0.08) 0%, transparent 70%);
    filter: blur(60px);
  }

  /* Subtle grid */
  .bg-grid {
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image:
      linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size: 48px 48px;
    mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
  }

  /* Card */
  .card {
    position: relative; z-index: 1;
    width: 420px; max-width: calc(100vw - 32px);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 48px 40px 40px;
    box-shadow:
      0 0 0 1px rgba(255,255,255,0.04) inset,
      0 40px 80px rgba(0,0,0,0.5),
      0 0 60px rgba(79,140,255,0.06);
    animation: rise 0.55s cubic-bezier(0.22,1,0.36,1) both;
  }
  @keyframes rise {
    from { opacity:0; transform: translateY(28px) scale(0.98); }
    to   { opacity:1; transform: translateY(0) scale(1); }
  }

  /* Top accent line */
  .card::before {
    content: '';
    position: absolute; top: 0; left: 50%; transform: translateX(-50%);
    width: 60%; height: 1px;
    background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), transparent);
    border-radius: 999px;
  }

  /* Header */
  .header { text-align: center; margin-bottom: 36px; }

  .icon-wrap {
    display: inline-flex; align-items: center; justify-content: center;
    width: 56px; height: 56px;
    background: linear-gradient(135deg, rgba(79,140,255,0.15), rgba(0,229,176,0.1));
    border: 1px solid rgba(79,140,255,0.25);
    border-radius: 16px;
    margin-bottom: 20px;
    font-size: 24px;
  }

  .header h1 {
    font-family: 'Syne', sans-serif;
    font-size: 22px; font-weight: 800;
    letter-spacing: -0.3px;
    color: var(--text);
  }
  .header h1 span { color: var(--accent); }

  .header p {
    font-size: 13px; color: var(--muted);
    margin-top: 6px; font-weight: 300;
    letter-spacing: 0.2px;
  }

  .badge {
    display: inline-flex; align-items: center; gap: 5px;
    margin-top: 12px;
    padding: 4px 12px;
    border-radius: 999px;
    background: rgba(79,140,255,0.1);
    border: 1px solid rgba(79,140,255,0.2);
    font-size: 11px; font-weight: 600;
    color: var(--accent);
    letter-spacing: 0.8px;
    text-transform: uppercase;
  }
  .badge::before { content: ''; width:6px; height:6px; border-radius:50%; background:var(--accent); animation: pulse 2s infinite; }
  @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

  /* Error */
  .error-box {
    display: flex; align-items: center; gap: 10px;
    background: var(--error-bg);
    border: 1px solid rgba(255,79,106,0.2);
    color: var(--error);
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 24px;
    animation: shake 0.35s ease;
  }
  @keyframes shake {
    0%,100%{transform:translateX(0);} 25%{transform:translateX(-4px);} 75%{transform:translateX(4px);}
  }

  /* Form fields */
  .field { margin-bottom: 16px; }

  label {
    display: block;
    font-size: 12px; font-weight: 500;
    color: var(--muted);
    letter-spacing: 0.4px;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  .input-wrap {
    position: relative;
  }
  .input-wrap svg {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    width: 16px; height: 16px; opacity: 0.4; pointer-events: none;
    transition: opacity 0.2s;
  }
  .input-wrap:focus-within svg { opacity: 0.9; color: var(--accent); }

  input[type=email], input[type=password] {
    width: 100%;
    padding: 13px 14px 13px 42px;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--border);
    border-radius: 10px;
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
  }
  input[type=email]::placeholder, input[type=password]::placeholder { color: var(--muted); }
  input[type=email]:focus, input[type=password]:focus {
    border-color: rgba(79,140,255,0.5);
    background: rgba(79,140,255,0.05);
    box-shadow: 0 0 0 3px rgba(79,140,255,0.1);
  }

  /* Submit */
  .btn {
    width: 100%; margin-top: 8px;
    padding: 14px;
    border: none; border-radius: 12px;
    background: linear-gradient(135deg, #3b7bff, #4f8cff);
    color: #fff;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px; font-weight: 600;
    cursor: pointer;
    letter-spacing: 0.2px;
    position: relative; overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 24px rgba(79,140,255,0.3);
  }
  .btn::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, transparent 60%);
  }
  .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 32px rgba(79,140,255,0.45);
  }
  .btn:active { transform: translateY(0); }

  /* Footer */
  .footer {
    text-align: center; margin-top: 24px;
    font-size: 13px; color: var(--muted);
  }
  .footer a {
    color: var(--accent); text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
  }
  .footer a:hover { color: var(--accent2); }

  .divider {
    height: 1px; background: var(--border);
    margin: 28px 0 24px;
  }
</style>
</head>
<body>

<div class="bg-glow"></div>
<div class="bg-grid"></div>

<div class="card">
  <div class="header">
    <div class="icon-wrap">🤖</div>
    <h1>HeritechAI</h1>
    <p>Restricted access — administrators only</p>
    <div class="badge">Admin Portal</div>
  </div>

  <?php if ($error): ?>
  <div class="error-box">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <?php echo htmlspecialchars($error); ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="field">
      <label>Email Address</label>
      <div class="input-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        <input type="email" name="email" placeholder="Admin Email" required autocomplete="email">
      </div>
    </div>

    <div class="field">
      <label>Password</label>
      <div class="input-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <input type="password" name="password" placeholder="••••••••••" required>
      </div>
    </div>

    <button type="submit" class="btn">Sign in to Admin Panel</button>
  </form>

  <div class="divider"></div>

  <div class="footer">
    <a href="../index.php">← Back to site</a>
  </div>
</div>

</body>
</html>