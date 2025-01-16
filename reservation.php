<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Vérifie si l'utilisateur est administrateur
$isAdmin = isset($_SESSION['est_admin']);

require 'db.php';

$sqlClient = "SELECT poids FROM PERSONNE WHERE idPersonne = ?";
$stmtClient = $pdo->prepare($sqlClient);
$stmtClient->execute([$_SESSION['user_id']]);
$client = $stmtClient->fetch(PDO::FETCH_ASSOC);
$poidsClient = $client['poids'];

$sql = "SELECT idPoney, nomP, poidsMax, imagePoney FROM PONEY WHERE poidsMax >= ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$poidsClient]);
$poneys = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPoney = $_POST['poney_id'];
    $idSeance = $_POST['seance_id'];
    $idClient = $_POST['user_id'];

    $sql = "INSERT INTO PARTICIPER (idSeance, idPoney, idCl) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idSeance, $idPoney, $idClient]);

    $message = "Réservation réussie !";
}

$sqlReservations = "
    SELECT S.dateDebut, S.dateFin
    FROM SEANCE S
    INNER JOIN PARTICIPER P ON S.idSeance = P.idSeance
    WHERE P.idCl = ?
";
$stmtReservations = $pdo->prepare($sqlReservations);
$stmtReservations->execute([$_SESSION['user_id']]);
$reservationsUtilisateur = $stmtReservations->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - Centre Équestre</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin: 15px 20px 0;
            font-weight: bold;
            border: 1px solid #d0e9c6;
        }

        .block {
            padding: 40px 20px;
            text-align: center;
            flex: 1;
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
            width: 250px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .les_poneys img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .les_poneys h3 {
            margin-top: 10px;
            color: #004d40;
        }

        .les_poneys form {
            margin-top: 10px;
        }

        form label, select, button {
            display: block;
            margin-bottom: 10px;
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
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #004d40;
        }

        .vide {
            color: red;
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

    <?php if ($message): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="block">
        <h1>Réservez une séance</h1>
        <div class="poney-block">
            <?php foreach ($poneys as $poney): ?>
                <div class="les_poneys">
                    <img src="<?= 'poney/' . htmlspecialchars($poney['imagePoney']) ?>" alt="Image de <?= htmlspecialchars($poney['nomP']) ?>">
                    <h3><?= htmlspecialchars($poney['nomP']) ?></h3>
                    <?php
                    $sqlSeances = "
                        SELECT S.idSeance, S.dateDebut, S.dateFin
                        FROM SEANCE S
                        WHERE S.idSeance NOT IN (
                            SELECT P.idSeance
                            FROM PARTICIPER P
                            WHERE P.idPoney = ?
                        ) 
                        AND NOT EXISTS (
                            SELECT 1
                            FROM PARTICIPER P
                            JOIN SEANCE S2 ON P.idSeance = S2.idSeance
                            WHERE P.idCl = ?
                            AND (
                                (S.dateDebut < S2.dateFin AND S.dateFin > S2.dateDebut)
                            )
                        )
                        ORDER BY S.dateDebut ASC
                    ";
                    $stmtSeances = $pdo->prepare($sqlSeances);
                    $stmtSeances->execute([$poney['idPoney'], $_SESSION['user_id']]);
                    $seancesDisponibles = $stmtSeances->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($seancesDisponibles)): ?>
                        <form method="POST" action="reservation.php">
                            <input type="hidden" name="poney_id" value="<?= $poney['idPoney'] ?>">
                            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                            <label for="seance_<?= $poney['idPoney'] ?>">Sélectionnez une séance :</label>
                            <select name="seance_id" id="seance_<?= $poney['idPoney'] ?>" required>
                                <?php foreach ($seancesDisponibles as $seance): ?>
                                    <option value="<?= $seance['idSeance'] ?>">
                                        <?= 'Le ' . date("d/m/Y", strtotime($seance['dateDebut'])) . ' de ' . date("H:i", strtotime($seance['dateDebut'])) . ' à ' . date("H:i", strtotime($seance['dateFin'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Réserver</button>
                        </form>
                    <?php else: ?>
                        <p class="vide">Aucune séance disponible</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php">Contactez-nous</a></p>
    </footer>
</body>
</html>
