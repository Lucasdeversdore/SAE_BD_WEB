<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre Équestre Orléans</title>
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
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        .user-info {
            color: white;
            font-weight: bold;
            margin-right: 15px;
        }

        .container {
            text-align: center;
            margin-top: 50px;
        }

        .logo {
            margin: 20px 0;
        }

        .logo img {
            width: 200px;
            height: auto;
        }

        .hero-image {
            width: 100%;
            height: 300px;
            background: url('horse.jpg') no-repeat center center;
            background-size: cover;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
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
                    Bonjour, <?php echo htmlspecialchars($_SESSION['prenom']) . " " . htmlspecialchars($_SESSION['nom']); ?>
                </span>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <div class="logo">
            <img src="logo.png" alt="Logo Centre Équestre Orléans">
        </div>
        <div class="hero-image"></div>
        <h1>Bienvenue au Centre Équestre Poney Club d'Orléans</h1>
        <p>Réservez dès maintenant vos cours de poney et vivez une expérience inoubliable.</p>
    </div>
</body>
</html>
