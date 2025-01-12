<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de cours - Centre Équestre Grand Galop</title>
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

        /* Section de réservation */
        .reservation-section {
            padding: 60px 40px;
            text-align: center;
        }

        .reservation-section h1 {
            font-size: 28px;
            color: #00796b;
            margin-bottom: 20px;
        }

        .poney-block {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .les_poneys {
            border: 1px solid #ddd;
            padding: 20px;
            width: 250px;
            text-align: center;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .les_poneys img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }

        .les_poneys h3 {
            margin: 15px 0;
            color: #004d40;
        }

        .vide {
            color: red;
            font-weight: bold;
        }

        form {
            margin-top: 10px;
        }

        form label, form select, form button {
            display: block;
            margin: 10px 0;
            width: 100%;
        }

        select, button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #00796b;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #004d40;
        }

        /* Message de confirmation */
        .message {
            color: green;
            font-weight: bold;
            margin-top: 20px;
            background-color: #dff0d8;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #d0e9c6;
            display: inline-block;
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

    <!-- Section de réservation -->
    <div class="reservation-section">
        <h1>Réservation de cours</h1>
        <div class="poney-block">
            <!-- Blocs de poneys disponibles -->
            <?php
            if (!empty($poneys)) {
                foreach ($poneys as $poney) {
                    echo '<div class="les_poneys">';
                    echo '<img src="poney/' . htmlspecialchars($poney['imagePoney']) . '" alt="Poney">';
                    echo '<h3>' . htmlspecialchars($poney['nomP']) . '</h3>';

                    if (!empty($seancesDisponibles)) {
                        echo '<form method="POST">';
                        echo '<label>Sélectionnez une séance :</label>';
                        echo '<select name="seance_id" required>';
                        echo '<option value="">Choisir une séance</option>';
                        foreach ($seancesDisponibles as $seance) {
                            echo '<option value="' . $seance['idSeance'] . '">Du ' . date("d/m/Y H:i", strtotime($seance['dateDebut'])) . ' au ' . date("H:i", strtotime($seance['dateFin'])) . '</option>';
                        }
                        echo '</select>';
                        echo '<button type="submit">Réserver</button>';
                        echo '</form>';
                    } else {
                        echo '<p class="vide">Aucune séance disponible</p>';
                    }

                    echo '</div>';
                }
            } else {
                echo '<p>Aucun poney disponible pour votre poids.</p>';
            }
            ?>
        </div>

        <!-- Message de confirmation -->
        <?php if (isset($message)) echo '<div class="message">' . $message . '</div>'; ?>
    </div>

    <!-- Pied de page -->
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php" style="color: #ffccbc;">Contactez-nous</a></p>
    </footer>

</body>
</html>
