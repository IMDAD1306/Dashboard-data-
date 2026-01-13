<?php
session_start();
require_once 'model.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $user = loginUser($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header("Location: vue/vue_accueil.php");
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style/style_connexion.css">
</head>
<body>

<div class="login-container">
 

    <div class="logo-container">
        <img src="images/logo.png" alt="Logo Divrsitee" class="logo">
    </div>
    <h2>CONNECTEZ-VOUS</h2>

    <?php if (isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>

    <form method="POST">
        <label for="email"><strong>Email</strong></label>
        <input type="email" id="email" name="email" required>

        <label for="password"><strong>Mot de passe :</strong></label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Connexion</button>
    </form>

    <p>
        Vous n'avez pas de compte ?
        <a href="vue/vue_inscription.php">Inscrivez-vous</a>
    </p>

    <p>
        <a href="vue/vue_mdp_oublie.php">Mot de passe oublié ?</a>
    </p>
</div>

</body>
</html>

