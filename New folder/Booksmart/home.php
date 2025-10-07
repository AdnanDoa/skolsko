<?php
session_start();

// Debug mode: visit home.php?debug=1 after attempting login to see session/cookie state
$debug = (isset($_GET['debug']) && $_GET['debug'] === '1');

if (!isset($_SESSION['user_id'])) {
    if ($debug) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "home.php debug\n";
        echo "=================\n";
        echo "Session status: " . session_status() . "\n";
    echo "Session ID: " . session_id() . "\n";
    echo "\n";
        echo "\$_SESSION dump:\n";
        var_export($_SESSION);
        echo "\n\n\$_COOKIE dump:\n";
        var_export($_COOKIE);
        echo "\n\nHeaders sent: " . (headers_sent() ? 'yes' : 'no') . "\n";
        exit;
    }

    header('Location: login.html');
    exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Home</title></head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
<p>You are logged in. Your user ID is <?php echo (int)$_SESSION['user_id']; ?>.</p>
<?php if (!empty($_SESSION['role'])): ?>
    <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>
<?php endif; ?>
<?php if (!empty($_SESSION['subscription_type'])): ?>
    <p>Subscription: <?php echo htmlspecialchars($_SESSION['subscription_type']); ?></p>
<?php endif; ?>
<p><a href="logout.php">Logout</a></p>
</body>
</html>
