<?php
$host = '127.0.0.1'; // Жергілікті хост
$db = 'meeting_manager'; // Мәліметтер базасының атауы
$user = 'root'; // Пайдаланушы аты, әдетте XAMPP-те root
$pass = ''; // Құпиясөз, әдетте бос
$charset = 'utf8mb4'; // Кодтау

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
} catch (PDOException $e) {
    echo "Мәліметтер базасына қосылуда қате: " . $e->getMessage();
}
?>
