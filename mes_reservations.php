<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if (isset($_POST['delete_reservation'])) {
    $idPoney = $_POST['delete_id_poney'];
    $idSeance = $_POST['delete_id_seance'];
    $deleteSql = "DELETE FROM PARTICIPER WHERE idPoney = ? AND idSeance = ? AND idCl = ?";
    $stmtDelete = $pdo->prepare($deleteSql);
    $stmtDelete->execute([$idPoney, $idSeance, $_SESSION['user_id']]);

    echo "<p style='color: green;'>Réservation supprimée avec succès !</p>";
    header("Location: mes_reservations.php"); // Evitrer derenvoyer le form
    exit;
}

// Le sréservations
$sqlReservations = "
    SELECT P.nomP, S.dateDebut, S.dateFin, PR.idPoney, PR.idSeance
    FROM PARTICIPER PR
    JOIN PONEY P ON PR.idPoney = P.idPoney
    JOIN SEANCE S ON PR.idSeance = S.idSeance
    WHERE PR.idCl = ?
    ORDER BY S.dateDebut
";
$stmtReservations = $pdo->prepare($sqlReservations);
$stmtReservations->execute([$_SESSION['user_id']]);
$reservations = $stmtReservations->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations</title>
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

        .les_reserv {
            padding: 20px;
            text-align: center;
            margin-top: 100px;
        }

        .liste_reserv {
            margin-top: 20px;
        }

        .une_reserv {
            background-color: #d7e9dc;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .une_reserv h3 {
            margin: 0;
            font-size: 18px;
        }

        .une_reserv p {
            margin: 5px 0;
        }

        .user-info {
            color: white;
            font-weight: bold;
        }

        button {
            background-color: #e53935;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
        }

        button:hover {
            background-color: #c62828;
        }
    </style>
</head>
<body>
    <div class="nav">
        <div>
            <a href="index.php">Accueil</a>
            <a href="calendar.php">Calendrier</a>
            <a href="reservation.php">Réservation</a>
            <a href="mes_reservations.php">Mes Réservations</a>
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

    <div class="les_reserv">
        <h1>Mes Réservations</h1>
        <?php if (empty($reservations)): ?>
            <p>Aucune réservation trouvée.</p>
        <?php else: ?>
            <div class="liste_reserv">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="une_reserv">
                        <h3>Poney : <?= htmlspecialchars($reservation['nomP']) ?></h3>
                        <p><strong>Date de début :</strong> <?= date("d/m/Y H:i", strtotime($reservation['dateDebut'])) ?></p>
                        <p><strong>Date de fin :</strong> <?= date("H:i", strtotime($reservation['dateFin'])) ?></p>

                        <!-- Suppr reservation -->
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
</body>
</html>
