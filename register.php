<?php
require 'db.php';

$error = '';
$success = '';

$name = '';
$email = '';
$role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passwordRaw = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($name === '' || $email === '' || $passwordRaw === '') {
        $error = 'Барлық өрістерді толтырыңыз.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email мекенжайы дұрыс емес.';
    } elseif (empty($role)) {
        $error = 'Рөлді таңдаңыз.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Бұл email бұрын тіркелген.';
        } else {
            $password = password_hash($passwordRaw, PASSWORD_BCRYPT);

            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password, role)
                     VALUES (?, ?, ?, ?)"
                );

                $stmt->execute([$name, $email, $password, $role]);

                $success = 'Тіркелу сәтті өтті! Енді жүйеге кіре аласыз.';

                $name = '';
                $email = '';
                $role = '';
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Бұл email бұрын тіркелген.';
                } else {
                    $error = 'Тіркелу кезінде қате пайда болды.';
                }
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
    <title>Тіркелу</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .auth-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        .auth-form h1 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: #66afe9;
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .error {
            color: #d9534f;
            text-align: center;
            margin-bottom: 15px;
        }

        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 15px;
        }

        .auth-form p {
            text-align: center;
            font-size: 14px;
        }

        .auth-form a {
            color: #007bff;
            text-decoration: none;
        }

        .auth-form a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
<div class="auth-container">
    <div class="auth-form">

        <h1>Тіркелу</h1>

        <?php if ($error): ?>
            <p class="error">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success">
                <?php echo htmlspecialchars($success); ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <div class="input-group">
                <label for="name">Аты-жөні:</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="<?php echo htmlspecialchars($name); ?>"
                    required
                >
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                >
            </div>

            <div class="input-group">
                <label for="password">Құпиясөз:</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                >
            </div>

            <div class="input-group">
                <label for="role">Рөл:</label>

                <select name="role" id="role" required>
                    <option value="">Рөлді таңдаңыз</option>

                    <option
                        value="participant"
                        <?php echo $role === 'participant' ? 'selected' : ''; ?>
                    >
                        Қатысушы
                    </option>

                    <option
                        value="organizer"
                        <?php echo $role === 'organizer' ? 'selected' : ''; ?>
                    >
                        Ұйымдастырушы
                    </option>

                    <option
                        value="admin"
                        <?php echo $role === 'admin' ? 'selected' : ''; ?>
                    >
                        Әкімші
                    </option>
                </select>
            </div>

            <button type="submit" class="btn">
                Тіркелу
            </button>

        </form>

        <p>
            Аккаунтыңыз бар ма?
            <a href="login.php">Кіру</a>
        </p>

    </div>
</div>
</body>
</html>