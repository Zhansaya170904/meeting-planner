<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Задача қосу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['due_date'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    if (empty($title) || empty($description) || empty($due_date)) {
        $error = "Барлық өрістерді толтырыңыз.";
    } else {
        $current_date = date("Y-m-d");
        if ($due_date < $current_date) {
            $error = "Орындалу мерзімі өткен уақыт бола алмайды.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $title, $description, $due_date])) {
                $success = "Задача сәтті қосылды!";
                // Задачаларды жаңарту
                $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
                $stmt->execute([$user_id]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $error = "Задачаны қосуда қате болды.";
            }
        }
    }
}

// Задачаны жою
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
    header("Location: tasks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сіздің тапсырмаларыңыз</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #333;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f7f7f7;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px; /* Added spacing */
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
            margin-left: 5px; /* Space between edit and delete buttons */
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
        }
        .input-group input, .input-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .error {
            color: #dc3545;
        }
        .success {
            color: #28a745;
        }
        .back-btn {
            background-color: #6c757d;
            margin-top: 20px; /* Added spacing */
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Сіздің тапсырмаларыңыз</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <?php if (count($tasks) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Тапсырма</th>
                    <th>Сипаттама</th>
                    <th>Орындалу мерзімі</th>
                    <th>Әрекеттер</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($task['description'])); ?></td>
                        <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                        <td>
                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn">Өзгерту</a>
                            <a href="tasks.php?delete=<?php echo $task['id']; ?>" class="btn delete-btn" onclick="return confirm('Шынымен осы тапсырманы жойғыңыз келе ме?');">Жою</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Сіздің тапсырмаларыңыз жоқ.</p>
    <?php endif; ?>

    <h3>Жаңа тапсырма қосу</h3>
    <form method="POST">
        <div class="input-group">
            <label for="title">Тапсырманың атауы</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="input-group">
            <label for="description">Тапсырманың сипаттамасы</label>
            <textarea name="description" id="description" required></textarea>
        </div>
        <div class="input-group">
            <label for="due_date">Орындалу мерзімі</label>
            <input type="date" name="due_date" id="due_date" required>
        </div>
        <button type="submit" class="btn">Тапсырманы қосу</button>
    </form>

    <a href="index.php" class="btn back-btn">Негізгі бетке қайту</a>
</div>
</body>
</html>
