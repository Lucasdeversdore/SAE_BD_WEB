<?php
require 'db.php';
session_start();

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$dateDebut = date('Y-m-d', strtotime("now +$offset week"));
$dateFin = date('Y-m-d', strtotime("now +$offset week +6 days"));

$query = "
    SELECT 
        (strftime('%w', S.dateDebut) + 6) % 7 AS jour,
        strftime('%H:%M', S.dateDebut) AS heure,
        S.duree,
        S.nbPersonneMax,
        (SELECT COUNT(*) FROM ASSISTER A WHERE A.idSeance = S.idSeance) AS nbParticipants,
        P.prenom || ' ' || P.nom AS moniteur,
        SUBSTR(P.prenom, 1, 1) || '.' || P.nom AS moniteurAbrege
    FROM SEANCE S
    JOIN MONITEUR M ON S.idMoniteur = M.idMoniteur
    JOIN PERSONNE P ON M.idPersonne = P.idPersonne
    WHERE S.dateDebut >= :dateDebut AND S.dateDebut <= :dateFin
    ORDER BY S.dateDebut";
$stmt = $pdo->prepare($query);
$stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
$seances = $stmt->fetchAll(PDO::FETCH_ASSOC);

$heures = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

$occupes = [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des cours - Centre Équestre Grand Galop</title>
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

        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 15px 0;
        }

        .user-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: url('img/user.png') no-repeat center center;
            background-size: contain;
            vertical-align: middle;
        }
    </style>
</head>
<body>
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
    <h1 style="text-align: center; color: #00796b; margin-top: 40px;">Calendrier des Cours de Poney</h1>
    <h2 style="text-align: center; color: #555;">Semaine du <?php echo date('d/m/Y', strtotime($dateDebut)); ?> au <?php echo date('d/m/Y', strtotime($dateFin)); ?></h2>
    <div class="week-nav">
        <a href="calendar.php?offset=<?php echo $offset - 1; ?>"> 
            <button <?php if ($offset <= 0) echo 'disabled'; ?>>Semaine précédente</button> 
        </a> 
        <a href="calendar.php?offset=<?php echo $offset + 1; ?>"> 
            <button>Semaine suivante</button>  
        </a> 
    </div>
    <div style="overflow-x: auto; padding: 0 20px;">   
        <table border="1" cellpadding="10" cellspacing="0"> 
            <tr>
                <th>Heure</th>
                <?php foreach ($jours as $jour): ?>
                    <th><?php echo $jour; ?></th>
                <?php endforeach; ?>
            </tr>

            <?php foreach ($heures as $heure): ?> 
                <tr>
                    <td><?php echo $heure; ?></td> 
                    <?php foreach ($jours as $jour): ?> 
                        <?php 
                        if (isset($occupes["$jour-$heure"])) { 
                            continue; 
                        }

                        $reservationTrouvee = false;

                        foreach ($seances as $seance) {
                            $duree = (int)$seance['duree'];
                            $heureDebut = $seance['heure'];
                            $nbPersonneMax = (int)$seance['nbPersonneMax'];
                            $nbParticipants = (int)$seance['nbParticipants'];
                            $moniteurAbrege = htmlspecialchars($seance['moniteurAbrege']);

                            if ($jours[$seance['jour']] == $jour && $heureDebut == $heure) {
                                // Calcul du créneau horaire final
                                for ($i = 0; $i < $duree; $i++) {
                                    $heureOccupee = date('H:i', strtotime("$heure +$i hour"));
                                    $occupes["$jour-$heureOccupee"] = true;
                                }

                                echo "<td class='reserved' rowspan='$duree'>
                                    <span class='user-icon'></span> 
                                    $nbPersonneMax - $moniteurAbrege
                                </td>";

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
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php">Contactez-nous</a></p>
    </footer>
</body>
</html>
