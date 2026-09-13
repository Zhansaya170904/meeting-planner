<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Егер пайдаланушы авторизациядан өтпеген болса, кіріспе бетке жіберу
    exit;
}

require 'db.php';

// Пайдаланушының мәліметтерін алу
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Профильді жаңарту
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Егер жаңа құпиясөз енгізілсе, оны жаңарту
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT); // Жаңа құпиясөзді хэштеу
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $email, $password, $user_id]);
    } else {
        // Егер құпиясөз өзгермесе
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $user_id]);
    }

    $successMessage = "Профиль жаңартылды!";
}

// Пайдаланушының хабарламаларын алу
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пайдаланушының профилі</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef; /* Жеңіл сұр фон */
            color: #343a40; /* Қара сұр мәтін түсі */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            background-color: #28a745; /* Жасыл тақырып */
            color: white;
            padding: 15px;
            text-align: center;
            width: 100%;
        }

        h2, h3 {
            color: #28a745; /* Жасыл тақырыптар */
        }

        section {
            width: 100%;
            max-width: 600px;
            margin: 20px;
            padding: 20px;
            background-color: #ffffff; /* Ақ фон */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        form label {
            font-weight: bold;
            margin-top: 10px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #ced4da; /* Жеңіл шекара түсі */
            border-radius: 4px;
        }

        form button, .cancel-button {
            background-color: #28a745; /* Жасыл батырма */
            color: white;
            padding: 10px 0; /* Теңдей батырма биіктігі */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%; /* Толық ені */
            max-width: 200px; /* Батырманың ең үлкен ені */
            margin: 5px 0; /* Батырмалар арасындағы арақашықтық */
        }

        form button:hover {
            background-color: #218838; /* Батырма үстінен өту кезінде қараңғы жасыл түс */
        }

        .cancel-button {
            background-color: #dc3545; /* Қызыл түсті болдырмау батырмасы */
        }

        .cancel-button:hover {
            background-color: #c82333; /* Батырма үстінен өту кезінде қызыл түс */
        }

        .notifications ul {
            list-style-type: none;
            padding: 0;
        }

        .notifications li {
            background-color: #f8f9fa; /* Хабарламалар үшін жеңіл фон */
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .notifications li strong {
            display: block;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        footer {
            background-color: #343a40; /* Қара сұр түсті футер */
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
            width: 100%;
        }

        .logout-button {
            display: flex;
            justify-content: center;
            margin-top: 20px; /* Батырманың үстінде орын қалдырыңыз */
        }

        a {
            color: white; /* Ақ түсті шығу батырмасы */
            background-color: #dc3545; /* Қызыл шығу батырмасы */
            padding: 10px 15px;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            width: 100%; /* Толық ені */
            max-width: 200px; /* Ең үлкен ені */
        }

        a:hover {
            text-decoration: underline;
            background-color: #c82333; /* Батырма үстінен өту кезінде қызыл түс */
        }

        .success {
            color: #28a745; /* Жасыл түс жетістіктер хабарламасы */
            margin-bottom: 15px;
        }

        .error {
            color: #dc3545; /* Қызыл түс қателер хабарламасы */
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Пайдаланушының профилі</h1>
    </header>

    <!-- Профильді жаңарту формасы -->
    <section>
        <?php if (isset($successMessage)): ?>
            <p class="success"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Аты-жөні:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label>Электрондық пошта:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Жаңа құпиясөз (өзгерту қажет болмаса, бос қалдырыңыз):</label>
            <input type="password" name="password">

            <div style="display: flex; flex-direction: column; align-items: center;">
                <button type="submit" name="update_profile">Профильді жаңарту</button>
                <a class="cancel-button" href="index.php">Бас тарту</a> <!-- Бас тарту батырмасы -->
            </div>
        </form>
    </section>

    <hr>

    <!-- Пайдаланушының хабарламалары -->
    <section class="notifications">
        <h3>Хабарламалар</h3>
        <?php if (count($notifications) > 0): ?>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                        <small>Күні: <?php echo htmlspecialchars($notification['created_at']); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Жаңа хабарламалар жоқ.</p>
        <?php endif; ?>
    </section>

    <hr>

    <!-- Шығу батырмасы -->
    <div class="logout-button">
        <a href="logout.php">Шығу</a>
    </div>
</body>
</html>
