<?php
require 'db.php';

header('Content-Type: application/json');

$pdo = getPDO();
if ($pdo) {
    try {
        $stmt = $pdo->query('SELECT * FROM matches WHERE date >= NOW() ORDER BY date ASC');
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($matches);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération des données: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Échec de la connexion à la base de données.']);
}
?>
