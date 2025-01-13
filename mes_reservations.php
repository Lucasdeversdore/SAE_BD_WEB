<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupérer les réservations
$sql = "
    SELECT PONEY.nomP, SEANCE.dateDebut, SEANCE.dateFin, PARTICIPER.idPoney, PARTICIPER.idSeance
    FROM PARTICIPER
    INNER JOIN PONEY ON PARTICIPER.idPoney = PONEY.idPoney
    INNER JOIN SEANCE ON PARTICIPER.idSeance = SEANCE.idSeance
    WHERE PARTICIPER.idCl = ?
    ORDER BY SEANCE.dateDebut ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gérer la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reservation'])) {
    $idPoney = $_POST['idPoney'];
    $idSeance = $_POST['idSeance'];

    $sqlDelete = "DELETE FROM PARTICIPER WHERE idSeance = ? AND idPoney = ? AND idCl = ?";
    $stmtDelete = $pdo->prepare($sqlDelete);

    try {
        $stmtDelete->execute([$idSeance, $idPoney, $_SESSION['user_id']]);
        header("Location: mes_reservations.php");
        exit;
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Erreur lors de la suppression : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations</title>
    <style>
        /* Style général */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Conteneur principal */
        .container {
            flex: 1;
            padding: 20px;
        }

        /* Navigation */
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
            padding: 20px;
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
            margin-top: auto;
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

    <!-- Conteneur principal -->
    <div class="container">
        <div class="reservations-section">
            <h1>Mes Réservations</h1>
            <?php if (empty($reservations)): ?>
                <p>Aucune réservation trouvée.</p>
            <?php else: ?>
                <div class="liste-reservations">
                    <?php foreach ($reservations as $reservation): ?>
                        <div class="reservation-card">
                            <h3>Poney : <?= htmlspecialchars($reservation['nomP']) ?></h3>
                            <p><strong>Date :</strong> 
                                Le <?= date('d/m/Y', strtotime($reservation['dateDebut'])) ?> 
                                de <?= date('H:i', strtotime($reservation['dateDebut'])) ?>
                                à <?= date('H:i', strtotime($reservation['dateFin'])) ?>
                            </p>
                            <form method="POST">
                                <input type="hidden" name="idPoney" value="<?= $reservation['idPoney'] ?>">
                                <input type="hidden" name="idSeance" value="<?= $reservation['idSeance'] ?>">
                                <button type="submit" name="delete_reservation">Annuler</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pied de page -->
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php">Contactez-nous</a></p>
    </footer>
</body>
</html>

