<?php
// Fichier : update_description.php
require_once 'bdd.php'; // 🚨 Assurez-vous que le chemin vers votre fichier de connexion DB est correct
header('Content-Type: application/json');

// Vérification de sécurité
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'], $_POST['description'])) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide ou données manquantes.']);
    exit;
}

$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
$description = trim($_POST['description']);

if ($id === false) {
    echo json_encode(['success' => false, 'message' => 'ID de fichier invalide.']);
    exit;
}

try {
    // 1. Préparation de la requête de mise à jour (UPDATE)
    // Nous supposons que le nom de la table est 'fichiers_json' et la colonne ID est 'id'.
    $stmt = $pdo->prepare("UPDATE fichiers_json SET description = :description WHERE id = :id");
    
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id);
    
    $stmt->execute();

    // 2. Retourner le succès
    echo json_encode(['success' => true]);

} catch (\PDOException $e) {
    // 3. Gérer les erreurs de base de données
    error_log("DB Error in update_description: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => "Erreur DB: Impossible de sauvegarder la description."]);
}
?>