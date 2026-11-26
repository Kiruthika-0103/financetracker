<?php
require_once __DIR__ . '/functions.php'; require_login();
$rows = db()->query("SELECT DATE_FORMAT(entry_date,'%Y-%m') month, entry_type, SUM(amount) total FROM entries GROUP BY month,entry_type ORDER BY month DESC")->fetchAll();
$months=[];
foreach($rows as $r) $months[$r['month']][$r['entry_type']] = (float)$r['total'];
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Reports</title><link rel="stylesheet" href="assets/style.css"></head><body><header class="topbar"><a class="brand" href="index.php">₹ Our Finance</a><a href="logout.php">Logout</a></header><main class="container"><div class="heading"><div><p class="eyebrow">TRACK YOUR PROGRESS</p><h1>Monthly reports</h1></div><a class="btn small" href="index.php">Dashboard</a></div><section class="panel"><div class="table-wrap"><table><thead><tr><th>Month</th><th>Income</th><th>Expenses</th><th>Savings</th><th>Net</th></tr></thead><tbody><?php foreach($months as $m=>$v): $i=$v['income']??0;$x=$v['expense']??0;$s=$v['saving']??0; ?><tr><td><?=e($m)?></td><td><?=money($i)?></td><td><?=money($x)?></td><td><?=money($s)?></td><td><?=money($i-$x)?></td></tr><?php endforeach; ?><?php if(!$months): ?><tr><td colspan="5">No report data yet.</td></tr><?php endif; ?></tbody></table></div></section></main></body></html>
