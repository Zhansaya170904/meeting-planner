<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Егер тапсырма ID-і көрсетілмеген болса
if (!isset($_GET['id'])) {
    header("Location: tasks.php");
    exit;
}

$task_id = $_GET['id'];

// Тапсырманы өңдеу үшін мәліметтерді алу
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

// Егер тапсырма табылмаса
if (!$task) {
    header("Location: tasks.php");
    exit;
}

// Тапсырманы өңдеуге арналған форма жіберу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['due_date'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    // Енгізу өрістерін тексеру
    if (empty($title) || empty($description) || empty($due_date)) {
        $error = "Барлық өрістерді толтырыңыз.";
    } else {
        $current_date = date("Y-m-d");
        if ($due_date < $current_date) {
            $error = "Аяқталу мерзімі өткен күн болмауы керек.";
        } else {
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$title, $description, $due_date, $task_id, $user_id])) {
                header("Location: tasks.php"); // Сәтті өңделгеннен кейін бағыттау
                exit;
            } else {
                $error = "Тапсырманы жаңарту кезінде қате болды.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тапсырманы өңдеу</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .input-group input, .input-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .success {
            color: #28a745;
            margin-bottom: 15px;
        }
        .back-btn {
            background-color: #6c757d;
            margin-top: 10px;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Тапсырманы өңдеу</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label for="title">Тапсырманың атауы</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
        </div>
        <div class="input-group">
            <label for="description">Тапсырманың сипаттамасы</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>
        <div class="input-group">
            <label for="due_date">Аяқталу мерзімі</label>
            <input type="date" name="due_date" id="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
        </div>
        <button type="submit" class="btn">Өзгерістерді сақтау</button>
    </form>

    <a href="tasks.php" class="btn back-btn">Тапсырмаларға қайту</a>
</div>
</body>
</html>
