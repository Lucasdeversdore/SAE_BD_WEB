<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupérer le poids du client connecté
$sqlClient = "SELECT poids FROM PERSONNE WHERE idPersonne = ?";
$stmtClient = $pdo->prepare($sqlClient);
$stmtClient->execute([$_SESSION['user_id']]);
$client = $stmtClient->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    echo "<p style='color: red;'>Erreur : Client introuvable.</p>";
    exit;
}

$poidsClient = $client['poids'];

// Récupérer les poneys disponibles pour le poids du client
$sql = "SELECT idPoney, nomP, poidsMax, imagePoney FROM PONEY WHERE poidsMax >= ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$poidsClient]);
$poneys = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gérer la réservation
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPoney = $_POST['poney_id'];
    $idSeance = $_POST['seance_id'];
    $idClient = $_SESSION['user_id'];

    $sqlInsert = "INSERT INTO PARTICIPER (idSeance, idPoney, idCl) VALUES (?, ?, ?)";
    $stmtInsert = $pdo->prepare($sqlInsert);

    try {
        $stmtInsert->execute([$idSeance, $idPoney, $idClient]);
        $message = "<p style='color: green;'>Réservation effectuée avec succès !</p>";
    } catch (PDOException $e) {
        $message = "<p style='color: red;'>Erreur lors de la réservation : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - Centre Équestre Grand Galop</title>
    <style>
        /* Copier les styles généraux */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
            box-sizing: border-box;
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
        <h1>Réservez une séance</h1>
        <?= $message ?>
        <div class="poney-block">
            <?php foreach ($poneys as $poney): ?>
                <div class="les_poneys">
                    <img src="poney/<?= htmlspecialchars($poney['imagePoney']) ?>" alt="Image de <?= htmlspecialchars($poney['nomP']) ?>">
                    <h3><?= htmlspecialchars($poney['nomP']) ?></h3>
                    <form method="POST">
                        <input type="hidden" name="poney_id" value="<?= $poney['idPoney'] ?>">
                        <label for="seance_<?= $poney['idPoney'] ?>">Choisissez une séance :</label>
                        <select name="seance_id" id="seance_<?= $poney['idPoney'] ?>" required>
                            <option value="" disabled selected>-- Choisissez --</option>
                            <?php
                            $sqlSeances = "SELECT idSeance, dateDebut, dateFin FROM SEANCE 
                                           WHERE idSeance NOT IN (
                                               SELECT idSeance FROM PARTICIPER WHERE idPoney = ?
                                           )";
                            $stmtSeances = $pdo->prepare($sqlSeances);
                            $stmtSeances->execute([$poney['idPoney']]);
                            $seances = $stmtSeances->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($seances as $seance):
                                ?>
                                <option value="<?= $seance['idSeance'] ?>">
                                    Le <?= date('d/m/Y', strtotime($seance['dateDebut'])) ?> 
                                    de <?= date('H:i', strtotime($seance['dateDebut'])) ?>
                                    à <?= date('H:i', strtotime($seance['dateFin'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Réserver</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pied de page -->
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php">Contactez-nous</a></p>
    </footer>
</body>
</html>

