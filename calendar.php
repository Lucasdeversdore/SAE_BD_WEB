<?php
// Activer l'affichage des erreurs pour débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Gestion des semaines : ajuster les dates pour que la semaine commence le lundi
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Calcul de la date du lundi de la semaine courante
$lundi = strtotime("monday this week +$offset week");
$dateDebut = date('Y-m-d', $lundi); // Lundi de la semaine
$dateFin = date('Y-m-d', strtotime("+6 days", $lundi)); // Dimanche de la semaine

// Récupération des informations sur les réservations de l'utilisateur
$sqlReservations = "
    SELECT PR.idSeance, PR.idPoney, P.nomP AS poneyNom
    FROM PARTICIPER PR
    JOIN PONEY P ON PR.idPoney = P.idPoney
    WHERE PR.idCl = ?
";
$stmtReservations = $pdo->prepare($sqlReservations);
$stmtReservations->execute([$_SESSION['user_id']]);
$reservations = $stmtReservations->fetchAll(PDO::FETCH_ASSOC);
$reservationMap = [];
foreach ($reservations as $reservation) {
    $reservationMap[$reservation['idSeance']] = $reservation;
}

// Récupération des séances de la semaine avec les informations supplémentaires
$sqlSeances = "
    SELECT 
        S.idSeance,
        strftime('%w', S.dateDebut) AS jour,
        strftime('%H:%M', S.dateDebut) AS heure,
        S.duree,
        S.nbPersonneMax,
        S.niveau,
        C.typeCours,
        (SELECT COUNT(*) FROM PARTICIPER P WHERE P.idSeance = S.idSeance) AS nbParticipants,
        P.prenom || ' ' || P.nom AS moniteur,
        SUBSTR(P.prenom, 1, 1) || '. ' || P.nom AS moniteurAbrege
    FROM SEANCE S
    JOIN MONITEUR M ON S.idMoniteur = M.idMoniteur
    JOIN PERSONNE P ON M.idPersonne = P.idPersonne
    JOIN COURS C ON S.idCours = C.idCours
    WHERE S.dateDebut BETWEEN ? AND ?
    ORDER BY S.dateDebut
";
$stmtSeances = $pdo->prepare($sqlSeances);
$stmtSeances->execute([$dateDebut, $dateFin]);
$seances = $stmtSeances->fetchAll(PDO::FETCH_ASSOC);

$heures = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

// Gestion des annulations de réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $idSeance = $_POST['idSeance'];
    $idPoney = $_POST['idPoney'];

    $sqlCancelReservation = "DELETE FROM PARTICIPER WHERE idSeance = ? AND idPoney = ? AND idCl = ?";
    $stmtCancelReservation = $pdo->prepare($sqlCancelReservation);
    $stmtCancelReservation->execute([$idSeance, $idPoney, $_SESSION['user_id']]);
    header("Location: calendar.php?offset=$offset");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier de Réservations</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
            box-sizing: border-box;
        }

        /* Navigation styles */
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
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s ease, opacity 0.3s ease;
        }

        .nav-right p {
            color: white;
            font-weight: 600;
            margin: 0 15px 0 0;
        }

        /* Navigation buttons */
        .navigation-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .navigation-buttons a button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .navigation-buttons a button:hover {
            background-color: #00574b;
        }

        .navigation-buttons a button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 80px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        td.reserved {
            background-color: #bbdefb;
            cursor: pointer;
        }

        td.libre {
            background-color: #c8e6c9;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        td.libre:hover {
            background-color: #a5d6a7;
        }

        td.reserved:hover {
            background-color: #90caf9;
        }

        td form button.cancel {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        td form button.cancel:hover {
            background-color: #b71c1c;
        }

        td a {
            display: block;
            text-decoration: none;
            color: black;
            font-weight: 600;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        td a:hover {
            background-color: #81c784;
            color: white;
        }

        td.complet {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        /* Footer styles */
        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 15px 20px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .nav .nav-left, .nav .nav-right {
                flex-direction: column;
                gap: 10px;
            }

            .navigation-buttons {
                flex-direction: column;
                align-items: center;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }
        }

    </style>
</head>
<body>
    <div class="nav">
        <div class="nav-left">
            <a href="index.php">Accueil</a>
            <a href="calendar.php">Calendrier</a>
        </div>
        <div class="nav-right">
            <p>Bonjour, <?= htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']) ?></p>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>

    <main>
        <h1 style="text-align: center;">Calendrier de réservation</h1>
        <h2 style="text-align: center;">Semaine du <?= date('d/m/Y', strtotime($dateDebut)) ?> au <?= date('d/m/Y', strtotime($dateFin)) ?></h2>

        <div class="navigation-buttons">
            <a href="?offset=<?= $offset - 1 ?>">
                <button <?= $offset <= 0 ? 'disabled' : '' ?>>Semaine précédente</button>
            </a>
            <a href="?offset=<?= $offset + 1 ?>">
                <button>Semaine suivante</button>
            </a>
        </div>

        <table>
            <tr>
                <th>Heure</th>
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <th><?= $jours[$i] ?><br><?= date('d/m', strtotime("$dateDebut +$i day")) ?></th>
                <?php endfor; ?>
            </tr>
            <?php
            $usedCells = [];
            foreach ($heures as $heure): ?>
                <tr>
                    <td><?= $heure ?></td>
                    <?php foreach ($jours as $jourIndex => $jour): ?>
                        <?php
                        $cellKey = "$jourIndex-$heure";
                        if (isset($usedCells[$cellKey])) {
                            continue;
                        }

                        $seanceTrouvee = false;
                        foreach ($seances as $seance) {
                            $jourCorrected = ($seance['jour'] == 0) ? 6 : $seance['jour'] - 1;
                            if ($jourCorrected == $jourIndex && $seance['heure'] == $heure) {
                                $seanceTrouvee = true;
                                $isReserved = isset($reservationMap[$seance['idSeance']]);
                                $isComplete = $seance['nbParticipants'] >= $seance['nbPersonneMax'];
                                $cssClass = $isComplete ? 'complet' : ($isReserved ? 'reserved' : 'libre');
                                $rowspan = $seance['duree'];

                                for ($i = 0; $i < $rowspan; $i++) {
                                    $usedCells["$jourIndex-" . date('H:i', strtotime("+$i hour", strtotime($heure)))] = true;
                                }
                                ?>
                                <td class="<?= $cssClass ?>" rowspan="<?= $rowspan ?>">
                                    <p><strong>Type :</strong> <?= htmlspecialchars($seance['typeCours']) ?></p>
                                    <p><strong>Niveau :</strong> <?= htmlspecialchars($seance['niveau']) ?></p>
                                    <p><strong>Durée :</strong> <?= $seance['duree'] ?> heure(s)</p>
                                    <p><strong>Participants :</strong> <?= $seance['nbParticipants'] ?>/<?= $seance['nbPersonneMax'] ?></p>
                                    <p><strong>Moniteur :</strong> <?= htmlspecialchars($seance['moniteurAbrege']) ?></p>
                                    <?php if ($isReserved): ?>
                                        <form method="POST">
                                            <input type="hidden" name="idSeance" value="<?= $seance['idSeance'] ?>">
                                            <input type="hidden" name="idPoney" value="<?= $reservationMap[$seance['idSeance']]['idPoney'] ?>">
                                            <button type="submit" name="cancel_reservation" class="cancel">Annuler</button>
                                        </form>
                                    <?php elseif ($isComplete): ?>
                                        <p>Complet</p>
                                    <?php else: ?>
                                        <a href="reservation.php?idSeance=<?= $seance['idSeance'] ?>">Réserver</a>
                                    <?php endif; ?>
                                </td>
                                <?php
                                break;
                            }
                        }
                        if (!$seanceTrouvee): ?>
                            <td></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>

    <footer>
        © <?= date('Y') ?> Poney Club - Tous droits réservés
    </footer>
</body>
</html>
