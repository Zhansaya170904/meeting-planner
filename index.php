<?php
session_start();

// Пайдаланушының авторизацияланғанын тексеру
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Егер пайдаланушы авторизацияланбаса, login бетіне қайта бағыттау
    exit;
}

require 'db.php'; // Мәліметтер базасы конфигурациясын қосу

// Пайдаланушының кездесулері тізімін алу
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT m.id, m.title, m.date, m.time, m.location
                       FROM meetings m
                       JOIN meeting_participants mp ON m.id = mp.meeting_id
                       WHERE mp.user_id = ?");
$stmt->execute([$user_id]);

// Нәтиженің бар-жоғын тексеру
if ($stmt->rowCount() > 0) {
    $meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $meetings = [];
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сіздің кездесулеріңіз</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #5c6bc0;
            padding: 10px 0;
        }

        header nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        header nav ul li {
            display: inline;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 4px;
        }

        header nav ul li a:hover {
            background-color: #3f4e9e;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            max-width: 1200px;
        }

        .welcome-message {
            text-align: center;
            margin: 30px 0;
        }

        .welcome-message h2 {
            font-size: 32px;
            color: #333;
        }

        .welcome-message p {
            font-size: 18px;
            color: #777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        td a {
            text-decoration: none;
            color: #007BFF;
        }

        td a:hover {
            text-decoration: underline;
        }

        .create-meeting-btn {
            background-color: #5c6bc0;
            color: white;
            padding: 12px 25px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
            font-size: 18px;
        }

        .create-meeting-btn:hover {
            background-color: #3f4e9e;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            position: fixed;
            width: 100%;
            bottom: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Басты бет</a></li>
                <li><a href="calendar.php">Кездесулер күнтізбесі</a></li>
                <li><a href="tasks.php">Тапсырмалар</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="about.php">Біз туралы</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="welcome-message">
            <h2>Қош келдіңіз, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
            <p>Сіздің рөліңіз: <?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
        </div>

        <h3>Сіздің кездесулеріңіз:</h3>

        <?php if (empty($meetings)): ?>
            <p>Сіздің кездесуіңіз жоқ.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Атауы</th>
                    <th>Күні</th>
                    <th>Уақыты</th>
                    <th>Орыны</th>
                    <th>Әрекеттер</th>
                </tr>
                <?php foreach ($meetings as $meeting): ?>
                <tr>
                    <td><?php echo htmlspecialchars($meeting['title']); ?></td>
                    <td><?php echo htmlspecialchars($meeting['date']); ?></td>
                    <td><?php echo htmlspecialchars($meeting['time']); ?></td>
                    <td><?php echo htmlspecialchars($meeting['location']); ?></td>
                    <td>
                        <a href="edit_meeting.php?id=<?php echo $meeting['id']; ?>">Өзгерту</a> | 
                        <a href="delete_meeting.php?id=<?php echo $meeting['id']; ?>" 
                           onclick="return confirm('Шынымен осы кездесуді жойғыңыз келе ме?')">Жою</a> | 
                        <a href="minutes.php?id=<?php echo $meeting['id']; ?>">Протоколдар</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <a href="create_meeting.php" class="create-meeting-btn">Жаңа кездесу құру</a>
    </div>

    <footer>
        © 2024 Кездесулерді басқару веб-сайты
    </footer>
</body>
</html>
