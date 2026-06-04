<?php
session_start();
require_once "../config.php";
require_once "../PHPMailer/src/PHPMailer.php";
require_once "../PHPMailer/src/SMTP.php";
require_once "../PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ── MAIL HELPER ───────────────────────────────────────────
function sendMail($to, $subject, $body) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'anirudhchaudhari581@gmail.com';
        $mail->Password   = 'hnuwjllzfvsydlqd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('anirudhchaudhari581@gmail.com', 'AI Community Admin');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $e->getMessage());
        return false;
    }
}

// ── LOG HELPER — saves to admin_logs table (NOT users table) ──
function logAction($conn, $content_id, $content_title, $user_name, $user_email, $action, $reason = null) {
    $stmt = $conn->prepare("
        INSERT INTO admin_logs (content_id, content_title, user_name, user_email, action, reason, acted_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    if ($stmt) {
        $stmt->bind_param("isssss", $content_id, $content_title, $user_name, $user_email, $action, $reason);
        $stmt->execute();
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ══════════════════════════════════════════
// APPROVE
// ══════════════════════════════════════════
if ($action === 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // ✅ FIXED: uses "contents" table
    $stmt = $conn->prepare("UPDATE contents SET status='approved' WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Get content + user info
        $res  = $conn->query("SELECT c.title, u.name, u.email FROM contents c JOIN users u ON c.creator_id=u.id WHERE c.id=$id");
        $data = $res->fetch_assoc();

        if ($data) {
            // ✅ Save to admin_logs table (NOT users table)
            logAction($conn, $id, $data['title'], $data['name'], $data['email'], 'approved', 'Content approved and published.');

            // Send approval email
            $emailBody = "
            <div style='font-family:Poppins,sans-serif;background:#0f2027;color:white;padding:30px;border-radius:14px;max-width:520px;margin:auto;'>
              <h2 style='color:#00ffa6;'>🎉 Your Content is Approved!</h2>
              <p>Hello <strong>{$data['name']}</strong>,</p>
              <p style='margin:12px 0;'>Your content <strong>\"{$data['title']}\"</strong> has been <span style='color:#00ffa6;font-weight:bold;'>approved</span> and is now live on the AI Community homepage! 🚀</p>
              <p>Thank you for contributing to our community!</p>
              <hr style='border-color:rgba(255,255,255,0.1);margin:20px 0;'>
              <p style='font-size:12px;opacity:.5;'>AI Community Admin Team</p>
            </div>";
            sendMail($data['email'], "✅ Content Approved — AI Community", $emailBody);
        }

        header("Location: admin_panel.php?msg=Content+approved+and+logged+in+admin_logs!");
    } else {
        header("Location: admin_panel.php?err=Failed+to+approve.");
    }
    exit();
}

// ══════════════════════════════════════════
// REJECT (POST)
// ══════════════════════════════════════════
if ($action === 'reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int)$_POST['id'];
    $reason        = $conn->real_escape_string(trim($_POST['reason']));
    $user_email    = trim($_POST['user_email']);
    $user_name     = trim($_POST['user_name']);
    $content_title = trim($_POST['content_title']);

    // ✅ FIXED: uses "contents" table
    $stmt = $conn->prepare("UPDATE contents SET status='rejected', reject_reason=? WHERE id=?");
    $stmt->bind_param("si", $reason, $id);

    if ($stmt->execute()) {
        // ✅ Save to admin_logs table (NOT users table)
        logAction($conn, $id, $content_title, $user_name, $user_email, 'rejected', $reason);

        // Send rejection email
        $emailBody = "
        <div style='font-family:Poppins,sans-serif;background:#0f2027;color:white;padding:30px;border-radius:14px;max-width:520px;margin:auto;'>
          <h2 style='color:#ff416c;'>❌ Content Rejected</h2>
          <p>Hello <strong>{$user_name}</strong>,</p>
          <p style='margin:12px 0;'>Your content <strong>\"{$content_title}\"</strong> has been <span style='color:#ff416c;font-weight:bold;'>rejected</span> by our admin team.</p>
          <div style='background:rgba(255,65,108,0.1);border:1px solid rgba(255,65,108,0.4);border-radius:10px;padding:16px;margin:16px 0;'>
            <strong style='color:#ff416c;'>Reason:</strong><br><br>{$reason}
          </div>
          <p>Please review our guidelines and feel free to resubmit with corrections.</p>
          <hr style='border-color:rgba(255,255,255,0.1);margin:20px 0;'>
          <p style='font-size:12px;opacity:.5;'>AI Community Admin Team</p>
        </div>";

        sendMail($user_email, "❌ Content Rejected — AI Community", $emailBody);

        header("Location: admin_panel.php?msg=Content+rejected,+email+sent,+saved+in+admin_logs!");
    } else {
        header("Location: admin_panel.php?err=Failed+to+reject.");
    }
    exit();
}

// ══════════════════════════════════════════
// REMOVE (approved → pending)
// ══════════════════════════════════════════
if ($action === 'remove' && isset($_GET['id'])) {
    $id   = (int)$_GET['id'];
    // ✅ FIXED: uses "contents" table
    $stmt = $conn->prepare("UPDATE contents SET status='pending' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_panel.php?msg=Content+moved+back+to+pending.");
    exit();
}

// ══════════════════════════════════════════
// DELETE USER
// ══════════════════════════════════════════
if ($action === 'delete_user' && isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $res = $conn->query("SELECT name, email FROM users WHERE id=$id");
    $user = $res->fetch_assoc();

    if ($user) {
        // ✅ FIXED: uses "contents" table
        $cres = $conn->query("SELECT id, title FROM contents WHERE creator_id=$id");
        while ($c = $cres->fetch_assoc()) {
            logAction($conn, $c['id'], $c['title'], $user['name'], $user['email'], 'deleted', 'User deleted by admin.');
        }
        $conn->query("DELETE FROM contents WHERE creator_id=$id");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_panel.php?msg=User+deleted+and+logged.");
    } else {
        header("Location: admin_panel.php?err=Failed+to+delete+user.");
    }
    exit();
}

header("Location: admin_panel.php?err=Invalid+action.");
exit();
?>