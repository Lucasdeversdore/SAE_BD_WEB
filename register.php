<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $numTel = $_POST['numTel'];
    $email = $_POST['email'];
    $poids = $_POST['poids'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO PERSONNE (prenom, nom, numTel, email, poids, mdp) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$prenom, $nom, $numTel, $email, $poids, $mdp]);
        // Redirection vers la page de connexion après une inscription réussie
        header("Location: login.php?success=1");
        exit;
    } catch (PDOException $e) {
        echo "<p style='color: red; text-align: center;'>Erreur lors de l'inscription : " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #d7e9dc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%; /* Responsive pour petits écrans */
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #2e7d32;
        }

        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #2e7d32;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #1b5e20;
        }

        .nav {
            background-color: #2e7d32;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        .nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Inclusion de la navbar -->
    <div class="nav">
        <a href="index.php">Accueil</a>
        <a href="calendar.php">Calendrier</a>
        <a href="reservation.php">Réservation</a>
        <a href="login.php">Connexion</a>
    </div>

    <div class="container">
        <h2>Formulaire d'inscription</h2>
        <form method="POST">
            <label>Prénom : </label><input type="text" name="prenom" required>
            <label>Nom : </label><input type="text" name="nom" required>
            <label>Numéro de téléphone : </label><input type="text" name="numTel" required>
            <label>Email : </label><input type="email" name="email" required>
            <label>Poids : </label><input type="number" step="0.1" name="poids" required>
            <label>Mot de passe : </label><input type="password" name="mdp" required>
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>
