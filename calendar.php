<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Ағымдағы күнді аламыз
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$today = date('Y-m-d');

// Пайдаланушының берілген айдағы барлық кездесулерін аламыз
$stmt = $pdo->prepare("SELECT * FROM meetings WHERE organizer_id = ? AND YEAR(date) = ? AND MONTH(date) = ?");
$stmt->execute([$_SESSION['user_id'], $year, $month]);
$meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Кездесулерді күндер бойынша массивке айналдырамыз
$events = [];
foreach ($meetings as $meeting) {
    $date = $meeting['date'];
    if (!isset($events[$date])) {
        $events[$date] = [];
    }
    $events[$date][] = $meeting;
}

// Айдың күндерін көрсету функциясы
function draw_calendar($month, $year, $events, $today) {
    $daysOfWeek = ['Дс', 'Сс', 'Ср', 'Бс', 'Жм', 'Сн', 'Жк'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $startDay = date('N', $firstDayOfMonth);

    $calendar = '<table class="table table-bordered table-hover">';
    $calendar .= '<thead class="table-dark"><tr>';

    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='text-center'>$day</th>";
    }
    $calendar .= '</tr></thead><tbody><tr>';

    // Айдың басындағы бос ұяшықтар
    if ($startDay > 1) {
        $calendar .= str_repeat('<td></td>', $startDay - 1);
    }

    $currentDay = 1;
    while ($currentDay <= $numberDays) {
        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
        $classes = ['text-center', 'p-3'];

        if ($currentDate === $today) {
            $classes[] = 'bg-warning text-white'; // Ағымдағы күн
        } elseif (isset($events[$currentDate])) {
            $classes[] = 'bg-light';
        }

        $calendar .= '<td class="' . implode(' ', $classes) . '">';
        $calendar .= "<strong>$currentDay</strong>";

        if (isset($events[$currentDate])) {
            $calendar .= '<ul class="list-unstyled mt-2">';
            foreach ($events[$currentDate] as $event) {
                $calendar .= "<li><a href='meeting_details.php?id={$event['id']}' class='text-decoration-none text-primary'>{$event['title']}</a></li>";
            }
            $calendar .= '</ul>';
        }

        $calendar .= '</td>';

        if (date('N', mktime(0, 0, 0, $month, $currentDay, $year)) == 7) {
            $calendar .= '</tr><tr>';
        }

        $currentDay++;
    }

    // Айдың соңындағы бос ұяшықтар
    $remainingDays = 7 - date('N', mktime(0, 0, 0, $month, $numberDays, $year));
    if ($remainingDays < 7) {
        $calendar .= str_repeat('<td></td>', $remainingDays);
    }

    $calendar .= '</tr></tbody></table>';
    return $calendar;
}
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кездесулер күнтізбесі</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 30px 0;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .fw-bold {
            font-weight: bold;
        }
        .fs-4 {
            font-size: 1.25rem;
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

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Кездесулер күнтізбесі</h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="calendar.php?year=<?= $year ?>&month=<?= $month - 1 ?>" class="btn btn-primary">← Алдыңғы ай</a>
            <span class="fw-bold fs-4"><?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></span>
            <a href="calendar.php?year=<?= $year ?>&month=<?= $month + 1 ?>" class="btn btn-primary">Келесі ай →</a>
        </div>

        <?= draw_calendar($month, $year, $events, $today); ?>

        <div class="mt-3 text-center">
            <a href="index.php" class="btn btn-secondary">Басты бетке оралу</a>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
