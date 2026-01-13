<?php
require_once "../bdd.php"; 
session_start();

$message = ""; // La boîte est vide au début

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $message = "<p style='color:red; text-align:center;'>Les mots de passe ne correspondent pas.</p>";
    } else {
        // ... (ton code de vérification email) ...
        // Si tout est bon :
        $message = "<p style='color:green; text-align:center;'>Inscription réussie ! <a href='../index.php'>Connectez-vous</a></p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Client</title>
    <link rel="stylesheet" href="../style/style_inscription.css"> 
</head>
<body>

<div class="container">
    
    <div class="left-panel">
        <div class="logo">
            <img src="../images/logo.png" alt="Logo Divrsitee" class="logo">
        </div>
    </div>

    <div class="right-panel">
        <div class="right-panel">
    <h2>Inscription Client</h2>

    <?php echo $message; ?> 

    <form method="POST">
       ```


        <h2>Inscription Client</h2>

        <form method="POST">
    
    <div class="input-group" data-label="EMAIL">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="input-group" data-label="MOT DE PASSE">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="input-group" data-label="CONFIRMEZ MOT DE PASSE">
        <label for="confirm-password">Confirmez mot de passe</label>
        <input type="password" id="confirm-password" name="confirm_password" required>
    </div>

    <button type="submit" name="register">S'INSCRIRE</button>
</form>

        <div class="login-section">
            <h2>DÉJÀ UN COMPTE ??</h2>
            <a href="../index.php">
                <button>CONNECTEZ-VOUS</button>
            </a>
        </div>
    </div>

</div>

</body>
</html>