<?php
require_once __DIR__ . '/functions.php';
require_login();

$pdo = db();
$userId = (int)$_SESSION['user_id'];

$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';

    if (!$id || $id <= 0) {
        exit('Invalid entry ID');
    }

    if ($action === 'restore') {
        $stmt = $pdo->prepare("
            UPDATE entries
            SET deleted_at = NULL
            WHERE id = ?
              AND user_id = ?
              AND deleted_at IS NOT NULL
        ");
        $stmt->execute([$id, $userId]);

        header('Location: recently_deleted.php?status=restored');
        exit;
    }

    if ($action === 'permanent_delete') {
        $stmt = $pdo->prepare("
            DELETE FROM entries
            WHERE id = ?
              AND user_id = ?
              AND deleted_at IS NOT NULL
        ");
        $stmt->execute([$id, $userId]);

        header('Location: recently_deleted.php?status=deleted');
        exit;
    }
}

// Fetch deleted entries
$stmt = $pdo->prepare("
    SELECT *
    FROM entries
    WHERE user_id = ?
      AND deleted_at IS NOT NULL
    ORDER BY deleted_at DESC
");
$stmt->execute([$userId]);

$deletedEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recently Deleted</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header class="topbar">
    <a class="brand" href="index.php">₹ Our Finance</a>
    <a href="index.php">Back to Dashboard</a>
</header>

<main class="container">

    <div class="heading">
        <div>
            <p class="eyebrow">RECOVER YOUR ENTRIES</p>
            <h1>Recently Deleted</h1>
            <p class="muted">Restore or permanently delete your entries.</p>
        </div>
    </div>

    <?php if (($_GET['status'] ?? '') === 'restored'): ?>
        <p class="muted">Entry restored successfully! Check your Dashboard.</p>
    <?php elseif (($_GET['status'] ?? '') === 'deleted'): ?>
        <p class="muted">Entry permanently deleted.</p>
    <?php endif; ?>

    <section class="panel">

        <?php if (!empty($deletedEntries)): ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>App / Category</th>
                            <th>Amount</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($deletedEntries as $r): ?>
                        <tr>
                            <td><?= e($r['entry_date']) ?></td>

                            <td>
                                <span class="pill <?= e($r['entry_type']) ?>">
                                    <?= e(ucfirst($r['entry_type'])) ?>
                                </span>
                            </td>

                            <td>
                                <?= e($r['app_name'] ?: $r['category'] ?: '—') ?>
                            </td>

                            <td><?= money($r['amount']) ?></td>

                            <td><?= e($r['deleted_at']) ?></td>

                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf"
                                           value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id"
                                           value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="action"
                                           value="restore">

                                    <button type="submit" class="btn small">
                                        Restore
                                    </button>
                                </form>

                                <form method="POST" style="display:inline;"
                                      onsubmit="return confirm('Delete permanently? This cannot be undone.');">

                                    <input type="hidden" name="csrf"
                                           value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id"
                                           value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="action"
                                           value="permanent_delete">

                                    <button type="submit" class="btn small">
                                        Delete Permanently
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <p class="muted">No recently deleted entries.</p>

        <?php endif; ?>

    </section>

</main>

</body>
</html>