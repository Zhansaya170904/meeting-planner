<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Егер пайдаланушы авторизацияланбаған болса, оны login-ге бағыттаймыз
    exit;
}

require 'db.php';

$meeting_id = $_GET['id']; // URL-ден кездесудің ID алу

// Пайдаланушының кездесуге қатысушы екенін тексеру
$stmt = $pdo->prepare("SELECT * FROM meeting_participants WHERE meeting_id = ? AND user_id = ?");
$stmt->execute([$meeting_id, $_SESSION['user_id']]);
$participant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$participant) {
    echo "<div class='alert alert-danger'>Сіз осы кездесудің қатысушысы емессіз.</div>";
    exit;
}

// Осы кездесу үшін барлық протоколдарды алу
$stmt = $pdo->prepare("SELECT * FROM minutes WHERE meeting_id = ?");
$stmt->execute([$meeting_id]);
$minutes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];

    // Жаңа протоколды қосу
    $stmt = $pdo->prepare("INSERT INTO minutes (meeting_id, content) VALUES (?, ?)");
    if ($stmt->execute([$meeting_id, $content])) {
        echo "<div class='alert alert-success'>Протокол сәтті қосылды! <a href='minutes.php?id=$meeting_id'>Протоколдарға өту</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Протоколды қосу кезінде қате болды.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кездесу протоколдары</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Кездесу протоколдары</h2>

    <?php if (count($minutes) > 0): ?>
        <ul class="list-group mb-4">
            <?php foreach ($minutes as $minute): ?>
                <li class="list-group-item">
                    <strong>Күні:</strong> <?php echo htmlspecialchars($minute['created_at']); ?><br>
                    <strong>Протокол:</strong> <?php echo nl2br(htmlspecialchars($minute['content'])); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">Бұл кездесу үшін протоколдар жоқ.</div>
    <?php endif; ?>

    <h3>Жаңа протокол қосу</h3>
    <form method="POST" class="bg-light p-4 rounded shadow">
        <div class="mb-3">
            <label for="content" class="form-label">Протокол:</label>
            <textarea id="content" name="content" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Қосу</button>
    </form>

    <div class="mt-3 text-center">
        <a href="index.php" class="btn btn-secondary">Бас тарту</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
