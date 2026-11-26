<?php
require_once __DIR__ . '/functions.php';
require_login();

$pdo = db();

$today = date('Y-m-d');
$monthStart = date('Y-m-01');

// Monthly totals
$sum = $pdo->prepare("
    SELECT entry_type, COALESCE(SUM(amount),0) total
    FROM entries
    WHERE entry_date BETWEEN ? AND ?
      AND deleted_at IS NULL
    GROUP BY entry_type
");
$sum->execute([$monthStart, $today]);

$totals = ['income'=>0, 'expense'=>0, 'saving'=>0];

foreach ($sum as $r) {
    $totals[$r['entry_type']] = (float)$r['total'];
}

// Car goal
$goalRow = $pdo->query("
    SELECT setting_value FROM settings
    WHERE setting_key='car_goal'
")->fetch();

$goal = (float)($goalRow['setting_value'] ?? 600000);

// Total savings
$saved = (float)$pdo->query("
    SELECT COALESCE(SUM(amount),0)
    FROM entries
    WHERE entry_type='saving'
      AND deleted_at IS NULL
")->fetchColumn();

// Recent entries
$recent = $pdo->query("
    SELECT e.*,u.name
    FROM entries e
    JOIN users u ON u.id=e.user_id
    WHERE e.deleted_at IS NULL
    ORDER BY entry_date DESC,id DESC
    LIMIT 12
")->fetchAll();

$apps = $pdo->query("
    SELECT app_name FROM apps
    WHERE is_active=1
    ORDER BY app_name
")->fetchAll(PDO::FETCH_COLUMN);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header class="topbar">
    <a class="brand" href="index.php">₹ Our Finance</a>
    <div>
        Hi, <?=e($_SESSION['user_name'])?> ·
        <a href="logout.php">Logout</a>
    </div>
</header>

<main class="container">

<div class="heading">
    <div>
        <p class="eyebrow">YOUR MONEY, IN ONE PLACE</p>
        <h1>Dashboard</h1>
        <p class="muted">Monthly overview · <?=date('F Y')?></p>
    </div>

    <div class="heading-actions">
        <a class="btn secondary small" href="apps.php">Manage Apps</a>
        <a class="btn secondary small" href="recently_deleted.php">Recently Deleted</a>
        <a class="btn small" href="#add-entry">+ Add entry</a>
    </div>
</div>

<section class="stats">

    <div class="stat income">
        <span>Income this month</span>
        <strong><?=money($totals['income'])?></strong>
    </div>

    <div class="stat expense">
        <span>Expenses this month</span>
        <strong><?=money($totals['expense'])?></strong>
    </div>

    <div class="stat saving">
        <span>Savings recorded this month</span>
        <strong><?=money($totals['saving'])?></strong>
    </div>

    <div class="stat">
        <span>Net after expenses</span>
        <strong><?=money($totals['income']-$totals['expense'])?></strong>
    </div>

</section>

<section class="panel goal-panel">
    <div>
        <p class="eyebrow">DREAM CAR FUND</p>
        <h2><?=money($saved)?> <small>of <?=money($goal)?></small></h2>
        <p class="muted"><?=money(max(0,$goal-$saved))?> left to reach your goal</p>
    </div>

    <div class="progress">
        <span style="width:<?=($goal > 0 ? min(100,($saved/$goal)*100) : 0)?>%"></span>
    </div>
</section>

<section class="panel" id="add-entry">
    <h2>Add daily entry</h2>

    <p class="muted">
        Enter the amount manually from the app's earnings screen.
        This tracker does not connect to delivery apps.
    </p>

    <form action="save.php" method="post" class="entry-form">

        <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">

        <label>Date
            <input type="date" name="entry_date" value="<?=$today?>" required>
        </label>

        <label>Type
            <select name="entry_type" id="type" required>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
                <option value="saving">Car / other saving</option>
            </select>
        </label>

        <label>App / category
            <select name="app_name">
                <?php foreach($apps as $a): ?>
                    <option><?=e($a)?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Expense category
            <input name="category" placeholder="Fuel, home, food...">
        </label>

        <label>Amount (₹)
            <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0.00">
        </label>

        <label>Rides / orders
            <input type="number" name="rides" min="0" step="1" value="0">
        </label>

        <label class="wide">Note
            <input name="note" maxlength="255" placeholder="Optional note">
        </label>

        <button class="btn wide">Save entry</button>

    </form>
</section>

<section class="panel">

    <div class="section-head">
        <h2>Recent entries</h2>
        <a href="reports.php">View reports →</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>App / category</th>
                    <th>Amount</th>
                    <th>Rides</th>
                    <th>Added by</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach($recent as $r): ?>

                <tr>
                    <td><?=e($r['entry_date'])?></td>

                    <td>
                        <span class="pill <?=e($r['entry_type'])?>">
                            <?=e(ucfirst($r['entry_type']))?>
                        </span>
                    </td>

                    <td>
                        <?=e($r['app_name'] ?: $r['category'] ?: '—')?>
                        <?= $r['note'] ? '<small class="subnote">'.e($r['note']).'</small>' : '' ?>
                    </td>

                    <td><?=money($r['amount'])?></td>
                    <td><?= (int)$r['rides'] ?></td>
                    <td><?=e($r['name'])?></td>

                    <td>
                        <a class="btn small" href="edit.php?id=<?= (int)$r['id'] ?>">Edit</a>

                        <form action="delete.php" method="POST"
                              style="display:inline;"
                              onsubmit="return confirm('Are you sure you want to delete this entry?');">

                            <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">

                            <button type="submit" class="btn small">Delete</button>
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>

            <?php if(!$recent): ?>
                <tr>
                    <td colspan="7" class="muted">
                        No entries yet. Add your first entry above.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</section>

<footer>
    Keep your login private. Back up your database regularly.
</footer>

</main>
</body>
</html>