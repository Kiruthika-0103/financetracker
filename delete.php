<?php
require_once __DIR__ . '/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verify_csrf();

$pdo = db();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$userId = (int)$_SESSION['user_id'];

if (!$id || $id <= 0) {
    exit('Invalid entry ID.');
}

$stmt = $pdo->prepare("
    UPDATE entries
    SET deleted_at = NOW()
    WHERE id = ? AND user_id = ? AND deleted_at IS NULL
");

$stmt->execute([$id, $userId]);

header('Location: index.php');
exit;