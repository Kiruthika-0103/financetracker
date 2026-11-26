<?php
require_once __DIR__ . '/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $u = $stmt->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id']; $_SESSION['user_name'] = $u['name'];
        header('Location: index.php'); exit;
    }
    $error = 'Username or password is incorrect.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-bg"><main class="auth-card"><div class="brand-mark">₹</div><h1>Our Finance Tracker</h1><p>Sign in to view your shared finances.</p><?php if($error): ?><div class="notice error"><?=e($error)?></div><?php endif; ?><form method="post"><label>Username<input name="username" required autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button class="btn">Login</button></form></main></body></html>
