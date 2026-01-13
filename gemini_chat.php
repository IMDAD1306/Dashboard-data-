<?php
// Fichier : gemini_chat.php

// Note : Il serait préférable de déplacer ce CSS intégré (navbar) dans un fichier .css à part.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIVRSITEE - Chat IA</title>
    
    <link rel="stylesheet" href="style/style_dashboard.css">
    
    <link rel="stylesheet" href="../style/style_ia.css"> 
    
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script> 
    
    <style>
    :root {
        /* Variables nécessaires pour l'entête */
        --secondary-text-color: #fff;
        --header-bg: #000000ff;
        /* ... autres variables si nécessaires pour les liens ... */
    }
    /* --- Style de la Barre de Navigation (Header) --- */
    .navbar {
        background-color: var(--header-bg); 
        color: var(--secondary-text-color);
        
        /* 🚀 AUGMENTATION DE LA HAUTEUR : 
           J'utilise 15px pour la marge interne en haut et en bas */
        padding: 15px 25px; 
        
        display: flex;
        justify-content: space-between; 
        align-items: center; 
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        z-index: 1000;
        white-space: nowrap; 
        
        /* 📏 POUR LA LARGEUR (Longueur) : 
           Assurez-vous qu'elle prend 100% de la largeur du parent */
        width: 100%; 
    }
    /* ... (Ajoutez tous les autres styles de .navbar, .logo, .nav-links, .logout-btn ici) ... */
    .logo img { width: 200px; margin-top: 5px; }
    .nav-links { display: flex; gap: 25px; margin-left: 40px; }
    .nav-links a { 
        color: var(--secondary-text-color); 
        text-decoration: none; 
        font-size: 1em; 
        
        /* 🚀 AUGMENTATION DE LA HAUTEUR DES LIENS : 
           Cela contribue à rendre la zone cliquable plus grande */
        padding: 10px 0; 
        display: inline-block; /* Nécessaire pour que le padding vertical s'applique */
        
        transition: color 0.3s, border-bottom 0.3s; 
    }
    .nav-links a:hover, .nav-links a.active { color: #00bcd4; border-bottom: 2px solid #00bcd4; }
    .logout-btn { background-color: #00bcd4; color: var(--secondary-text-color); border: none; padding: 10px 15px; font-size: 1.2em; cursor: pointer; border-radius: 4px; transition: background-color 0.3s; }
    .logout-btn:hover { background-color: #008c99; }
    @media (max-width: 768px) { .navbar { flex-direction: column; text-align: center; } .nav-links { padding: 10px 0; } }
</style>
</head>
<body>
<header class="navbar">
    <div class="logo"><img src="../images/logo.png" alt="Logo Divrsitee" class="logo"></div> 
    
    <nav class="nav-links">
        <a href="../vue/vue_accueil.php" >Accueil</a>
        <a href="gemini_chat.php" class="active" > Analyse IA</a> 
        <a href="../dashboard.php">Dashboard data</a> 
    </nav>
    <button class="logout-btn">
        &rarr;
    </button>
</header>
 
<div class="chat-container">
    <h1>Chat IA </h1>

    <div id="historique-chat">
        <div class="message ai-message">
            Bonjour ! Je suis IA. Comment puis-je vous aider aujourd'hui ?
        </div>
    </div>

    <div class="input-area">
    <textarea id="question" placeholder="Posez votre question, collez votre texte à résumer ou à classifier ici..."></textarea>
    
    <div class="boutons-actions">
        <button id="envoyer">Envoyer</button>
        <button onclick="resumerTexte()">Résumer</button>
       <button id="btn-classifier" onclick="classifierTexte()">Classifier</button>
    </div>
</div>
    </div>
</div> 

<script src="../chat.js"></script> 

</body>
</html>