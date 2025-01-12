<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    $sql = "SELECT * FROM PERSONNE WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $personne = $stmt->fetch();

    if ($personne && password_verify($mdp, $personne['mdp'])) {
        // Stockage des informations dans la session
        $_SESSION['user_id'] = $personne['idPersonne'];
        $_SESSION['prenom'] = $personne['prenom'];
        $_SESSION['nom'] = $personne['nom'];

        // Redirection vers la page d'accueil
        header("Location: index.php");
        exit;
    } else {
        echo "<p style='color: red; text-align: center;'>Identifiants incorrects.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .nav {
            background-color: #2e7d32;
            padding: 10px 20px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .container {
            background-color: #d7e9dc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin-top: 100px; /* Espace sous la navbar */
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
    </style>
</head>
<body>
    <!-- Inclusion de la navbar -->
    <div class="nav">
        <div>
            <a href="index.php">Accueil</a>
            <a href="calendar.php">Calendrier</a>
            <a href="reservation.php">Réservation</a>
        </div>
        <div>
            <a href="register.php">Inscription</a>
        </div>
    </div>

    <div class="container">
        <h2>Formulaire de connexion</h2>
        <form method="POST">
            <label>Email :</label>
            <input type="email" name="email" required>
            <label>Mot de passe :</label>
            <input type="password" name="mdp" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
