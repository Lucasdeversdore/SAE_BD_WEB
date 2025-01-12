<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connexion à la base de données
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cheval = $_POST['cheval'];
    $creneau = $_POST['creneau'];
    $userId = $_SESSION['user_id'];

    $sql = "INSERT INTO RESERVATION (idPersonne, cheval, creneau) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$userId, $cheval, $creneau]);
        echo "<p style='color: green; text-align: center;'>Réservation effectuée avec succès !</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red; text-align: center;'>Erreur lors de la réservation : " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
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
            margin-top: 100px;
        }

        form {
            display: inline-block;
            padding: 20px;
            background-color: #d7e9dc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form label, select, button {
            display: block;
            margin-bottom: 15px;
            width: 100%;
        }

        select, button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #2e7d32;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #1b5e20;
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
        <h1>Réservation de cours</h1>
        <form method="POST">
            <label for="cheval">Choisir un cheval :</label>
            <select name="cheval" id="cheval" required>
                <option value="Cheval 1">Cheval 1</option>
                <option value="Cheval 2">Cheval 2</option>
                <option value="Cheval 3">Cheval 3</option>
            </select>

            <label for="creneau">Choisir un créneau :</label>
            <select name="creneau" id="creneau" required>
                <option value="Lundi 10h">Lundi 10h</option>
                <option value="Mercredi 14h">Mercredi 14h</option>
                <option value="Samedi 16h">Samedi 16h</option>
            </select>

            <button type="submit">Réserver</button>
        </form>
    </div>
</body>
</html>
