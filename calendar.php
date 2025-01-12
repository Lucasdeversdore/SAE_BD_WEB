<?php
require 'db.php';
session_start();

// Récupération de l'offset de semaine depuis la requête GET
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Calcul des dates de début et fin pour la requête SQL
$dateDebut = date('Y-m-d', strtotime("now +$offset week"));
$dateFin = date('Y-m-d', strtotime("now +$offset week +6 days"));

// Requête pour récupérer les séances de la semaine sélectionnée
$query = "SELECT strftime('%w', dateDebut) AS jour, 
                 strftime('%H:%M', dateDebut) AS heure, 
                 duree 
          FROM SEANCE 
          WHERE dateDebut >= :dateDebut 
            AND dateDebut <= :dateFin
          ORDER BY dateDebut";

$stmt = $pdo->prepare($query);
$stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
$seances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Créneaux horaires et jours de la semaine
$heures = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
$jours = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des cours - Centre Équestre Grand Galop</title>
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

        /* Tableau des créneaux */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        td {
            font-size: 16px;
        }

        td.libre {
            background-color: #e8f5e9;
        }

        td.reserved {
            background-color: #ffcdd2;
            font-weight: bold;
        }

        td.pause {
            background-color:rgb(255, 255, 255);
            border: none;
        }

        /* Boutons de navigation */
        .week-nav {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            gap: 10px;
        }

        .week-nav button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #00796b;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .week-nav button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .week-nav button:hover:not(:disabled) {
            background-color: #004d40;
        }

        /* Pied de page */
        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 15px 0;
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

    <!-- Section calendrier -->
    <h1 style="text-align: center; color: #00796b; margin-top: 40px;">Calendrier des Cours de Poney</h1>
    <h2 style="text-align: center; color: #555;">Semaine du <?php echo date('d/m/Y', strtotime($dateDebut)); ?> au <?php echo date('d/m/Y', strtotime($dateFin)); ?></h2>

    <!-- Boutons de navigation -->
    <div class="week-nav">
        <!-- Bouton Semaine Précédente (désactivé si offset <= 0) -->
        <a href="calendar.php?offset=<?php echo $offset - 1; ?>">
            <button <?php if ($offset <= 0) echo 'disabled'; ?>>Semaine précédente</button>
        </a>

        <!-- Bouton Semaine Suivante -->
        <a href="calendar.php?offset=<?php echo $offset + 1; ?>">
            <button>Semaine suivante</button>
        </a>
    </div>

    <!-- Tableau des créneaux -->
    <div style="overflow-x: auto; padding: 0 20px;">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Heure</th>
                <?php foreach ($jours as $jour): ?>
                    <th><?php echo $jour; ?></th>
                <?php endforeach; ?>
            </tr>

            <?php
            // Tableau pour suivre les heures occupées
            $heuresOccupees = [];

            foreach ($heures as $index => $heure):
                if ($heure === '14:00' && !in_array('13:00', $heures)): ?>
                    <!-- Ligne spéciale pour la pause de 13h à 14h -->
                    <tr>
                        <td>13:00</td>
                        <?php foreach ($jours as $jour): ?>
                            <td class="pause"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td><?php echo $heure; ?></td>
                    <?php foreach ($jours as $jour): ?>
                        <?php
                        // Vérifier si cette heure est déjà occupée
                        if (isset($heuresOccupees["$jour-$heure"])) {
                            continue;
                        }

                        $reservationTrouvee = false;

                        foreach ($seances as $seance) {
                            $duree = (int)$seance['duree'];
                            $heureDebut = $seance['heure'];

                            if ($jours[$seance['jour']] == $jour && $heureDebut == $heure) {
                                echo "<td class='reserved' rowspan='$duree'>Réservé</td>";

                                // Marquer les heures suivantes comme occupées
                                for ($i = 1; $i < $duree; $i++) {
                                    $heureOccupee = date('H:i', strtotime("$heure +$i hour"));
                                    $heuresOccupees["$jour-$heureOccupee"] = true;
                                }

                                $reservationTrouvee = true;
                                break;
                            }
                        }

                        if (!$reservationTrouvee) {
                            echo "<td class='libre'>Libre</td>";
                        }
                        ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Pied de page -->
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php" style="color: #ffccbc;">Contactez-nous</a></p>
    </footer>

</body>
</html>
