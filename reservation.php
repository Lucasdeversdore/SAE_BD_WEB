<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if (!isset($_GET['idSeance'])) {
    die("Erreur : aucune séance sélectionnée.");
}

$idSeance = (int)$_GET['idSeance'];

// Vérification si l'utilisateur a déjà réservé cette séance
$sqlCheckReservation = "
    SELECT 1
    FROM PARTICIPER
    WHERE idSeance = ? AND idCl = ?
";
$stmtCheckReservation = $pdo->prepare($sqlCheckReservation);
$stmtCheckReservation->execute([$idSeance, $_SESSION['user_id']]);
$reservationExistante = $stmtCheckReservation->fetchColumn();

if ($reservationExistante) {
    die("Erreur : vous avez déjà réservé cette séance.");
}

// Vérification des poneys disponibles
$sqlPoneys = "
    SELECT P.idPoney, P.nomP, P.poidsMax, P.imagePoney
    FROM PONEY P
    WHERE P.idPoney NOT IN (
        SELECT idPoney
        FROM PARTICIPER
        WHERE idSeance = ?
    )
";
$stmtPoneys = $pdo->prepare($sqlPoneys);
$stmtPoneys->execute([$idSeance]);
$poneys = $stmtPoneys->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPoney = $_POST['idPoney'];

    $sqlInsert = "INSERT INTO PARTICIPER (idSeance, idPoney, idCl) VALUES (?, ?, ?)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([$idSeance, $idPoney, $_SESSION['user_id']]);

    header("Location: calendar.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
        }

        .nav {
            width: 100%;
            background-color: #00796b;
            color: white;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 16px;
            margin: 0 10px;
            transition: opacity 0.3s ease;
        }

        .nav a:hover {
            opacity: 0.8;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        h1, h2, h3 {
            text-align: center;
            color: #00796b;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .poney {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background:rgb(238, 255, 247);
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        }

        .poney:hover {
            transform: scale(1.02);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
            background-color: #e3f2fd;
        }

        .poney img {
            width: 120px;
            height: 120px;
            margin-right: 15px;
            border-radius: 10px;
            border: 2px solid #ddd;
            object-fit: cover;
        }

        .poney label {
            flex: 1;
            font-size: 1rem;
            display: flex;
            flex-direction: column;
        }

        .poney input[type="radio"] {
            display: none; /* Masque les boutons radio */
        }

        .poney.selected {
            border: 2px solid #00796b;
            background-color: #e3f2fd;
        }

        button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #004d40;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .poney {
                flex-direction: column;
                text-align: center;
            }

            .poney img {
                margin-bottom: 10px;
                width: 100px;
                height: 100px;
            }

            .poney label {
                font-size: 1rem;
                align-items: center;
            }

            button {
                font-size: 1rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="calendar.php">Retour au calendrier</a>
    </div>

    <div class="container">
        <h1>Réserver une séance</h1>

        <form method="POST" id="reservationForm">
            <h3>Choisissez un poney :</h3>
            <?php foreach ($poneys as $poney): ?>
                <div class="poney" data-poney-id="<?= $poney['idPoney'] ?>">
                    <img src="poney/<?= htmlspecialchars($poney['imagePoney']) ?>" alt="<?= htmlspecialchars($poney['nomP']) ?>">
                    <label>
                        <span><?= htmlspecialchars($poney['nomP']) ?> (Poids max : <?= htmlspecialchars($poney['poidsMax']) ?> kg)</span>
                        <input type="radio" name="idPoney" value="<?= $poney['idPoney'] ?>" required>
                    </label>
                </div>
            <?php endforeach; ?>

            <button type="submit">Réserver</button>
        </form>
    </div>

    <script>
        // Sélection des cartes poneys
        document.querySelectorAll('.poney').forEach(card => {
            card.addEventListener('click', function () {
                // Supprimer la classe "selected" des autres cartes
                document.querySelectorAll('.poney').forEach(c => c.classList.remove('selected'));
                // Ajouter la classe "selected" à la carte cliquée
                this.classList.add('selected');
                // Cocher le bouton radio correspondant
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>
</html>
