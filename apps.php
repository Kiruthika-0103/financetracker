<?php
require_once __DIR__ . '/functions.php'; require_login();
$pdo = db(); $error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add') {
            $name = trim($_POST['app_name'] ?? '');
            if ($name === '' || mb_strlen($name) > 60) throw new Exception('Enter an app name (maximum 60 characters).');
            $pdo->prepare("INSERT INTO apps(app_name,is_active) VALUES(?,1)")->execute([$name]);
            $success = 'App added.';
        } elseif ($action === 'rename') {
            $name = trim($_POST['app_name'] ?? '');
            if ($name === '' || mb_strlen($name) > 60) throw new Exception('Enter a valid app name.');
            $pdo->prepare("UPDATE apps SET app_name=? WHERE id=?")->execute([$name,(int)$_POST['id']]);
            $success = 'App name updated. Past entries keep their recorded app name.';
        } elseif ($action === 'toggle') {
            $pdo->prepare("UPDATE apps SET is_active=IF(is_active=1,0,1) WHERE id=?")->execute([(int)$_POST['id']]);
            $success = 'App status updated.';
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM apps WHERE id=?")->execute([(int)$_POST['id']]);
            $success = 'App removed. Previously saved entries remain.';
        }
    } catch (PDOException $ex) { $error = 'That app name already exists. Choose a different name.'; }
      catch (Throwable $ex) { $error = $ex->getMessage(); }
}
$apps = $pdo->query("SELECT * FROM apps ORDER BY is_active DESC,app_name")->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage Apps</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="topbar"><a class="brand" href="index.php">₹ Our Finance</a><a href="logout.php">Logout</a></header>
<main class="container"><div class="heading"><div><p class="eyebrow">CUSTOMIZE YOUR TRACKER</p><h1>Manage Apps</h1><p class="muted">Add, rename, pause or remove delivery and ride apps.</p></div><a class="btn secondary small" href="index.php">Dashboard</a></div>
<?php if($success): ?><div class="notice success"><?=e($success)?></div><?php endif; ?><?php if($error): ?><div class="notice error"><?=e($error)?></div><?php endif; ?>
<section class="panel"><h2>Add a new app</h2><form method="post" class="add-app-form"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="add"><label>App name<input name="app_name" maxlength="60" placeholder="e.g. New delivery app" required></label><button class="btn">+ Add App</button></form></section>
<section class="panel"><h2>Your apps</h2><div class="app-list"><?php foreach($apps as $app): ?><div class="app-row">
<form method="post" class="rename-form"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="rename"><input type="hidden" name="id" value="<?=(int)$app['id']?>"><input name="app_name" value="<?=e($app['app_name'])?>" maxlength="60" required aria-label="App name"><button class="btn secondary small">Save name</button></form>
<span class="pill <?=$app['is_active']?'income':'expense'?>"><?=$app['is_active']?'Active':'Inactive'?></span>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=(int)$app['id']?>"><button class="btn secondary small"><?=$app['is_active']?'Hide':'Activate'?></button></form>
<form method="post" onsubmit="return confirm('Remove this app from the list? Past records will remain.');"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$app['id']?>"><button class="btn danger small">Delete</button></form>
</div><?php endforeach; ?></div><p class="muted note">Use Hide if you may use an app again. Delete removes it from the selectable list; saved entries remain in reports.</p></section></main></body></html>
