<?php
require_once '../model.php';
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $token = generateResetToken($email);

    if ($token) {
        // Ici tu peux envoyer le token par mail, pour l'exemple on l'affiche
        $success = "Un lien de réinitialisation a été généré : <a href='vue_reinitialisation.php?token=$token'>Réinitialiser le mot de passe</a>";
    } else {
        $error = "Email inconnu.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="../style/style_connexion.css">
</head>
<body>

<div class="login-container">
    <div class="logo"><img src="../images/logo.png" alt="Logo Divrsitee" class="logo"></div>
    <h2>Mot de passe oublié</h2>


    <?php
    if ($success) echo "<p style='color:green;'>$success</p>";
    if ($error) echo "<p style='color:red;'>$error</p>";
    ?>

    <form method="POST">
        <label for="email"><strong>Email</strong></label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Envoyer le lien</button>
    </form>

    <p>
        <a href="index.php">Retour à la connexion</a>
    </p>
</div>

</body>
</html>
