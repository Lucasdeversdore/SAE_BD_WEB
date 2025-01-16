<?php
session_start();
require 'db.php';

if (!isset($_SESSION['est_admin']) || !$_SESSION['est_admin']) {
    header("Location: index.php");
    exit;
}

$message_personne = "";
$message_moniteur = "";

// Traitement pour ajouter une personne
if (isset($_POST['ajouter_personne'])) {
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $num_tel = htmlspecialchars(trim($_POST['num_tel']));
    $email = htmlspecialchars(trim($_POST['email']));
    $poids = htmlspecialchars(trim($_POST['poids']));
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO PERSONNE (prenom, nom, numTel, email, poids, mdp, est_admin) 
                               VALUES (:prenom, :nom, :numTel, :email, :poids, :mdp, 0)");
        $stmt->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':numTel' => $num_tel,
            ':email' => $email,
            ':poids' => $poids,
            ':mdp' => $mdp
        ]);

        $message_personne = "La personne a été ajoutée avec succès.";
    } catch (PDOException $e) {
        $message_personne = "Erreur lors de l'ajout de la personne : " . htmlspecialchars($e->getMessage());
    }
}

// Traitement pour transformer une personne en moniteur
if (isset($_POST['ajouter_moniteur'])) {
    $id_personne = $_POST['id_personne'];
    $date_naissance = $_POST['date_naissance'];

    try {
        $stmt = $pdo->prepare("SELECT MAX(idMoniteur) + 1 AS newId FROM MONITEUR");
        $stmt->execute();
        $newId = $stmt->fetchColumn() ?? 1;

        $stmt = $pdo->prepare("INSERT INTO MONITEUR (idMoniteur, idPersonne, dateDeNaissance) 
                               VALUES (:idMoniteur, :idPersonne, :dateNaissance)");
        $stmt->execute([
            ':idMoniteur' => $newId,
            ':idPersonne' => $id_personne,
            ':dateNaissance' => $date_naissance
        ]);

        $message_moniteur = "Le moniteur a été créé avec succès.";
    } catch (PDOException $e) {
        $message_moniteur = "Erreur lors de la création du moniteur : " . htmlspecialchars($e->getMessage());
    }
}

// Récupération des personnes qui ne sont pas déjà moniteurs
$query_personnes = "
SELECT idPersonne, prenom, nom
FROM PERSONNE
WHERE idPersonne NOT IN (SELECT idPersonne FROM MONITEUR)
";
$result_personnes = $pdo->query($query_personnes);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Personnes et Moniteurs</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }

        .nav {
            background-color: #0288d1;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
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
            color: #0288d1;
            text-align: center;
        }

        .form {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        .form label {
            font-weight: bold;
            margin: 10px 0 5px;
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
            background-color: #0288d1;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .form button:hover {
            background-color: #01579b;
        }

        .message {
            margin: 20px 0;
            text-align: center;
            font-size: 16px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }

        footer {
            background-color: #0288d1;
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
                <span>Bonjour, <?php echo htmlspecialchars($_SESSION['prenom']) . " " . htmlspecialchars($_SESSION['nom']); ?></span>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h1>Gestion des Personnes et des Moniteurs</h1>

        <!-- Message Personne -->
        <?php if ($message_personne): ?>
            <div class="message <?php echo strpos($message_personne, 'Erreur') === false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message_personne); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="form">
            <h2>Ajouter une Personne</h2>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="num_tel">Numéro de téléphone :</label>
            <input type="text" id="num_tel" name="num_tel" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="poids">Poids (kg) :</label>
            <input type="number" id="poids" name="poids" required>

            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>

            <button type="submit" name="ajouter_personne">Ajouter la Personne</button>
        </form>

        <!-- Message Moniteur -->
        <?php if ($message_moniteur): ?>
            <div class="message <?php echo strpos($message_moniteur, 'Erreur') === false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message_moniteur); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="form">
            <h2>Ajouter un Moniteur</h2>
            <label for="id_personne">Sélectionner une Personne :</label>
            <select id="id_personne" name="id_personne" required>
                <?php while ($row = $result_personnes->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $row['idPersonne']; ?>">
                        <?php echo htmlspecialchars($row['prenom'] . ' ' . $row['nom']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="date_naissance">Date de naissance :</label>
            <input type="date" id="date_naissance" name="date_naissance" required>

            <button type="submit" name="ajouter_moniteur">Ajouter le Moniteur</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Tous droits réservés.</p>
    </footer>
</body>
</html>
