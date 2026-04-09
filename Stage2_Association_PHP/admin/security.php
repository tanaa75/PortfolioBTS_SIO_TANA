<?php
/**
 * ===========================================
 * PAGE SÉCURITÉ ADMIN
 * ===========================================
 * 
 * Cette page permet à l'administrateur de changer
 * son mot de passe.
 * 
 * Étapes de sécurité :
 * 1. Vérification de l'ancien mot de passe
 * 2. Confirmation du nouveau mot de passe
 * 3. Hashage sécurisé avant enregistrement
 * 
 * Sécurité :
 * - Accessible uniquement aux administrateurs
 * - Le mot de passe est hashé avec password_hash()
 */

// Démarrage de la session
session_start();

// Vérification de sécurité : redirection si non connecté
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../auth/login.php"); 
    exit(); 
}

// Connexion à la base de données
require_once '../includes/db.php';

// Variable pour les messages (succès ou erreur)
$message = "";

// ========== TRAITEMENT DU FORMULAIRE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Étape 1 : Récupérer les infos de l'admin connecté
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Étape 2 : Vérifier l'ancien mot de passe
    if (!password_verify($current_pass, $user['mot_de_passe'])) {
        $message = "<div class='alert alert-danger border-0 shadow-sm'><i class='bi bi-x-circle-fill me-2'></i> L'ancien mot de passe est incorrect.</div>";
    } 
    // Étape 3 : Vérifier que les deux nouveaux sont identiques
    elseif ($new_pass !== $confirm_pass) {
        $message = "<div class='alert alert-danger border-0 shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i> Les nouveaux mots de passe ne correspondent pas.</div>";
    } 
    // Étape 4 : Tout est bon, on met à jour !
    else {
        // Hashage du nouveau mot de passe
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        
        // Mise à jour en base de données
        $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$new_hash, $_SESSION['user_id']]);
        
        $message = "<div class='alert alert-success border-0 shadow-sm'><i class='bi bi-check-circle-fill me-2'></i> Mot de passe modifié avec succès !</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sécurité - Aujourd'hui vers Demain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body {
            /* MÊME FOND QUE LE RESTE DE L'ADMIN */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .card-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .form-control, .form-control:focus {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            padding: 0.8rem 1rem;
        }
    </style>
</head>
<body>

    <?php include '../includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <div class="card card-custom p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow" style="width: 70px; height: 70px;">
                            <i class="bi bi-shield-lock-fill fs-1"></i>
                        </div>
                        <h2 class="fw-bold mt-3 text-danger">Sécurité du Compte</h2>
                        <p class="text-muted small">Modifiez votre mot de passe administrateur</p>
                    </div>

                    <?= $message ?>
                    
                    <form method="POST">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Mot de passe actuel</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="current_password" class="form-control border-start-0 rounded-end-3" placeholder="••••••••" required>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-4">
                            <hr class="flex-grow-1 opacity-25">
                            <span class="px-3 text-muted small text-uppercase fw-bold">Nouveau mot de passe</span>
                            <hr class="flex-grow-1 opacity-25">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control rounded-3" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="confirm_password" class="form-control rounded-3" required>
                        </div>
                        
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold shadow hover-scale">
                                <i class="bi bi-save2-fill me-2"></i> Enregistrer le changement
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill fw-bold border-0">
                                Annuler et retour
                            </a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script_theme.js"></script>
</body>
</html>