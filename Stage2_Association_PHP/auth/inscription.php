<?php
/**
 * ===========================================
 * PAGE D'INSCRIPTION MEMBRE AVEC CONFIRMATION
 * ===========================================
 * 
 * Cette page permet aux visiteurs de créer un compte membre.
 * Un code de confirmation est généré pour vérifier l'email.
 * 
 * PROCESSUS :
 * 1. L'utilisateur remplit le formulaire
 * 2. Un code de confirmation est affiché (simulé)
 * 3. L'utilisateur doit entrer le code pour activer son compte
 * 
 * Note : En production, le code serait envoyé par email (PHPMailer)
 */

session_start();
require_once '../includes/db.php';

// Gestion du reset de l'inscription si demandé
if (isset($_GET['reset']) && $_GET['reset'] == '1') {
    unset($_SESSION['inscription_temp']);
}

$message = "";
$step = 1; // 1 = formulaire, 2 = confirmation code

// ========== ÉTAPE 1 : INSCRIPTION ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'inscription') {
    
    $nom = htmlspecialchars($_POST['nom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['mot_de_passe'];
    
    // Vérification si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM membres WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $message = "<div class='alert alert-warning text-center border-0 shadow-sm'>⚠️ Cet email est déjà utilisé.</div>";
    } 
    elseif (strlen($password) < 6) {
        $message = "<div class='alert alert-danger text-center border-0 shadow-sm'>❌ Le mot de passe doit faire au moins 6 caractères.</div>";
    }
    else {
        // Générer un code de confirmation à 6 chiffres
        $code = sprintf("%06d", mt_rand(0, 999999));
        
        // Stocker les infos temporairement en session
        $_SESSION['inscription_temp'] = [
            'nom' => $nom,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'code' => $code,
            'expire' => time() + (15 * 60) // 15 minutes
        ];
        
        $step = 2;
        $message = "<div class='alert alert-info text-center border-0 shadow-sm'>
            <strong>📧 Code de confirmation</strong><br>
            <small>En production, ce code serait envoyé à votre email.</small><br>
            <div class='mt-2 p-3 bg-white rounded shadow-sm'>
                <span class='fs-3 fw-bold text-primary letter-spacing-2'>$code</span>
            </div>
        </div>";
    }
}

// ========== ÉTAPE 2 : CONFIRMATION DU CODE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'confirmer') {
    
    $code_saisi = $_POST['code'];
    
    // Vérifications
    if (!isset($_SESSION['inscription_temp'])) {
        $message = "<div class='alert alert-danger'>Session expirée. Recommencez l'inscription.</div>";
    }
    elseif (time() > $_SESSION['inscription_temp']['expire']) {
        $message = "<div class='alert alert-danger'>Code expiré. Recommencez l'inscription.</div>";
        unset($_SESSION['inscription_temp']);
    }
    elseif ($code_saisi !== $_SESSION['inscription_temp']['code']) {
        $message = "<div class='alert alert-danger'>Code incorrect. Réessayez.</div>";
        $step = 2;
    }
    else {
        // Code correct ! Créer le compte
        $data = $_SESSION['inscription_temp'];
        
        $stmt = $pdo->prepare("INSERT INTO membres (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$data['nom'], $data['email'], $data['password']])) {
            unset($_SESSION['inscription_temp']);
            $message = "<div class='alert alert-success text-center border-0 shadow-sm'>
                <i class='bi bi-check-circle-fill fs-1 text-success'></i><br>
                <strong class='fs-5'>✅ Compte créé avec succès !</strong><br>
                <a href='connexion.php' class='btn btn-success rounded-pill mt-3'>Se connecter maintenant</a>
            </div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Une erreur technique est survenue.</div>";
        }
    }
}

// Si on revient à l'étape 2 après une erreur
if (isset($_SESSION['inscription_temp']) && $step == 1) {
    $step = 2;
}
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Créez votre compte membre Aujourd'hui vers Demain et rejoignez notre communauté.">
    <meta name="robots" content="noindex, nofollow">
    <title>Inscription | Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

    <div class="card card-custom shadow-lg p-4 p-md-5">
        
        <!-- En-tête -->
        <div class="text-center mb-4">
            <img src="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" width="60" class="mb-3">
            <?php if ($step == 1): ?>
                <h3 class="fw-bold text-primary">Créer un compte</h3>
                <p class="text-muted small">Rejoignez l'association en quelques clics</p>
            <?php else: ?>
                <h3 class="fw-bold text-success">Confirmez votre email</h3>
                <p class="text-muted small">Entrez le code reçu pour activer votre compte</p>
            <?php endif; ?>
        </div>
        
        <!-- Messages -->
        <?= $message ?>

        <?php if ($step == 1): ?>
            <!-- ÉTAPE 1 : Formulaire d'inscription -->
            <form method="POST">
                <input type="hidden" name="action" value="inscription">
                
                <div class="form-floating mb-3">
                    <input type="text" name="nom" class="form-control rounded-4" id="nomInput" placeholder="Jean Dupont" required>
                    <label for="nomInput"><i class="bi bi-person"></i> Nom complet</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control rounded-4" id="emailInput" placeholder="name@example.com" required>
                    <label for="emailInput"><i class="bi bi-envelope"></i> Email</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" name="mot_de_passe" class="form-control rounded-4" id="passInput" placeholder="Password" required minlength="6">
                    <label for="passInput"><i class="bi bi-key"></i> Mot de passe (min. 6 car.)</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-3 fs-5 shadow">
                    <i class="bi bi-person-plus me-2"></i>S'inscrire
                </button>
            </form>
            
        <?php else: ?>
            <!-- ÉTAPE 2 : Confirmation du code -->
            <form method="POST">
                <input type="hidden" name="action" value="confirmer">
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Code de confirmation</label>
                    <input type="text" name="code" class="form-control form-control-lg code-input rounded-4" placeholder="000000" maxlength="6" required autofocus>
                    <div class="form-text text-center">Le code expire dans 15 minutes</div>
                </div>
                
                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-3 fs-5 shadow">
                    <i class="bi bi-check-lg me-2"></i>Confirmer mon compte
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="inscription.php?reset=1" class="text-muted small">
                    <i class="bi bi-arrow-left"></i> Recommencer l'inscription
                </a>
            </div>
        <?php endif; ?>

        <!-- Liens supplémentaires -->
        <div class="d-grid gap-2 text-center mt-4">
            <a href="connexion.php" class="btn btn-outline-secondary rounded-pill py-2 fw-bold border-2">
                Déjà membre ? Se connecter
            </a>

            <a href="../index.php" class="text-decoration-none text-secondary mt-2 py-2 fs-6">
                <i class="bi bi-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>

    <script src="../assets/js/script_theme.js"></script>
</body>
</html>