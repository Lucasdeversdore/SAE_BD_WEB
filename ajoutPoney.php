<?php
session_start();
require 'db.php';

// Vérification de l'accès admin
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
            // Vérification de l'existence du dossier "poney/"
            $targetDir = "poney/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $stmt = $pdo->query("SELECT IFNULL(MAX(idPoney), 0) + 1 AS nextId FROM PONEY");
            $nextId = $stmt->fetch(PDO::FETCH_ASSOC)['nextId'];

            // Verif de l'image
            $imageName = "poney" . $nextId . "." . pathinfo($imagePoney['name'], PATHINFO_EXTENSION);
            $targetFilePath = $targetDir . $imageName;

            // Déplacemer l'image
            if (move_uploaded_file($imagePoney['tmp_name'], $targetFilePath)) {
                // Insertion des données dans la base
                $stmt = $pdo->prepare("INSERT INTO PONEY (nomP, poidsMax, imagePoney) VALUES (:nomP, :poidsMax, :imagePoney)");
                $stmt->execute([
                    ':nomP' => $nomP,
                    ':poidsMax' => $poidsMax,
                    ':imagePoney' => $imageName
                ]);

                $message = "Poney ajouté avec succès !";
            } else {
                $message = "Erreur lors du téléchargement de l'image.";
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
        <div class="left-links">
            <a href="page_admin.php">Accueil</a>
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
