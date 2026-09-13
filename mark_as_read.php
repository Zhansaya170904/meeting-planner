<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['notification_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$notification_id = $_GET['notification_id'];

// Хабарламаны оқылған деп белгілеу
$stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ?");
$stmt->execute([$notification_id]);

header("Location: profile.php"); // Профильге қайта бағыттау
exit;
?>
