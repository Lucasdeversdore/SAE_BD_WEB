<?php
session_start();
require 'db.php';

if (!isset($_SESSION['est_admin']) || !$_SESSION['est_admin']) {
    header("Location: index.php"); 
    exit;
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomP = htmlspecialchars(trim($_POST['nomP']));
    $poidsMax = floatval($_POST['poidsMax']);
    $imagePoney = $_FILES['imagePoney'];

    if ($nomP && $poidsMax > 0 && $imagePoney['tmp_name']) {
        try {

            $imageName = basename($imagePoney['name']);
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM PONEY WHERE imagePoney = :imageName");
            $stmt->execute([':imageName' => $imageName]);
            $imageExists = $stmt->fetchColumn();

            if ($imageExists > 0) {
                $message = "Cette image existe déjà dans la base de données.";
            } else {
                $stmt = $pdo->query("SELECT IFNULL(MAX(idPoney), 0) + 1 AS nextId FROM PONEY");
                $nextId = $stmt->fetch(PDO::FETCH_ASSOC)['nextId'];
                $newImageName = "poney" . $nextId . "." . pathinfo($imageName, PATHINFO_EXTENSION);
                $targetFilePath = $newImageName;

                if (move_uploaded_file($imagePoney['tmp_name'], $targetFilePath)) {
                    $stmt = $pdo->prepare("INSERT INTO PONEY (nomP, poidsMax, imagePoney) VALUES (:nomP, :poidsMax, :imagePoney)");
                    $stmt->execute([
                        ':nomP' => $nomP,
                        ':poidsMax' => $poidsMax,
                        ':imagePoney' => $targetFilePath
                    ]);

                    $message = "Poney ajouté avec succès !";
                } else {
                    $message = "Erreur lors de l'enregistrement de l'image.";
                }
            }
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Poney</title>
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
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        .container {
            max-width: 600px;
            margin: 100px auto 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #00796b;
            text-align: center;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        form input, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        form input:focus {
            border-color: #00796b;
            outline: none;
        }

        button {
            background-color: #00796b;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #004d40;
        }

        .message {
            margin-top: 20px;
            font-size: 16px;
            text-align: center;
        }

        .message.error {
            color: red;
        }

        .message.success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="page_admin.php">Accueil</a>
        <a href="calendar.php">Calendrier</a>
        <a href="reservation.php">Réservation</a>
        <a href="mes_reservations.php">Mes Réservations</a>
    </div>
    <div class="container">
        <h2>Ajouter un Poney</h2>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Erreur') === false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="nomP">Nom du Poney :</label>
            <input type="text" id="nomP" name="nomP" required>

            <label for="poidsMax">Poids Maximum (en kg) :</label>
            <input type="number" id="poidsMax" name="poidsMax" step="1" required>

            <label for="imagePoney">Image du Poney :</label>
            <input type="file" id="imagePoney" name="imagePoney" accept="image/*" required>

            <button type="submit">Ajouter le Poney</button>
        </form>
    </div>
</body>
</html>
