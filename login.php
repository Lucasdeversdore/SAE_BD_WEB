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

        $_SESSION['user_id'] = $personne['idPersonne'];
        $_SESSION['prenom'] = $personne['prenom'];
        $_SESSION['nom'] = $personne['nom'];
        $_SESSION['est_admin'] = $personne['est_admin'];
        $_SESSION['est_moniteur'] = $personne['est_moniteur'];

        if ($personne['est_admin']) {
            header("Location: page_admin.php");
        } 
        elseif($personne['est_moniteur']) {
            header("Location: calendar_moniteur.php");
        }
        else {
            header("Location: index.php");
        }
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }

        .nav {
            background-color: #00796b;
            padding: 15px 20px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin-top: 100px;
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

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin-top: 120px;
            }
        
            .nav {
                padding: 10px 15px;
            }
        
            .nav a {
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="nav">
        <div class="left-links">
            <a href="index.php">Accueil</a>
            <a href="calendar.php">Calendrier</a>
        </div>
        <div class="right-links">
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
