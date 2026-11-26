<?php
require_once __DIR__ . '/functions.php';
require_login();

$pdo = db();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$userId = (int)$_SESSION['user_id'];

if (!$id) {
    exit('Invalid entry ID.');
}

$stmt = $pdo->prepare(
    "SELECT * FROM entries WHERE id = ? AND user_id = ?"
);
$stmt->execute([$id, $userId]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entry) {
    exit('Entry not found.');
}

$apps = $pdo->query(
    "SELECT app_name FROM apps WHERE is_active = 1 ORDER BY app_name"
)->fetchAll(PDO::FETCH_COLUMN);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Entry</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="topbar">
    <a class="brand" href="index.php">₹ Our Finance</a>
    <a href="logout.php">Logout</a>
</header>

<main class="container">
    <section class="panel">
        <h2>Edit Entry</h2>

        <form action="update.php" method="POST" class="entry-form">

            <input type="hidden" name="csrf"
                   value="<?= e(csrf_token()) ?>">

            <input type="hidden" name="id"
                   value="<?= (int)$entry['id'] ?>">

            <label>Date
                <input type="date" name="entry_date"
                       value="<?= e($entry['entry_date']) ?>" required>
            </label>

            <label>Type
                <select name="entry_type" required>
                    <option value="income" <?= $entry['entry_type'] === 'income' ? 'selected' : '' ?>>Income</option>
                    <option value="expense" <?= $entry['entry_type'] === 'expense' ? 'selected' : '' ?>>Expense</option>
                    <option value="saving" <?= $entry['entry_type'] === 'saving' ? 'selected' : '' ?>>Saving</option>
                </select>
            </label>

            <label>App / category
                <select name="app_name">
                    <option value="">Select app</option>
                    <?php foreach ($apps as $app): ?>
                        <option value="<?= e($app) ?>"
                            <?= $entry['app_name'] === $app ? 'selected' : '' ?>>
                            <?= e($app) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Expense category
                <input type="text" name="category"
                       value="<?= e($entry['category'] ?? '') ?>">
            </label>

            <label>Amount (₹)
                <input type="number" name="amount"
                       min="0.01" step="0.01"
                       value="<?= e($entry['amount']) ?>" required>
            </label>

            <label>Rides / orders
                <input type="number" name="rides"
                       min="0" step="1"
                       value="<?= (int)$entry['rides'] ?>">
            </label>

            <label class="wide">Note
                <input type="text" name="note" maxlength="255"
                       value="<?= e($entry['note'] ?? '') ?>">
            </label>

            <button type="submit" class="btn wide">
                Update Entry
            </button>

            <a href="index.php" class="btn secondary wide">
                Cancel
            </a>

        </form>
    </section>
</main>

</body>
</html>