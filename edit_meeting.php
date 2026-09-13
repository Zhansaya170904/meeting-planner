<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Егер пайдаланушы авторизациядан өтпесе, login-ге бағыттаймыз
    exit;
}

require 'db.php';

$meeting_id = $_GET['id']; // URL-ден кездесу ID-ін аламыз

// Кездесу туралы мәліметтерді алу
$stmt = $pdo->prepare("SELECT * FROM meetings WHERE id = ? AND organizer_id = ?");
$stmt->execute([$meeting_id, $_SESSION['user_id']]);
$meeting = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$meeting) {
    echo "<div class='alert alert-danger'>Кездесу табылмады немесе сіз осы кездесудің ұйымдастырушысы емессіз.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];

    // Кездесудің мәліметтерін жаңарту
    $stmt = $pdo->prepare("UPDATE meetings SET title = ?, date = ?, time = ?, location = ? WHERE id = ?");
    if ($stmt->execute([$title, $date, $time, $location, $meeting_id])) {
        echo "<div class='alert alert-success'>Кездесу сәтті жаңартылды! <a href='index.php'>Негізгі бетке өту</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Кездесуді жаңарту кезінде қате пайда болды.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кездесуді өңдеу</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Кездесуді өңдеу</h2>
    
    <form method="POST" class="bg-light p-4 rounded shadow">
        <div class="mb-3">
            <label for="title" class="form-label">Кездесудің атауы:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($meeting['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Күні:</label>
            <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($meeting['date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="time" class="form-label">Уақыты:</label>
            <input type="time" id="time" name="time" class="form-control" value="<?php echo htmlspecialchars($meeting['time']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Өтетін орны:</label>
            <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($meeting['location']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Кездесуді жаңарту</button>
    </form>

    <div class="mt-3 text-center">
        <a href="index.php" class="btn btn-secondary">Болдырмау</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
