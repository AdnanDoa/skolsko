<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
require_once __DIR__ . '/db_connect.php';
$uid = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT user_id, name, email, role, subscription_type, avatar_url, bio, created_at FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $uid);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
if (!$user) {
    echo "User not found";
    exit;
}
if (empty($user['avatar_url'])) $user['avatar_url'] = 'https://i.pravatar.cc/150?img=32';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profile - <?php echo htmlspecialchars($user['name']); ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header style="padding:15px;background:#4361ee;color:white;display:flex;align-items:center;justify-content:space-between;">
    <a href="home.html" style="color:white;text-decoration:none;font-weight:700;font-size:20px"><i class="fas fa-book"></i> Booksmart</a>
    <div>
        <a href="logout.php" style="color:white;text-decoration:none;margin-left:10px">Logout</a>
    </div>
</header>
<main style="max-width:900px;margin:40px auto;padding:0 20px;font-family:Arial,Helvetica,sans-serif;">
    <div style="display:flex;gap:30px;align-items:center;">
        <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar" style="width:150px;height:150px;border-radius:50%;object-fit:cover;border:4px solid #4361ee;">
        <div>
            <h1 style="margin:0"><?php echo htmlspecialchars($user['name']); ?></h1>
            <p style="color:#666;margin:6px 0"><?php echo htmlspecialchars($user['email']); ?></p>
            <p style="margin:6px 0">Role: <?php echo htmlspecialchars($user['role']); ?> — Subscription: <?php echo htmlspecialchars($user['subscription_type']); ?></p>
            <p style="margin:6px 0;color:#999;font-size:13px">Member since: <?php echo htmlspecialchars($user['created_at']); ?></p>
            <p><a href="#" id="editProfile">Edit profile</a></p>
        </div>
    </div>

    <section style="margin-top:30px;background:white;padding:20px;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,0.06);">
        <h2>About</h2>
        <p><?php echo nl2br(htmlspecialchars($user['bio'] ?? '')); ?></p>
    </section>
</main>

<!-- Link back to edit modal on home.html or directly to profile_update.php -->
<script>
document.getElementById('editProfile').addEventListener('click', function(e){
    e.preventDefault();
    window.location.href = 'home.html';
});
</script>
</body>
</html>
