<?php
require_once 'bdd.php'; // Connexion PDO

// ---------------------------
// FONCTIONS UTILISATEUR
// ---------------------------

function registerUser($email, $password) {
    global $pdo;

    // Vérifier si l'email existe déjà
    $requete = "SELECT id FROM users WHERE email = :email";
    $donnees = array(":email" => $email);
    $exec = $pdo->prepare($requete);
    $exec->execute($donnees);

    if ($exec->rowCount() > 0) {
        return false; // Email déjà utilisé
    }

    // Hacher le mot de passe
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insérer l'utilisateur
    $requete = "INSERT INTO users (email, password) VALUES (:email, :password)";
    $donnees = array(
        ":email" => $email,
        ":password" => $hashed
    );
    $exec = $pdo->prepare($requete);
    return $exec->execute($donnees);
}

function loginUser($email, $password) {
    global $pdo;

    $requete = "SELECT * FROM users WHERE email = :email";
    $donnees = array(":email" => $email);
    $exec = $pdo->prepare($requete);
    $exec->execute($donnees);
    $user = $exec->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

// ---------------------------
// FONCTIONS MOT DE PASSE OUBLIE
// ---------------------------

function generateResetToken($email) {
    global $pdo;

    // Vérifier que l'utilisateur existe
    $requete = "SELECT id FROM users WHERE email = :email";
    $donnees = array(":email" => $email);
    $exec = $pdo->prepare($requete);
    $exec->execute($donnees);
    $user = $exec->fetch();
    if (!$user) return false;

    // Générer token et date d'expiration
    $token = bin2hex(random_bytes(16));
    $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Sauvegarder token dans la BDD
    $requete = "UPDATE users SET reset_token = :token, token_expire = :expire WHERE email = :email";
    $donnees = array(
        ":token" => $token,
        ":expire" => $expire,
        ":email" => $email
    );
    $exec = $pdo->prepare($requete);
    $exec->execute($donnees);

    return $token;
}

function verifyResetToken($token) {
    global $pdo;

    $requete = "SELECT * FROM users WHERE reset_token = :token AND token_expire > NOW()";
    $donnees = array(":token" => $token);
    $exec = $pdo->prepare($requete);
    $exec->execute($donnees);

    return $exec->fetch(); // retourne l'utilisateur si valide
}

function updatePassword($userId, $newPassword) {
    global $pdo;

    // Récupérer l'ancien mot de passe
    $requete = "SELECT password FROM users WHERE id = :id";
    $donnees = array(":id" => $userId);
    $exec = $pdo->prepare($requete);
    $exec->execute($donnees);
    $user = $exec->fetch();

    // Vérifier que ce n'est pas le même mot de passe
    if (password_verify($newPassword, $user['password'])) {
        return false; // mot de passe identique
    }

    // Hacher et mettre à jour
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $requete = "UPDATE users SET password = :password, reset_token = NULL, token_expire = NULL WHERE id = :id";
    $donnees = array(
        ":password" => $hashed,
        ":id" => $userId
    );
    $exec = $pdo->prepare($requete);
    return $exec->execute($donnees);
}
?>
