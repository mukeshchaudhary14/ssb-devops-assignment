<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, status) VALUES (?, 'Pending')");
        $stmt->execute([$title]);
    }
    header("Location: index.php");
    exit;
}

if (isset($_GET['complete_id'])) {
    $id = (int)$_GET['complete_id'];
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSB Digital - Web Operations Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f6f8; }
        .container { max-width: 700px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        form { margin-bottom: 20px; display: flex; gap: 10px; }
        input[type="text"] { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        button { padding: 10px 15px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8fafc; color: #334155; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-completed { background: #dcfce7; color: #166534; }
        a.action-btn { color: #2563eb; text-decoration: none; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h2>SSB Digital - Internal Operations Portal</h2>

    <!-- Create Form -->
    <form method="POST" action="index.php">
        <input type="text" name="title" placeholder="Enter new task description..." required>
        <button type="submit">Add Task</button>
    </form>

    <!-- Task List (Read) -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Task Title</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['id']) ?></td>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td>
                    <span class="badge <?= $task['status'] === 'Completed' ? 'badge-completed' : 'badge-pending' ?>">
                        <?= htmlspecialchars($task['status']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($task['status'] !== 'Completed'): ?>
                        <a href="index.php?complete_id=<?= $task['id'] ?>" class="action-btn">Mark Done</a>
                    <?php else: ?>
                        <span style="color: gray;">✓ Done</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
