<?php
session_start();

// Пайдаланушының авторизациясын тексеру
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Егер пайдаланушы авторизацияланбаған болса, оны login-ге бағыттаймыз
    exit;
}

require 'db.php';

// URL-ден кездесу ID алу
$meeting_id = $_GET['id']; 

// Кездесу туралы мәліметтерді алу
$stmt = $pdo->prepare("SELECT * FROM meetings WHERE id = ? AND (organizer_id = ? OR EXISTS (SELECT 1 FROM meeting_participants WHERE meeting_id = ? AND user_id = ?))");
$stmt->execute([$meeting_id, $_SESSION['user_id'], $meeting_id, $_SESSION['user_id']]);
$meeting = $stmt->fetch(PDO::FETCH_ASSOC);

// Кездесу табылмаса немесе пайдаланушы ұйымдастырушы/қатысушы емес болса
if (!$meeting) {
    echo "Кездесу табылмады немесе сізде осы кездесуге қолжетімділік жоқ.";
    exit;
}

// Кездесу қатысушыларын алу
$stmt = $pdo->prepare("SELECT u.name FROM meeting_participants mp JOIN users u ON mp.user_id = u.id WHERE mp.meeting_id = ?");
$stmt->execute([$meeting_id]);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Осы кездесуге арналған протоколдарды алу
$stmt = $pdo->prepare("SELECT * FROM minutes WHERE meeting_id = ?");
$stmt->execute([$meeting_id]);
$minutes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кездесу мәліметтері</title>
    <link rel="stylesheet" href="styles.css"> <!-- CSS қосу -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Кездесу мәліметтері: <?php echo htmlspecialchars($meeting['title']); ?></h1>
        </header>

        <section class="meeting-info card">
            <p><strong>Күні:</strong> <?php echo htmlspecialchars($meeting['date']); ?></p>
            <p><strong>Уақыты:</strong> <?php echo htmlspecialchars($meeting['time']); ?></p>
            <p><strong>Өтетін орны:</strong> <?php echo htmlspecialchars($meeting['location']); ?></p>
        </section>

        <section class="participants card">
            <h3>Қатысушылар:</h3>
            <ul>
                <?php foreach ($participants as $participant): ?>
                    <li><?php echo htmlspecialchars($participant['name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="minutes card">
            <h3>Кездесудің протоколдары:</h3>
            <?php if (count($minutes) > 0): ?>
                <ul>
                    <?php foreach ($minutes as $minute): ?>
                        <li>
                            <strong>Күні:</strong> <?php echo htmlspecialchars($minute['created_at']); ?><br>
                            <strong>Протокол:</strong> <?php echo nl2br(htmlspecialchars($minute['content'])); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Бұл кездесуге протоколдар жоқ.</p>
            <?php endif; ?>
        </section>

        <?php if ($_SESSION['user_id'] == $meeting['organizer_id']): ?>
            <section class="add-minute card">
                <h3>Жаңа протокол қосу:</h3>
                <form method="POST" action="add_minutes.php">
                    <textarea name="content" required placeholder="Протоколды енгізіңіз" class="textarea"></textarea><br>
                    <button type="submit" class="btn">Протоколды қосу</button>
                </form>
            </section>
        <?php endif; ?>

        <footer>
            <a href="index.php" class="btn">Артқа</a>
        </footer>
    </div>
</body>
</html>
