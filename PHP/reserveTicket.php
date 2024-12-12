<?php
require 'db.php';
session_start();
header('Content-Type: application/json');

$pdo = getPDO();
if ($pdo) {
    $match_id = $_POST['match_id'];
    $user_id = $_SESSION['user_id'];
    //$user_id = 1;
    $quantity = $_POST['quantity'];

    if ($match_id && $user_id && $quantity) {
        try {
            $stmt = $pdo->prepare('INSERT INTO reservations (match_id, user_id, quantity, timestamp) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$match_id, $user_id, $quantity]);
            echo json_encode(['success' => 'Reservation successful']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Erreur lors de la réservation: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Tous les champs sont requis.']);
    }
} else {
    echo json_encode(['error' => 'Échec de la connexion à la base de données.']);
}
