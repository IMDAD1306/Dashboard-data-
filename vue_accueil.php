<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIVRSITEE - Dashboard</title>
    <link rel="stylesheet" href="../style/style_accueil.css"> 
    
    </head>
<body>

    <header class="navbar">
    <div class="logo"><img src="../images/logo.png" alt="Logo Divrsitee" class="logo"></div>
    <nav class="nav-links">
        <a href="#" class="active">Accueil</a>
        <a href="../IA/gemini_chat.php">Analyse IA</a>
        <a href="../dashboard.php">Dashboard data</a>
    </nav>
    
    <a href="../index.php" style="text-decoration: none;">
        <button class="logout-btn">
            &rarr;
        </button>
    </a>
</header>

    <div class="main-content">
        <div class="banner"></div>

        <section class="welcome-section">
            <p>Bienvenue sur votre Dashboard : Visualisez vos données en temps réel et accédez à l'Assistant IA pour obtenir des analyses instantanées.</p>
        </section>

        <section class="options-grid">
            
            <div class="option-card" onclick="window.location.href='../IA/gemini_chat.php'">
                <div class="icon-placeholder ai-icon">
                                    </div>
                <div class="card-label">Analyse IA</div>
            </div>

            <div class="option-card" onclick="window.location.href='../dashboard.php'"> 
                <div class="icon-placeholder dashboard-icon">
                                    </div>
                <div class="card-label">Dashboard data</div>
            </div>

        </section>
    </div>

    <footer class="footer">
        @2025Divrsitee tous droits réservés
    </footer>

    </body>
</html>