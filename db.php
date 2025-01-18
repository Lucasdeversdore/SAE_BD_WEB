<?php
// Connexion à la base de données bd.db avec PDO
try {
    $pdo = new PDO('sqlite:bd.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    die();
}
?>
