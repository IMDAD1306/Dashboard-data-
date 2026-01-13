<?php
// Fichier: fetch_fichiers.php

// 1. Connexion à la base de données
// Ce fichier doit être dans le même dossier que bdd.php
require_once 'bdd.php'; 

// 2. Définition du type de contenu
// C'est CRITIQUE pour que le JavaScript comprenne la réponse
header('Content-Type: application/json');

// 3. Récupération des données
try {
    // Sélectionne toutes les colonnes nécessaires, ordonnées par date d'intégration
    $stmt = $pdo->query("SELECT id, nom_fichier, date_integration, description, chemin_stockage 
                         FROM fichiers_json 
                         ORDER BY date_integration DESC");

    $fichiers = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    // 4. Envoi de la réponse JSON au navigateur
    echo json_encode($fichiers);
    
} catch (\PDOException $e) {
    // Gestion des erreurs BDD
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données.', 'message' => $e->getMessage()]);
}

// 🛑 Arrêt immédiat pour garantir l'intégrité du flux JSON
exit;

// AUCUNE BALISE DE FERMETURE ?> APRÈS CE BLOC