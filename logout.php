<?php
session_start();
session_destroy(); // Détruire toutes les données de session
header("Location: index.php"); // Rediriger vers la page d'accueil
exit;
?>
