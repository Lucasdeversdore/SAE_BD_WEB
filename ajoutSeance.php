<?php
session_start();
require 'db.php';

if (!isset($_SESSION['est_admin']) || !$_SESSION['est_admin']) {
    header("Location: index.php");
    exit;
}

$message_cours = "";
$message_seance = "";

if (isset($_POST['ajouter_cours'])) {
    $id_cours = $_POST['id_cours'];
    $type_cours = htmlspecialchars(trim($_POST['type_cours']));

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM COURS WHERE idCours = :id_cours");
        $stmt->execute([':id_cours' => $id_cours]);
        $coursExist = $stmt->fetchColumn();

        if ($coursExist > 0) {
            $message_cours = "Ce cours existe déjà.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO COURS (idCours, typeCours) VALUES (:id_cours, :type_cours)");
            $stmt->execute([
                ':id_cours' => $id_cours,
                ':type_cours' => $type_cours
            ]);

            $message_cours = "Le cours a été ajouté avec succès.";
        }
    } catch (PDOException $e) {
        $message_cours = "Erreur lors de l'ajout du cours : " . $e->getMessage();
    }
}

if (isset($_POST['ajouter_seance'])) {
    $id_cours = $_POST['id_cours'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $duree = $_POST['duree'];
    $particulier = isset($_POST['particulier']) ? 1 : 0;
    $nb_personne_max = $_POST['nb_personne_max'];
    $niveau = htmlspecialchars(trim($_POST['niveau']));
    $id_moniteur = $_POST['id_moniteur'];

    try {
        $stmt = $pdo->prepare("INSERT INTO SEANCE (idCours, dateDebut, dateFin, duree, particulier, nbPersonneMax, niveau, idMoniteur) 
                               VALUES (:id_cours, :date_debut, :date_fin, :duree, :particulier, :nb_personne_max, :niveau, :id_moniteur)");
        $stmt->execute([
            ':id_cours' => $id_cours,
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin,
            ':duree' => $duree,
            ':particulier' => $particulier,
            ':nb_personne_max' => $nb_personne_max,
            ':niveau' => $niveau,
            ':id_moniteur' => $id_moniteur
        ]);

        $message_seance = "La séance a été ajoutée avec succès.";
    } catch (PDOException $e) {
        $message_seance = "Erreur lors de l'ajout de la séance : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Cours et une Séance</title>
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
            transition: opacity 0.3s ease;
        }

        .nav a:hover {
            opacity: 0.8;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #00796b;
            text-align: center;
            margin-bottom: 20px;
        }

        .form {
            display: flex;
            flex-direction: column;
        }

        .form label {
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .form input, .form select, .form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }

        .form button {
            background-color: #00796b;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form button:hover {
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

        footer {
            background-color: #00796b;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="nav">
        <div class="left-links">
            <a href="page_admin.php">Accueil</a>
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

    <div class="container">
        <h1>Ajouter un Cours et une Séance</h1>

        <?php if ($message_cours): ?>
            <div class="message <?php echo strpos($message_cours, 'Erreur') === false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message_cours); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($message_seance): ?>
            <div class="message <?php echo strpos($message_seance, 'Erreur') === false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message_seance); ?>
            </div>
        <?php endif; ?>

        <!-- Formulaires -->
        <!-- Ajouter un cours -->
        <form action="ajoutSeance.php" method="POST" class="form">
            <h2>Ajouter un Cours</h2>
            <label for="id_cours">ID du Cours :</label>
            <input type="number" id="id_cours" name="id_cours" required>
            <label for="type_cours">Type du Cours :</label>
            <input type="text" id="type_cours" name="type_cours" required>
            <button type="submit" name="ajouter_cours">Ajouter le Cours</button>
        </form>

        <!-- Ajouter une séance -->
        <form action="ajoutSeance.php" method="POST" class="form">
            <h2>Ajouter une Séance</h2>
            <label for="id_cours">Sélectionner le Cours :</label>
            <select id="id_cours" name="id_cours" required>
                <?php
                $query_cours = "SELECT idCours, typeCours FROM COURS";
                $result_cours = $pdo->query($query_cours);
                while ($row = $result_cours->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['idCours'] . "'>" . $row['typeCours'] . "</option>";
                }
                ?>
            </select>
            <!-- Autres champs pour la séance -->
            <label for="date_debut">Date de début :</label>
            <input type="datetime-local" id="date_debut" name="date_debut" required>
            <button type="submit" name="ajouter_seance">Ajouter la Séance</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025. Tous droits réservés.</p>
    </footer>
</body>
</html>
