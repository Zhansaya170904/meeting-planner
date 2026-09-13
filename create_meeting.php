<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO meetings (title, date, time, location, organizer_id) 
                           VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $date, $time, $location, $user_id])) {
        $meeting_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO meeting_participants (meeting_id, user_id) 
                               VALUES (?, ?)");
        $stmt->execute([$meeting_id, $user_id]);

        echo "<div class='alert alert-success'>Кездесу сәтті құрылды! <a href='index.php'>Негізгі бетке өту</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Кездесу құруда қате кетті.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кездесу құру</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Жаңа кездесу құру</h2>
    
    <form method="POST" class="bg-light p-4 rounded shadow">
        <div class="mb-3">
            <label for="title" class="form-label">Кездесу атауы:</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Күні:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="time" class="form-label">Уақыты:</label>
            <input type="time" id="time" name="time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Орны:</label>
            <input type="text" id="location" name="location" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Кездесуді құру</button>
    </form>

    <div class="mt-3 text-center">
        <a href="index.php" class="btn btn-secondary">Бас тарту</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
