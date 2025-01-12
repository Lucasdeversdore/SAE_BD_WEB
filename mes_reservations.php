<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - Centre Équestre Grand Galop</title>
    <style>
        /* Style général */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
            box-sizing: border-box;
        }

        /* Barre de navigation */
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

        /* Section des réservations */
        .reservations-section {
            padding: 60px 40px;
            text-align: center;
        }

        .reservations-section h1 {
            font-size: 28px;
            color: #00796b;
            margin-bottom: 20px;
        }

        .liste-reservations {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .reservation-card {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
            max-width: 600px;
            margin: 0 auto;
        }

        .reservation-card h3 {
            margin: 0 0 10px;
            color: #004d40;
        }

        .reservation-card p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }

        .reservation-card button {
            margin-top: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background-color: #e53935;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .reservation-card button:hover {
            background-color: #c62828;
        }

        /* Pied de page */
        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <!-- Barre de navigation -->
    <div class="nav">
        <div class="left-links">
            <a href="index.php">Accueil</a>
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

    <!-- Section des réservations -->
    <div class="reservations-section">
        <h1>Mes Réservations</h1>

        <?php if (empty($reservations)): ?>
            <p>Aucune réservation trouvée.</p>
        <?php else: ?>
            <div class="liste-reservations">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-card">
                        <h3>Poney : <?= htmlspecialchars($reservation['nomP']) ?></h3>
                        <p><strong>Date de début :</strong> <?= date("d/m/Y H:i", strtotime($reservation['dateDebut'])) ?></p>
                        <p><strong>Date de fin :</strong> <?= date("H:i", strtotime($reservation['dateFin'])) ?></p>

                        <!-- Formulaire pour supprimer une réservation -->
                        <form method="POST" action="mes_reservations.php">
                            <input type="hidden" name="delete_id_poney" value="<?= $reservation['idPoney'] ?>">
                            <input type="hidden" name="delete_id_seance" value="<?= $reservation['idSeance'] ?>">
                            <button type="submit" name="delete_reservation">Supprimer</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pied de page -->
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php" style="color: #ffccbc;">Contactez-nous</a></p>
    </footer>

</body>
</html>
