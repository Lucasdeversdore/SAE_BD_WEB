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

/* Style général */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f7fc;
    color: #333;
    box-sizing: border-box;
}

/* Barre de navigation */
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

/* Section Hero */
.hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 60px 40px;
    background-color: #e3f2fd;
    border-bottom: 5px solid #00796b;
}

.hero-content {
    max-width: 600px;
}

.hero-content h1 {
    font-size: 36px;
    color: #00796b;
    margin-bottom: 20px;
}

.hero-content p {
    font-size: 18px;
    color: #555;
    margin-bottom: 20px;
}

.hero-content button {
    background-color: #00796b;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.hero-content button:hover {
    background-color: #004d40;
}

.hero-image img {
    max-width: 100%;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Section Présentation */
.presentation {
    padding: 60px 40px;
    text-align: center;
}

.presentation h2 {
    font-size: 28px;
    color: #00796b;
    margin-bottom: 20px;
}

.presentation p {
    font-size: 18px;
    color: #555;
    line-height: 1.6;
    margin-bottom: 40px;
}

.features {
    display: flex;
    justify-content: space-around;
    gap: 20px;
}

.feature-item {
    max-width: 300px;
    padding: 20px;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.feature-item h3 {
    font-size: 20px;
    color: #004d40;
    margin-bottom: 15px;
}

.feature-item p {
    font-size: 16px;
    color: #555;
}

/* Section Témoignages */
.testimonials {
    padding: 60px 40px;
    background-color: #f9fbe7;
    text-align: center;
}

.testimonials h2 {
    font-size: 28px;
    color: #00796b;
    margin-bottom: 30px;
}

.testimonial-item {
    max-width: 600px;
    margin: 0 auto 20px auto;
}

.testimonial-item blockquote {
    font-style: italic;
    padding: 20px;
    border-left: 5px solid #00796b;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Pied de page */
footer {
    background-color: #00796b;
    color: white;
    text-align: center;
    padding: 15px 0;
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

    <!-- Section Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue au Poney Club Grand Galop</h1>
            <p>
                Venez découvrir un lieu unique dédié à l’équitation en plein cœur de la Sologne. 
                Profitez de cours encadrés par des moniteurs qualifiés, des poneys bienveillants et des infrastructures modernes.
            </p>
            <button onclick="location.href='reservation.php'">Réservez un cours</button>
        </div>
        <div class="hero-image">
            <img src="img/poney.png" alt="Enfant avec un poney">
        </div>
    </section>

    <!-- Section Présentation -->
    <section class="presentation">
        <h2>Le Poney Club Grand Galop</h2>
        <p>
            Fondé par un couple de passionnés d’équitation, notre poney club accueille depuis plusieurs années des cavaliers de tous niveaux.
            Situé dans un village pittoresque de Sologne, nous offrons un cadre naturel idéal pour apprendre et pratiquer l’équitation.
        </p>
        <div class="features">
            <div class="feature-item">
                <h3>Des cours pour tous</h3>
                <p>
                    Que vous soyez débutant ou cavalier confirmé, nos moniteurs vous accompagnent dans votre progression avec des cours adaptés à votre niveau.
                </p>
            </div>
            <div class="feature-item">
                <h3>Balades et randonnées</h3>
                <p>
                    Explorez la beauté de la Sologne lors de balades encadrées par des professionnels, pour des souvenirs inoubliables en pleine nature.
                </p>
            </div>
            <div class="feature-item">
                <h3>Pension pour poneys et chevaux</h3>
                <p>
                    Offrez à vos compagnons un cadre de vie sain et agréable, avec des soins attentifs et une alimentation équilibrée.
                </p>
            </div>
        </div>
    </section>

    <!-- Pied de page -->
    <footer>
        <p>Centre Équestre Grand Galop &copy; 2025. Tous droits réservés. <a href="contact.php">Contactez-nous</a></p>
    </footer>
</body>


</html>
