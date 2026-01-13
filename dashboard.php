<?php
// Fichier : dashboard.php
// Aucune balise HTML structurelle ici. On commence directement la page.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIVRSITEE - Dashboard</title>
    
    <link rel="stylesheet" href="style/style_dashboard.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            /* Variables nécessaires pour l'entête */
            --secondary-text-color: #fff;
            --header-bg: #000000ff;
        }

        /* --- Style de la Barre de Navigation (Header) --- */
        .navbar {
            background-color: var(--header-bg); 
            color: var(--secondary-text-color);
            
            /* 🚀 HAUTEUR AUGMENTÉE (comme demandé précédemment) */
            padding: 15px 25px; 
            
            display: flex;
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            white-space: nowrap; 
        }

        /* Logo */
        .logo img { width: 200px; margin-top: 5px; }

        /* Liens */
        .nav-links { display: flex; gap: 25px; margin-left: 40px; }

        .nav-links a {
            color: var(--secondary-text-color);
            text-decoration: none;
            font-size: 1em;
            
            /* 🚀 PADDING AUGMENTÉ pour la hauteur */
            padding: 10px 0;
            display: inline-block; 
            
            transition: color 0.3s, border-bottom 0.3s;
        }
        
        .nav-links a:hover, .nav-links a.active {
            color: #00bcd4;
            border-bottom: 2px solid #00bcd4;
        }

        /* Bouton Déconnexion */
        .logout-btn {
            background-color: #00bcd4;
            color: var(--secondary-text-color);
            border: none;
            padding: 10px 15px;
            font-size: 1.2em;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover { background-color: #008c99; }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .nav-links { padding: 10px 0; }
        }
    </style>
</head>
<body>
    
<header class="navbar">
    <div class="logo"><img src="images/logo.png" alt="Logo Divrsitee" class="logo"></div>
    
    <nav class="nav-links">
        <a href="vue/vue_accueil.php" >Accueil</a>
        <a href="IA/gemini_chat.php">Analyse IA</a>
        <a href="dashboard.php" class="active">Dashboard data</a> 
    </nav>
    <button class="logout-btn">
        &rarr;
    </button>
</header>

<h2>Importer un fichier JSON pour générer un graphique</h2>

<input type="file" id="fileInput" accept=".json">

<div class="table-container">
    <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>Fichier</th>
                <th>Date d'intégration</th>
                <th>Description</th>
                <th>Dashboard</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<button id="toggleGraphBtn" onclick="toggleGraph()" style="display:none;">Voir Graphique</button>
<p id="texteGraphique"></p>

<div class="graphique-cadre">
    <canvas id="myChart" width="200" height="100"></canvas>
</div>
<script src="script.js"></script>
</body>
</html>