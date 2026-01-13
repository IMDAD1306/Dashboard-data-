<?php
// Fichier: upload.php
require_once 'bdd.php'; 
header('Content-Type: application/json');

// --- Configuration ---
$dossier_uploads = 'JSON/'; // ⚠️ Vérifie que ce dossier existe bien sur ton serveur
$description_par_defaut = "Données chargées par l'utilisateur"; 

// 1. Vérification du dossier
if (!is_dir($dossier_uploads)) {
    mkdir($dossier_uploads, 0777, true);
}

// 2. Vérification du fichier reçu
if (!isset($_FILES['fichier_json']) || $_FILES['fichier_json']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Aucun fichier reçu ou erreur de téléchargement.']);
    exit; 
}

$file = $_FILES['fichier_json'];
$nom_fichier = basename($file['name']);
$chemin_stockage_serveur = $dossier_uploads . $nom_fichier;

try {
    // 3. Vérification des doublons
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM fichiers_json WHERE nom_fichier = ?");
    $stmt_check->execute([$nom_fichier]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => "Le fichier '{$nom_fichier}' existe déjà."]);
        exit;
    }

    // 4. Déplacement du fichier physique
    if (move_uploaded_file($file['tmp_name'], $chemin_stockage_serveur)) {
        
        // 5. Insertion en BDD (Bien inclure la colonne description)
        $stmt_insert = $pdo->prepare("INSERT INTO fichiers_json (nom_fichier, chemin_stockage, description, date_integration) VALUES (?, ?, ?, NOW())");
        $stmt_insert->execute([$nom_fichier, $chemin_stockage_serveur, $description_par_defaut]);
        
        echo json_encode([
            'success' => true, 
            'nom_fichier' => $nom_fichier,
            'message' => 'Fichier ajouté avec succès.'
        ]);
        exit; 
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du déplacement du fichier.']);
        exit;
    }

} catch (Exception $e) {
    // En cas d'erreur, on renvoie du JSON, JAMAIS du texte brut
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    exit;
}