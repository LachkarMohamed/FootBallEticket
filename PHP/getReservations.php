<?php
require 'db.php';

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}
$user_id = $_SESSION['user_id'];

$pdo = getPDO();
if ($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT r.id, m.team_home, m.team_away, m.date, m.venue, r.quantity, r.timestamp
                               FROM reservations r
                               JOIN matches m ON r.match_id = m.id
                               WHERE r.user_id = :id
                               ORDER BY r.timestamp DESC');
        $stmt->execute([':id'=>$user_id]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($reservations);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération des données: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Échec de la connexion à la base de données.']);
}
?>
