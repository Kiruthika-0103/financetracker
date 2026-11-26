<?php
require_once __DIR__ . '/functions.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
verify_csrf();
$type = $_POST['entry_type'] ?? '';
if (!in_array($type, ['income','expense','saving'], true)) exit('Invalid entry type.');
$amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);
$date = $_POST['entry_date'] ?? '';
$d = DateTime::createFromFormat('Y-m-d', $date);
if (!$amount || $amount <= 0 || !$d || $d->format('Y-m-d') !== $date) exit('Please enter a valid date and amount.');
$stmt = db()->prepare("INSERT INTO entries(user_id,entry_date,entry_type,app_name,category,amount,rides,note) VALUES(?,?,?,?,?,?,?,?)");
$stmt->execute([$_SESSION['user_id'],$date,$type,trim($_POST['app_name'] ?? ''),trim($_POST['category'] ?? ''),$amount,max(0,(int)($_POST['rides'] ?? 0)),trim($_POST['note'] ?? '')]);
header('Location: index.php#add-entry'); exit;
