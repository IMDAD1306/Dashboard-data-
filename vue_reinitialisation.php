<?php
require_once '../model.php';
$success = $error = "";

// Récupération du token depuis l'URL
if (!isset($_GET['token'])) {
    die("Token manquant.");
}
$token = $_GET['token'];

// Vérifier que le token est valide
$user = verifyResetToken($token);
if (!$user) {
    die("Token invalide ou expiré.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if ($newPassword !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $updated = updatePassword($user['id'], $newPassword);
        if ($updated) {
            $success = "Mot de passe modifié avec succès. <a href='vue_connexion.php'>Connectez-vous</a>";
        } else {
            $error = "Le nouveau mot de passe ne peut pas être identique à l'ancien.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="../style/style_connexion.css">
</head>
<body>

<div class="login-container">
    <div class="logo"><img src="../images/logo.png" alt="Logo Divrsitee" class="logo"></div>
    <h2>Réinitialiser le mot de passe</h2>

    <?php
    if ($success) echo "<p style='color:green;'>$success</p>";
    if ($error) echo "<p style='color:red;'>$error</p>";
    ?>

    <?php if (!$success) : ?>
    <form method="POST">
        <label for="new_password"><strong>Nouveau mot de passe</strong></label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password"><strong>Confirmer le mot de passe</strong></label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Modifier le mot de passe</button>
    </form>
    <?php endif; ?>

    <p>
        <a href="../index.php">Retour à la connexion</a>
    </p>
</div>

</body>
</html>
