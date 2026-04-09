<?php
/**
 * ===========================================
 * PAGE DE CONNEXION ADMINISTRATEUR
 * ===========================================
 * 
 * Cette page permet aux ADMINISTRATEURS de se connecter.
 * Elle vérifie les identifiants dans la table 'utilisateurs'.
 * 
 * Différence avec connexion.php :
 * - login.php = pour les ADMINISTRATEURS (gestion du site)
 * - connexion.php = pour les MEMBRES (utilisateurs normaux)
 */

// Démarrage de la session
session_start();

// Connexion à la base de données
require_once '../includes/db.php';

// Inclusion des fonctions de sécurité
require_once '../includes/security.php';

// Variable pour les erreurs
$error_msg = "";

// ========== TRAITEMENT DU FORMULAIRE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Récupération des données
    $identifiant = $_POST['identifiant'];
    $mot_de_passe = $_POST['mot_de_passe'];
    
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || !verifier_token_csrf($_POST['csrf_token'])) {
        $error_msg = "Erreur de sécurité. Veuillez réessayer.";
    }
    // Vérification si l'utilisateur est bloqué
    elseif (est_connexion_bloquee($identifiant)) {
        $minutes = ceil(temps_restant_blocage($identifiant) / 60);
        $error_msg = "Trop de tentatives. Réessayez dans $minutes minutes.";
    }
    else {
        // Recherche de l'administrateur dans la base
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE identifiant = ?");
        $stmt->execute([$identifiant]);
        $user = $stmt->fetch();

        // Vérification du mot de passe
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            
            // Connexion réussie : réinitialiser les tentatives
            reinitialiser_tentatives($identifiant);
            regenerer_token_csrf();
            
            // Stockage en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['identifiant'] = $user['identifiant'];
            
            // Redirection vers le tableau de bord admin
            header("Location: ../admin/dashboard.php");
            exit();
            
        } else {
            // Échec : enregistrer la tentative
            enregistrer_tentative_echec($identifiant);
            $error_msg = "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Espace administrateur Aujourd'hui vers Demain - Accès sécurisé.">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion Admin | Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

    <!-- Carte de connexion admin -->
    <div class="card card-custom shadow-lg p-4 p-md-5">
        
        <!-- En-tête avec icône de sécurité -->
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
            <h3 class="fw-bold text-primary mt-2">Espace Admin</h3>
            <p class="text-muted small">Accès sécurisé réservé à la gestion</p>
        </div>

        <!-- Affichage des erreurs -->
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= $error_msg ?></div>
            </div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST">
            <?= champ_csrf() ?>
            <div class="form-floating mb-3">
                <input type="text" class="form-control rounded-4" id="identifiant" name="identifiant" placeholder="Admin" required>
                <label for="identifiant"><i class="bi bi-person-badge"></i> Identifiant</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control rounded-4" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe" required>
                <label for="mot_de_passe"><i class="bi bi-key"></i> Mot de passe</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-3 fs-5 shadow hover-scale">
                SE CONNECTER
            </button>
        </form>

        <!-- Lien retour -->
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill py-2 px-4 fw-bold border-2">
                <i class="bi bi-arrow-left"></i> Retour au site public
            </a>
        </div>

    </div>

</body>
</html>