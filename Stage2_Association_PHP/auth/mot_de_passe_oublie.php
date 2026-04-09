<?php
/**
 * ===========================================
 * PAGE MOT DE PASSE OUBLIÉ
 * ===========================================
 * 
 * Cette page permet aux membres de réinitialiser
 * leur mot de passe s'ils l'ont oublié.
 * 
 * PROCESSUS :
 * 1. L'utilisateur entre son email
 * 2. Un code de récupération est généré et stocké
 * 3. Le code est affiché (en production, il serait envoyé par email)
 * 4. L'utilisateur entre le code + nouveau mot de passe
 * 
 * Note : Pour une vraie implémentation avec envoi d'email,
 * il faudrait configurer un serveur SMTP (ex: PHPMailer)
 */

session_start();
require_once '../includes/db.php';

$message = "";
$step = 1; // Étape actuelle (1 = email, 2 = code + nouveau mdp)

// ========== ÉTAPE 1 : DEMANDE DE RÉINITIALISATION ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'demander') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Vérifier si l'email existe en base
    $stmt = $pdo->prepare("SELECT id FROM membres WHERE email = ?");
    $stmt->execute([$email]);
    $membre = $stmt->fetch();
    
    if ($membre) {
        // Générer un code aléatoire à 6 chiffres
        $code = sprintf("%06d", mt_rand(0, 999999));
        
        // Stocker le code en session (expiration 15 min)
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_expire'] = time() + (15 * 60);
        
        $step = 2;
        $message = "<div class='alert alert-success'>
            <strong>✅ Code envoyé !</strong><br>
            <small>En production, le code serait envoyé par email.</small><br>
            <div class='mt-2 p-2 bg-light rounded text-center'>
                <strong class='fs-4 text-primary'>$code</strong>
            </div>
        </div>";
    } else {
        $message = "<div class='alert alert-warning'>Aucun compte trouvé avec cet email.</div>";
    }
}

// ========== ÉTAPE 2 : RÉINITIALISATION DU MOT DE PASSE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'reinitialiser') {
    $code_saisi = $_POST['code'];
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirmer_mdp = $_POST['confirmer_mdp'];
    
    // Vérifications
    if (!isset($_SESSION['reset_code']) || !isset($_SESSION['reset_email'])) {
        $message = "<div class='alert alert-danger'>Session expirée. Recommencez.</div>";
    }
    elseif (time() > $_SESSION['reset_expire']) {
        $message = "<div class='alert alert-danger'>Code expiré. Recommencez.</div>";
        unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_expire']);
    }
    elseif ($code_saisi !== $_SESSION['reset_code']) {
        $message = "<div class='alert alert-danger'>Code incorrect.</div>";
        $step = 2;
    }
    elseif ($nouveau_mdp !== $confirmer_mdp) {
        $message = "<div class='alert alert-danger'>Les mots de passe ne correspondent pas.</div>";
        $step = 2;
    }
    elseif (strlen($nouveau_mdp) < 6) {
        $message = "<div class='alert alert-danger'>Le mot de passe doit faire au moins 6 caractères.</div>";
        $step = 2;
    }
    else {
        // Tout est OK : mettre à jour le mot de passe
        $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE membres SET mot_de_passe = ? WHERE email = ?");
        $stmt->execute([$hash, $_SESSION['reset_email']]);
        
        // Nettoyer la session
        unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_expire']);
        
        $message = "<div class='alert alert-success'>
            <strong>✅ Mot de passe modifié !</strong><br>
            <a href='connexion.php' class='alert-link'>Connectez-vous maintenant</a>
        </div>";
    }
}

// Si on revient à l'étape 2 après une erreur
if (isset($_SESSION['reset_code']) && $step == 1) {
    $step = 2;
}
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Aujourd'hui vers Demain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .card-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: none;
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>

    <div class="card card-custom shadow-lg p-4 p-md-5">
        
        <!-- En-tête -->
        <div class="text-center mb-4">
            <i class="bi bi-key-fill text-warning" style="font-size: 3rem;"></i>
            <h3 class="fw-bold text-dark mt-2">Mot de passe oublié ?</h3>
            <p class="text-muted small">Pas de panique, on va régler ça !</p>
        </div>
        
        <!-- Messages -->
        <?= $message ?>

        <?php if ($step == 1): ?>
            <!-- ÉTAPE 1 : Saisie de l'email -->
            <form method="POST">
                <input type="hidden" name="action" value="demander">
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Votre adresse email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control form-control-lg rounded-end" placeholder="email@exemple.com" required>
                    </div>
                    <div class="form-text">Entrez l'email utilisé lors de votre inscription.</div>
                </div>
                
                <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold py-3 fs-5 shadow">
                    <i class="bi bi-send me-2"></i>Envoyer le code
                </button>
            </form>
            
        <?php else: ?>
            <!-- ÉTAPE 2 : Saisie du code et nouveau mot de passe -->
            <form method="POST">
                <input type="hidden" name="action" value="reinitialiser">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Code de récupération</label>
                    <input type="text" name="code" class="form-control form-control-lg text-center" placeholder="000000" maxlength="6" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nouveau mot de passe</label>
                    <input type="password" name="nouveau_mdp" class="form-control form-control-lg" placeholder="••••••" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Confirmer le mot de passe</label>
                    <input type="password" name="confirmer_mdp" class="form-control form-control-lg" placeholder="••••••" required>
                </div>
                
                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-3 fs-5 shadow">
                    <i class="bi bi-check-lg me-2"></i>Changer le mot de passe
                </button>
            </form>
        <?php endif; ?>

        <!-- Lien retour -->
        <div class="text-center mt-4">
            <a href="connexion.php" class="text-decoration-none text-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la connexion
            </a>
        </div>

    </div>

    <script src="../assets/js/script_theme.js"></script>
</body>
</html>
