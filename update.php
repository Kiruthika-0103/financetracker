<?php
require_once __DIR__ . '/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Verify the CSRF token
verify_csrf();

$pdo = db();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$userId = (int)$_SESSION['user_id'];

$date = $_POST['entry_date'] ?? '';
$type = $_POST['entry_type'] ?? '';
$app = trim($_POST['app_name'] ?? '');
$category = trim($_POST['category'] ?? '');
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
$rides = filter_input(INPUT_POST, 'rides', FILTER_VALIDATE_INT);
$note = trim($_POST['note'] ?? '');

$allowedTypes = ['income', 'expense', 'saving'];

$dateObj = DateTime::createFromFormat('!Y-m-d', $date);

if (
    !$id ||
    !$dateObj ||
    $dateObj->format('Y-m-d') !== $date ||
    !in_array($type, $allowedTypes, true) ||
    $amount === false ||
    $amount === null ||
    $amount <= 0 ||
    $rides === false ||
    $rides === null ||
    $rides < 0 ||
    strlen($note) > 255
) {
    exit('Invalid entry details. Please go back and check.');
}

// Update only the logged-in user's entry
$stmt = $pdo->prepare("
    UPDATE entries
    SET entry_date = ?,
        entry_type = ?,
        app_name = ?,
        category = ?,
        amount = ?,
        rides = ?,
        note = ?
    WHERE id = ? AND user_id = ?
");

$stmt->execute([
    $date,
    $type,
    $app,
    $category,
    $amount,
    $rides,
    $note,
    $id,
    $userId
]);

header('Location: index.php');
exit;