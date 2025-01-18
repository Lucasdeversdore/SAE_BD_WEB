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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
            margin-top: 60px;
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        form label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }

        form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        form input:focus {
            border-color: #00796b;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 121, 107, 0.2);
        }

        button {
            background-color: #00796b;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #004d40;
        }

        .nav {
            background-color: #00796b;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            z-index: 1000;
        }

        .nav .left-links,
        .nav .right-links {
            display: flex;
            align-items: center;
        }

        .nav .left-links a,
        .nav .right-links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .nav .left-links a:hover,
        .nav .right-links a:hover {
            opacity: 0.8;
        }



    </style>
</head>
<body>
    <div class="nav">
        <div class="left-links">
            <a href="index.php">Accueil</a>
            <a href="calendar.php">Calendrier</a>
        </div>
        <div class="right-links">
            <a href="login.php">Connexion</a>
        </div>
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
