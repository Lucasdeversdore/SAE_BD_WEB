<?php
session_start();

// Vérifier si l'utilisateur est connecté et s'il est moniteur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'moniteur') {
    header("Location: index.php");
    exit;
}

require 'db.php';

// Gestion des semaines : Calcul de la date de début (lundi) et de fin (dimanche)
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$dateDebut = date('Y-m-d', strtotime("monday this week +$offset week"));
$dateFin = date('Y-m-d', strtotime("sunday this week +$offset week"));

// Récupérer les séances où le moniteur connecté est concerné
$sqlSeances = "
    SELECT 
        S.idSeance,
        strftime('%w', S.dateDebut) AS jour,
        strftime('%H:%M', S.dateDebut) AS heure,
        S.duree,
        S.nbPersonneMax,
        (SELECT COUNT(*) FROM PARTICIPER P WHERE P.idSeance = S.idSeance) AS nbParticipants,
        C.typeCours AS coursType,
        S.niveau
    FROM SEANCE S
    JOIN COURS C ON S.idCours = C.idCours
    WHERE S.idMoniteur = ? AND S.dateDebut BETWEEN ? AND ?
    ORDER BY S.dateDebut
";
$stmtSeances = $pdo->prepare($sqlSeances);
$stmtSeances->execute([$_SESSION['user_id'], $dateDebut, $dateFin]);
$seances = $stmtSeances->fetchAll(PDO::FETCH_ASSOC);

// Heures d'affichage
$heures = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Séances</title>
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
            <a href="calendar_moniteur.php">Calendrier</a>
        </div>
        <div class="nav-right">
            <p>Bonjour, <?= htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']) ?></p>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>

    <main>
        <h1 style="text-align: center;">Calendrier des séances (Moniteur)</h1>
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
                <?php foreach ($jours as $jour): ?>
                    <th><?= $jour ?></th>
                <?php endforeach; ?>
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
                                $rowspan = $seance['duree'];
            
                                for ($i = 0; $i < $rowspan; $i++) {
                                    $usedCells["$jourIndex-" . date('H:i', strtotime("+$i hour", strtotime($heure)))] = true;
                                }
                                ?>
                                <td class="seance" rowspan="<?= $rowspan ?>">
                                    <p><strong>Cours :</strong> <?= htmlspecialchars($seance['coursType']) ?></p>
                                    <p><strong>Niveau :</strong> <?= htmlspecialchars($seance['niveau']) ?></p>
                                    <p><strong>Participants :</strong> <?= $seance['nbParticipants'] ?>/<?= $seance['nbPersonneMax'] ?></p>
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
