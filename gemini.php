<?php
// AJOUTER CES LIGNES POUR LE DÉBOGAGE
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$userPrompt = "";

// Tentez d'abord de récupérer les données JSON (si appel AJAX)
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['prompt'])) {
 $userPrompt = $input['prompt'];
} 
// Tentez ensuite de récupérer les données POST (si appel FormData)
elseif (isset($_POST["prompt"])) {
 $userPrompt = $_POST["prompt"];
}

// Vérification si la question a été trouvée
if (empty($userPrompt)) {
 echo json_encode(["error" => "Aucune question reçue dans le prompt.", "debug_input" => $input, "debug_post" => $_POST]);
 exit;
}

// Votre clé API
$apiKey = "AIzaSyDxldCBl_j5RGlsZ3UoaKsWQm59XB82knY"; 

// Endpoint : Version stable 'v1' et modèle 'gemini-2.5-flash'
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$apiKey}";

// Le format de l'objet 'contents' pour l'API Gemini
$data = [
 "contents" => [
 [
 "role" => "user",
"parts" => [
["text" => $userPrompt]
 ]
 ]
 ]
];

$curl = curl_init($url);
curl_setopt_array($curl, [
 CURLOPT_RETURNTRANSFER => true,
 CURLOPT_POST => true,
 CURLOPT_POSTFIELDS => json_encode($data),
 CURLOPT_HTTPHEADER => [
 "Content-Type: application/json"
],
    // ------------------------------------------------------------------
    // 💡 AJOUT DES PARAMÈTRES DE TIMEOUT POUR ÉVITER LE 503
    // ------------------------------------------------------------------
    CURLOPT_CONNECTTIMEOUT => 5, // Tente de connecter rapidement (5 secondes max)
    CURLOPT_TIMEOUT => 30        // Laisse 30 secondes pour le traitement du gros texte
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Gérer les erreurs HTTP
if ($httpCode !== 200) {
echo json_encode([
"error" => "Erreur API : Code HTTP {$httpCode}",
"details" => json_decode($response)
 ]);
 exit;
}

// Renvoyer la réponse JSON brute de l'API
echo $response;
?>