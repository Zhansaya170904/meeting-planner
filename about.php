<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Біз туралы</title>
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
            padding: 30px 0;
        }

        .about-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .about-section h2 {
            font-size: 32px;
            color: #333;
        }

        .about-section p {
            font-size: 18px;
            color: #777;
            line-height: 1.6;
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
        <div class="about-section">
            <h2>Біз туралы</h2>
            <p>Кездесулерді басқаруға арналған платформамызға қош келдіңіз! Біз кездесулерді ұйымдастыруға, тапсырмаларды басқаруға және кестеңізбен жұмыс істеуге арналған қарапайым әрі тиімді құралды ұсынуға тырысамыз. Біздің команда өнімділігіңізді арттырып, жоспарлау процесін жеңілдетуге ұмтылатын тәжірибелі әзірлеушілерден тұрады.</p>
            <p>Біздің платформаны пайдалана отырып, сіз:</p>
            <ul>
                <li>Кездесулерді құрып, өңдеп және жоя аласыз</li>
                <li>Тапсырмаларыңызды басқара аласыз</li>
                <li>Кездесулер мен тапсырмалар туралы ақпараттың өзектілігін сақтай аласыз</li>
                <li>Және тағы басқалар!</li>
            </ul>
            <p>Біздің жүйеміз кездесулер мен тапсырмаларды ұйымдастыруда сенімді көмекшіңіз болады деп үміттенеміз!</p>
        </div>
    </div>

    <footer>
        © 2024 Кездесулерді басқаруға арналған веб-сайт
    </footer>
</body>
</html>
