<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Poids des clients
$sqlClient = "SELECT poids FROM PERSONNE WHERE idPersonne = ?";
$stmtClient = $pdo->prepare($sqlClient);
$stmtClient->execute([$_SESSION['user_id']]);
$client = $stmtClient->fetch(PDO::FETCH_ASSOC);
$poidsClient = $client['poids'];

// Verif des poids sur^pportés par le ponney
$sql = "SELECT idPoney, nomP, poidsMax, imagePoney FROM PONEY WHERE poidsMax >= ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$poidsClient]);
$poneys = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Quand on appuie sur réserver
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPoney = $_POST['poney_id'];
    $idSeance = $_POST['seance_id'];
    $idClient = $_POST['user_id'];

    $sql = "INSERT INTO PARTICIPER (idSeance, idPoney, idCl) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idSeance, $idPoney, $idClient]);
    $message = "<p style='color: green;'>Réservation effectuée avec succès !</p>";
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

        .block {
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

        .poney-block {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .les_poneys {
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            text-align: center;
        }

        .les_poneys img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .les_poneys h3 {
            margin-top: 10px;
        }

        .vide {
            color: red;
        }

        .message { 
            color: green;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            background-color: #dff0d8; 
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #d0e9c6;
        }

        
    </style>
</head>
<body>
    <!-- Header -->
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
    
    <div class="block">
        <h1>Réservation de cours</h1>
        <div class="poney-block">
            <?php
            // Verif si le clien est déja inscrit à une séance
            $sqlClientSeances = "
                SELECT idSeance
                FROM PARTICIPER
                WHERE idCl = ?
            ";
            $stmtClientSeances = $pdo->prepare($sqlClientSeances);
            $stmtClientSeances->execute([$_SESSION['user_id']]);
            $clientSeances = $stmtClientSeances->fetchAll(PDO::FETCH_ASSOC);
            $clientSeancesIds = array_column($clientSeances, 'idSeance');

            foreach ($poneys as $poney) {
                $cheminImage = 'poney/' . $poney['imagePoney'];

                echo '<div class="les_poneys">';
                echo '<img src="' . htmlspecialchars($cheminImage) . '" alt="Image de ' . htmlspecialchars($poney['nomP']) . '">';
                echo '<h3>' . htmlspecialchars($poney['nomP']) . '</h3>';

                // Check des poneys avec les consignes (Le implode permet de séparer chaque valeur par le caractere entre "")
                $sqlSeances = "
                    SELECT S.idSeance, S.dateDebut, S.dateFin
                    FROM SEANCE S
                    WHERE S.idSeance NOT IN (
                        SELECT P.idSeance
                        FROM PARTICIPER P
                        WHERE P.idPoney = ?
                    ) AND S.idSeance NOT IN (" . implode(",", $clientSeancesIds) . ") 
                    ORDER BY S.dateDebut ASC
                ";
                $stmtSeances = $pdo->prepare($sqlSeances);
                $stmtSeances->execute([$poney['idPoney']]);
                $seancesDisponibles = $stmtSeances->fetchAll(PDO::FETCH_ASSOC);

                // Verif séance et accès des données pour le post
                if (!empty($seancesDisponibles)) {
                    echo '<form method="POST" action="reservation.php">';
                    echo '<input type="hidden" name="poney_id" value="' . $poney['idPoney'] . '">';
                    echo '<input type="hidden" name="user_id" value="' . $_SESSION['user_id'] . '">';
                    echo '<label for="seance_' . $poney['idPoney'] . '">Sélectionnez une séance :</label>';
                    echo '<select name="seance_id" id="seance_' . $poney['idPoney'] . '" required>';
                    echo '<option value="" disabled selected>Choisir une séance</option>';
                    foreach ($seancesDisponibles as $seance) {
                        $dateDebut = date("d/m/Y H:i", strtotime($seance['dateDebut']));
                        $dateFin = date("H:i", strtotime($seance['dateFin']));
                        echo '<option value="' . $seance['idSeance'] . '">Du ' . $dateDebut . ' au ' . $dateFin . '</option>';
                    }
                    echo '</select>';
                    echo '<button type="submit">Réserver</button>';
                    echo '</form>';
                } else {
                    echo '<p class="vide">Aucune séance disponible</p>';
                }

                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Message de confirmation -->
        <div class="message">
            <?php if (isset($message)) {
                echo $message;
            } ?>
        </div>
    </div>
</body>
</html>
