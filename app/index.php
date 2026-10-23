<?php
require_once 'db.php';

// Create: नया टास्क जोड़ना
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, status) VALUES (?, 'Pending')");
        $stmt->execute([$title]);
    }
    header("Location: index.php");
    exit;
}

// Update: स्टेटस मार्क करना
if (isset($_GET['complete_id'])) {
    $id = (int)$_GET['complete_id'];
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// Read: लिस्ट फेच करना
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSB Digital - Web Operations Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f1f5f9; }
        .container { max-width: 750px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        h2 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; margin-top: 0; }
        form { margin: 20px 0; display: flex; gap: 10px; }
        input[type="text"] { flex: 1; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; }
        button { padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #475569; font-size: 14px; }
        .badge { padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-completed { background: #dcfce7; color: #15803d; }
        a.action-btn { color: #2563eb; text-decoration: none; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h2>SSB Digital - Internal Operations Portal <span style="font-size: 13px; color: #16a34a; font-weight: normal;">(v1.0) </span></h2>

    <!-- Create Form -->
    <form method="POST" action="index.php">
        <input type="text" name="title" placeholder="Enter new task description..." required>
        <button type="submit">+ Add Task</button>
    </form>

    <!-- Task List (Read) -->
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Task Title</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 100px;">Action</th>
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
                        <span style="color: #94a3b8;">✓ Done</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
