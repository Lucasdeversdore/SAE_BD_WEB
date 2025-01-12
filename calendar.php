<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des leçons</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8f5e9;
        }

        .nav {
            background-color: #2e7d32;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
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
            padding: 20px;
            text-align: center;
            margin-top: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #2e7d32;
            color: white;
        }

        .user-info {
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <div class="nav">
        <div>
            <a href="index.php">Accueil</a>
            <a href="calendar.php">Calendrier</a>
            <a href="reservation.php">Réservation</a>
        </div>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-info">
                    <?= htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']) ?>
                </span>
                <a href="logout.php">Déconnexion</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <h1>Planning des leçons 2024/2025</h1>
        <table>
            <tr>
                <th>Jour</th>
                <th>Heure</th>
                <th>Type de leçon</th>
            </tr>
            <tr>
                <td>Lundi</td>
                <td>10h - 11h</td>
                <td>Débutants</td>
            </tr>
            <tr>
                <td>Mercredi</td>
                <td>14h - 15h</td>
                <td>Confirmés</td>
            </tr>
            <tr>
                <td>Samedi</td>
                <td>16h - 17h</td>
                <td>Avancés</td>
            </tr>
        </table>
    </div>
</body>
</html>
