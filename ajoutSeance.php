<?php
session_start();
require 'db.php';

if (!isset($_SESSION['est_admin']) || !$_SESSION['est_admin']) {
    header("Location: index.php"); 
    exit;
}

$message_cours = "";
$message_seance = "";

// Traitement du formulaire pour ajouter un cours
if (isset($_POST['ajouter_cours'])) {
    $id_cours = $_POST['id_cours'];
    $type_cours = htmlspecialchars(trim($_POST['type_cours']));

    try {
        // Vérifier si le cours existe déjà (par ID)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM COURS WHERE idCours = :id_cours");
        $stmt->execute([':id_cours' => $id_cours]);
        $coursExist = $stmt->fetchColumn();

        if ($coursExist > 0) {
            $message_cours = "Ce cours existe déjà.";
        } else {
            // Insertion du nouveau cours
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

// Traitement du formulaire pour ajouter une séance
if (isset($_POST['ajouter_seance'])) {
    $id_cours = $_POST['id_cours'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $duree = $_POST['duree'];
    $particulier = isset($_POST['particulier']) ? 1 : 0;  // Si coche, valeur 1
    $nb_personne_max = $_POST['nb_personne_max'];
    $niveau = htmlspecialchars(trim($_POST['niveau']));
    $id_moniteur = $_POST['id_moniteur'];

    try {
        // Insertion de la séance
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

        .form input, .form select, .form textarea, .form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }

        .form textarea {
            resize: vertical;
            height: 100px;
        }

        .form input:focus, .form select:focus, .form textarea:focus {
            border-color: #00796b;
            outline: none;
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

        hr {
            margin: 40px 0;
            border: 0;
            border-top: 1px solid #ddd;
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
        <a href="page_admin.php">Accueil</a>
        <a href="calendar.php">Calendrier</a>
        <a href="reservation.php">Réservation</a>
        <a href="mes_reservations.php">Mes Réservations</a>
    </div>

    <div class="container">
        <h1>Ajouter un Cours et une Séance</h1>

        <!-- Affichage des messages de succès ou d'erreur -->
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

        <!-- Formulaire pour ajouter un cours -->
        <form action="ajoutSeance.php" method="POST" class="form">
            <h2>Ajouter un Cours</h2>

            <label for="id_cours">ID du Cours :</label>
            <input type="number" id="id_cours" name="id_cours" required>

            <label for="type_cours">Type du Cours :</label>
            <input type="text" id="type_cours" name="type_cours" required>

            <button type="submit" name="ajouter_cours">Ajouter le Cours</button>
        </form>

        <hr>

        <form action="ajoutSeance.php" method="POST" class="form">
            <h2>Ajouter une Séance</h2>

            <label for="id_cours">Sélectionner le Cours :</label>
            <select id="id_cours" name="id_cours" required>
                <?php
                // Récupérer les cours existants pour le menu déroulant
                $query_cours = "SELECT idCours, typeCours FROM COURS";
                $result_cours = $pdo->query($query_cours);
                while ($row = $result_cours->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['idCours'] . "'>" . $row['typeCours'] . "</option>";
                }
                ?>
            </select>

            <label for="date_debut">Date de début (YYYY-MM-DD HH:MM:SS) :</label>
            <input type="datetime-local" id="date_debut" name="date_debut" required>

            <label for="date_fin">Date de fin (YYYY-MM-DD HH:MM:SS) :</label>
            <input type="datetime-local" id="date_fin" name="date_fin" required>

            <label for="duree">Durée (en heures) :</label>
            <select id="duree" name="duree" required>
                <option value="1">1 heure</option>
                <option value="2">2 heures</option>
            </select>

            <label for="particulier">Cours particulier :</label>
            <input type="checkbox" id="particulier" name="particulier" value="1">
            <label for="particulier">Oui</label>

            <label for="nb_personne_max">Nombre maximum de personnes :</label>
            <input type="number" id="nb_personne_max" name="nb_personne_max" required>

            <label for="niveau">Niveau :</label>
            <input type="text" id="niveau" name="niveau" required>

            <label for="id_moniteur">Sélectionner le Moniteur :</label>
            <select id="id_moniteur" name="id_moniteur" required>
            <?php
                // Requête pour récupérer les prénoms et noms des moniteurs avec leur id
                $query_moniteur = "
                SELECT m.idMoniteur, p.prenom, p.nom
                FROM MONITEUR m
                JOIN Personne p ON m.idMoniteur = p.idPersonne
                ";
                $stmt_moniteur = $pdo->query($query_moniteur);
                            
                // Boucle pour afficher les moniteurs dans le menu déroulant
                while ($row = $stmt_moniteur->fetch(PDO::FETCH_ASSOC)) {
                // Concaténation du prénom et du nom pour l'affichage dans le menu
                $moniteurFullName = $row['prenom'] . ' ' . $row['nom'];
                echo "<option value='" . $row['idMoniteur'] . "'>" . htmlspecialchars($moniteurFullName) . "</option>";
        }

    ?>
</select>

            </select>

            <button type="submit" name="ajouter_seance">Ajouter la Séance</button>
        </form>

    </div>

    <footer>
        <p>&copy; 2025, Tous droits réservés.</p>
    </footer>

</body>
</html>
