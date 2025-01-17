<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Vérifie si l'utilisateur est administrateur
$isAdmin = isset($_SESSION['est_admin']);

// Suppression de réservation
if (isset($_POST['delete_reservation'])) {
    $idPoney = $_POST['delete_id_poney'];
    $idSeance = $_POST['delete_id_seance'];
    $deleteSql = "DELETE FROM PARTICIPER WHERE idPoney = ? AND idSeance = ? AND idCl = ?";
    $stmtDelete = $pdo->prepare($deleteSql);
    $stmtDelete->execute([$idPoney, $idSeance, $_SESSION['user_id']]);

    echo "<p style='color: green;'>Réservation supprimée avec succès !</p>";
    header("Location: mes_reservations.php");
    exit;
}

// Récupération des réservations
$sqlReservations = "
    SELECT P.nomP, S.dateDebut, S.dateFin, PR.idPoney, PR.idSeance
    FROM PARTICIPER PR
    JOIN PONEY P ON PR.idPoney = P.idPoney
    JOIN SEANCE S ON PR.idSeance = S.idSeance
    WHERE PR.idCl = ?
    ORDER BY S.dateDebut";
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

        html, body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }


        .nav {
            background-color: #00796b;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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


        .les_reserv {
            padding: 40px 20px;
            text-align: center;
            flex: 1;
        }

        .liste_reserv {
            margin-top: 20px;
        }

        .une_reserv {
            background-color: #d7e9dc;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: left;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .une_reserv h3 {
            font-size: 20px;
            color: #004d40;
            margin: 0;
        }

        .une_reserv p {
            margin: 8px 0;
        }

        button {
            background-color: #e53935;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #c62828;
        }


        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <div class="nav">
        <div>
            <a href="<?= $isAdmin ? 'page_admin.php' : 'index.php' ?>">Accueil</a>
            <a href="calendar.php">Calendrier</a>
            <a href="reservation.php">Réservation</a>
            <a href="mes_reservations.php">Mes Réservations</a>
        </div>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-info">
                    Bonjour, <?= htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']) ?>
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
                        <p><strong>Date de fin :</strong> <?= date("d/m/Y H:i", strtotime($reservation['dateFin'])) ?></p>

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
    
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php">Contactez-nous</a></p>
    </footer>
</body>
</html>
