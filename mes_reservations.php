<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Vérifie si l'utilisateur est administrateur
$isAdmin = isset($_SESSION['est_admin']);

// Gestion de l'annulation d'une réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $idSeance = $_POST['idSeance'];
    $idPoney = $_POST['idPoney'];

    $sqlDeleteReservation = "DELETE FROM PARTICIPER WHERE idSeance = ? AND idPoney = ? AND idCl = ?";
    $stmtDelete = $pdo->prepare($sqlDeleteReservation);
    $stmtDelete->execute([$idSeance, $idPoney, $_SESSION['user_id']]);
}

// Récupération des réservations de l'utilisateur
$sqlReservations = "
    SELECT 
        S.idSeance, 
        S.dateDebut, 
        S.duree,
        P.nomP AS poneyNom, 
        M.idMoniteur, 
        PR.idPoney, 
        PERS.prenom || ' ' || PERS.nom AS moniteurNom
    FROM PARTICIPER PR
    JOIN SEANCE S ON PR.idSeance = S.idSeance
    JOIN PONEY P ON PR.idPoney = P.idPoney
    JOIN MONITEUR M ON S.idMoniteur = M.idMoniteur
    JOIN PERSONNE PERS ON M.idPersonne = PERS.idPersonne
    WHERE PR.idCl = ?
    ORDER BY S.dateDebut";
$stmtReservations = $pdo->prepare($sqlReservations);
$stmtReservations->execute([$_SESSION['user_id']]);
$reservations = $stmtReservations->fetchAll(PDO::FETCH_ASSOC);

// Préparation des données pour le calendrier
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$dateDebut = date('Y-m-d', strtotime("now +$offset week"));
$dateFin = date('Y-m-d', strtotime("now +$offset week +6 days"));

$heures = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

// Préparation des réservations sous forme de tableau pour accès rapide
$reservationMap = [];
foreach ($reservations as $reservation) {
    $jour = date('N', strtotime($reservation['dateDebut'])) - 1; // 0 = Lundi
    $heure = date('H:i', strtotime($reservation['dateDebut']));
    $reservationMap["$jour-$heure"] = $reservation;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - Centre Équestre</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }
        .nav {
            background-color: #00796b;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        .nav a:hover {
            opacity: 0.8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
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
        td.reserved {
            background-color: #bbdefb; /* Bleu clair */
            position: relative;
        }
        td.reserved form {
            position: absolute;
            bottom: 5px;
            left: 5px;
        }
        td.libre {
            background-color: #e8f5e9;
        }
        .week-nav {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .week-nav button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #00796b;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .week-nav button:disabled {
            background-color: #ccc;
        }
        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 15px 0;
        }
        .cancel-button {
            background-color: #e53935;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .cancel-button:hover {
            background-color: #c62828;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="<?= $isAdmin ? 'page_admin.php' : 'index.php' ?>">Accueil</a>
        <a href="calendar.php">Calendrier</a>
        <a href="reservation.php">Réservation</a>
        <a href="mes_reservations.php">Mes Réservations</a>
    </div>

    <h1 style="text-align: center; color: #00796b;">Mes Réservations</h1>
    <h2 style="text-align: center; color: #555;">Semaine du <?= date('d/m/Y', strtotime($dateDebut)) ?> au <?= date('d/m/Y', strtotime($dateFin)) ?></h2>

    <div class="week-nav">
        <a href="mes_reservations.php?offset=<?= $offset - 1 ?>">
            <button <?= $offset <= 0 ? 'disabled' : '' ?>>Semaine précédente</button>
        </a>
        <a href="mes_reservations.php?offset=<?= $offset + 1 ?>">
            <button>Semaine suivante</button>
        </a>
    </div>

    <div style="overflow-x: auto; padding: 0 20px;">
        <table>
            <tr>
                <th>Heure</th>
                <?php foreach ($jours as $jour): ?>
                    <th><?= $jour ?></th>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($heures as $heure): ?>
                <tr>
                    <td><?= $heure ?></td>
                    <?php foreach ($jours as $index => $jour): ?>
                        <?php
                        $reservationKey = "$index-$heure";
                        if (isset($reservationMap[$reservationKey])):
                            $reservation = $reservationMap[$reservationKey];
                            $poneyNom = htmlspecialchars($reservation['poneyNom']);
                            $moniteurNom = htmlspecialchars($reservation['moniteurNom']);
                            $idSeance = $reservation['idSeance'];
                            $idPoney = $reservation['idPoney'];
                        ?>
                            <td class="reserved">
                                <strong><?= $poneyNom ?></strong><br>
                                Moniteur : <?= $moniteurNom ?><br>
                                <form method="POST">
                                    <input type="hidden" name="idSeance" value="<?= $idSeance ?>">
                                    <input type="hidden" name="idPoney" value="<?= $idPoney ?>">
                                    <button class="cancel-button" type="submit" name="cancel_reservation">Annuler</button>
                                </form>
                            </td>
                        <?php else: ?>
                            <td class="libre"></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés.</p>
    </footer>
</body>
</html>
