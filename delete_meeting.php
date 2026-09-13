<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Егер пайдаланушы авторизациядан өтпесе, login-ге бағыттаймыз
    exit;
}

require 'db.php';

$meeting_id = $_GET['id']; // URL-ден кездесу ID-ін аламыз

// Пайдаланушының кездесудің ұйымдастырушысы екенін тексереміз
$stmt = $pdo->prepare("SELECT * FROM meetings WHERE id = ? AND organizer_id = ?");
$stmt->execute([$meeting_id, $_SESSION['user_id']]);
$meeting = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$meeting) {
    echo "Кездесу табылмады немесе сіз осы кездесудің ұйымдастырушысы емессіз.";
    exit;
}

// Кездесуді және оған байланысты жазбаларды жоямыз
$stmt = $pdo->prepare("DELETE FROM meeting_participants WHERE meeting_id = ?");
$stmt->execute([$meeting_id]);

$stmt = $pdo->prepare("DELETE FROM meetings WHERE id = ?");
if ($stmt->execute([$meeting_id])) {
    echo "Кездесу сәтті жойылды! <a href='index.php'>Негізгі бетке өту</a>";
} else {
    echo "Кездесуді жою кезінде қате пайда болды.";
}
?>
