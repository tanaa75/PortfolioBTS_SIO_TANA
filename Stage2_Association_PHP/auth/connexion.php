<?php
/**
 * ===========================================
 * PAGE DE CONNEXION MEMBRE
 * ===========================================
 * 
 * Cette page permet aux membres de se connecter à leur espace.
 * Elle vérifie les identifiants dans la table 'membres' de la base.
 * 
 * Différence avec login.php :
 * - connexion.php = pour les MEMBRES (utilisateurs normaux)
 * - login.php = pour les ADMINISTRATEURS
 */

// Démarrage de la session PHP
session_start();

// Connexion à la base de données
require_once '../includes/db.php';

// Inclusion des fonctions de sécurité
require_once '../includes/security.php';

// Variable pour stocker les erreurs
$error = "";

// ========== TRAITEMENT DU FORMULAIRE ==========
// On vérifie si le formulaire a été soumis (méthode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $password = $_POST['mot_de_passe'];
    
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || !verifier_token_csrf($_POST['csrf_token'])) {
        $error = "Erreur de sécurité. Veuillez réessayer.";
    }
    // Vérification si l'utilisateur est bloqué
    elseif (est_connexion_bloquee($email)) {
        $minutes = ceil(temps_restant_blocage($email) / 60);
        $error = "Trop de tentatives. Réessayez dans $minutes minutes.";
    }
    else {
        // Recherche du membre dans la base de données par son email
        $stmt = $pdo->prepare("SELECT * FROM membres WHERE email = ?");
        $stmt->execute([$email]);
        $membre = $stmt->fetch();

        // Vérification du mot de passe avec password_verify()
        if ($membre && password_verify($password, $membre['mot_de_passe'])) {
            
            // Connexion réussie : réinitialiser les tentatives
            reinitialiser_tentatives($email);
            regenerer_token_csrf();
            
            // Stocker les infos en session
            $_SESSION['membre_id'] = $membre['id'];
            $_SESSION['membre_nom'] = $membre['nom'];
            $_SESSION['membre_email'] = $membre['email'];
            
            // Redirection vers la page d'accueil
            header("Location: ../index.php");
            exit();
            
        } else {
            // Échec : enregistrer la tentative
            enregistrer_tentative_echec($email);
            $error = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connectez-vous à votre espace membre Aujourd'hui vers Demain.">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion Membre | Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    
    <!-- Carte de connexion -->
    <div class="card card-custom shadow-lg p-4 p-md-5">
        
        <!-- En-tête avec logo -->
        <div class="text-center mb-4">
            <img src="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" width="60" class="mb-3">
            <h3 class="fw-bold text-success">Espace Membre</h3>
            <p class="text-muted small">Heureux de vous revoir !</p>
        </div>
        
        <!-- Affichage des erreurs éventuelles -->
        <?php if($error): ?>
            <div class="alert alert-danger text-center shadow-sm border-0"><?= $error ?></div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST">
            <?= champ_csrf() ?>
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control rounded-4" id="floatingInput" placeholder="name@example.com" required>
                <label for="floatingInput">Adresse Email</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" name="mot_de_passe" class="form-control rounded-4" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">Mot de passe</label>
            </div>
            
            <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-3 fs-5 shadow hover-scale">
                Se connecter
            </button>
        </form>
        
        <!-- Mot de passe oublié -->
        <div class="text-center mt-3">
            <a href="mot_de_passe_oublie.php" class="text-decoration-none text-muted small">
                <i class="bi bi-key"></i> Mot de passe oublié ?
            </a>
        </div>

        <!-- Liens supplémentaires -->
        <div class="d-grid gap-2 text-center mt-4">
            <!-- Lien vers inscription -->
            <a href="inscription.php" class="btn btn-outline-primary rounded-pill py-2 fw-bold border-2">
                Pas de compte ? Créer un compte
            </a>

            <!-- Retour à l'accueil -->
            <a href="../index.php" class="text-decoration-none text-secondary mt-2 py-2 fs-6">
                <i class="bi bi-arrow-left"></i> Retour à l'accueil
            </a>

            <!-- Lien discret vers l'espace admin -->
            <div class="border-top pt-3 mt-2">
                <a href="login.php" class="text-decoration-none text-danger small fw-bold opacity-75">
                    <i class="bi bi-shield-lock-fill"></i> Accès Administrateur
                </a>
            </div>
        </div>

    </div>

</body>
</html>