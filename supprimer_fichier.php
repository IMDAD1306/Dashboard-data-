<?php
// Fichier: supprimer_fichier.php

require_once 'bdd.php';
header('Content-Type: application/json');

// 1. Vérification de l'ID reçu
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de fichier invalide.']);
    exit;
}

$idFichier = $_POST['id'];

try {
    // 2. Récupérer le chemin de stockage avant de supprimer la ligne BDD
    $stmt = $pdo->prepare("SELECT chemin_stockage FROM fichiers_json WHERE id = ?");
    $stmt->execute([$idFichier]);
    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resultat) {
        // La ligne n'existe pas, on arrête ici
        echo json_encode(['success' => false, 'message' => 'Fichier non trouvé en base de données.']);
        exit;
    }

    $chemin_stockage = $resultat['chemin_stockage'];

    // 3. Supprimer la ligne dans la BDD
    $stmt = $pdo->prepare("DELETE FROM fichiers_json WHERE id = ?");
    $stmt->execute([$idFichier]);
    
    $message = 'Entrée BDD supprimée.';

    // 4. Supprimer le fichier physique du serveur
    if (file_exists($chemin_stockage)) {
        if (unlink($chemin_stockage)) {
            $message = 'Fichier supprimé (BDD et serveur).';
        } else {
            // Problème de permissions de suppression (vérifiez le CHMOD de votre dossier JSON/)
            $message = 'Entrée BDD supprimée, mais ERREUR : impossible de supprimer le fichier physique (permission?).';
        }
    } else {
        // Fichier physique non trouvé, ce n'est pas grave si l'entrée BDD est partie
        $message = 'Entrée BDD supprimée. Fichier physique déjà manquant sur le serveur.';
    }

    // Succès final
    echo json_encode(['success' => true, 'message' => $message]);

} catch (\PDOException $e) {
    // Erreur de base de données
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur BDD lors de la suppression.']);
}

exit; 
// AUCUNE BALISE DE FERMETURE ?>