<?php
session_start();
require 'db.php';

if (!isset($_SESSION['est_admin']) || !$_SESSION['est_admin']) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre Équestre Orléans - Administration</title>
    <style>

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
            box-sizing: border-box;
        }

        .nav {
            background-color: #00796b;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .nav a:hover {
            opacity: 0.8;
        }

        .admin {
            padding: 60px 40px;
            text-align: center;
        }

        .admin h1 {
            font-size: 32px;
            color: #00796b;
            margin-bottom: 30px;
        }

        .admin-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .admin-button {
            background-color: #00796b;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .admin-button:hover {
            background-color: #004d40;
        }

        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 15px 0;
        }

    </style>
</head>
<body>
    <div class="nav">
        <div class="left-links">
            <a href="page_admin.php">Accueil</a>
            <a href="calendar.php">Calendrier</a>
            <a href="reservation.php">Réservation</a>
            <a href="mes_reservations.php">Mes Réservations</a>
        </div>
        <div class="right-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-info">
                    Bonjour, <?php echo htmlspecialchars($_SESSION['prenom']) . " " . htmlspecialchars($_SESSION['nom']); ?>
                </span>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php endif; ?>
        </div>
    </div>

    <section class="admin">
        <h1>Bienvenue dans l'espace d'administration</h1>
        <p>
            Utilisez les options ci-dessous pour gérer le contenu du centre équestre.
        </p>
        <div class="admin-buttons">
            <a href="ajoutPoney.php" class="admin-button">Ajouter un Poney</a>
            <a href="ajoutSeance.php" class="admin-button">Ajouter une Séance</a>
            <a href="ajoutMoniteur.php" class="admin-button">Ajouter un Moniteur</a>
        </div>
    </section>

    <footer>
        © <?= date('Y') ?> Poney Club - Tous droits réservés
    </footer>
</body>
</html>
