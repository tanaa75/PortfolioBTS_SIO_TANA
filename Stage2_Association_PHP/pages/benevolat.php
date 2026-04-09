<?php
/**
 * ===========================================
 * PAGE BÉNÉVOLAT - DEVENIR BÉNÉVOLE
 * ===========================================
 * 
 * Cette page permet aux visiteurs de postuler comme bénévole.
 * 
 * Fonctionnalités :
 * - Présentation des besoins de l'association
 * - Formulaire de candidature avec upload de CV
 * - Gestion sécurisée des fichiers uploadés
 * 
 * Sécurité :
 * - Seuls les utilisateurs connectés peuvent postuler
 * - Les fichiers sont vérifiés (type et taille)
 */

// Démarrage de la session
session_start();

// Connexion à la base de données
require_once '../includes/db.php';

// Inclusion des fonctions de sécurité pour CSRF
require_once '../includes/security.php';

// Variables de suivi
$benevole_ok = false;     // Candidature envoyée ?
$error_msg = "";          // Message d'erreur éventuel

// Vérification : est-ce qu'un membre OU un admin est connecté ?
$est_connecte = (isset($_SESSION['membre_id']) || isset($_SESSION['user_id']));

// ========== TRAITEMENT DU FORMULAIRE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $est_connecte) {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifier_token_csrf($_POST['csrf_token'])) {
        $error_msg = "Erreur de sécurité. Veuillez réessayer.";
    } elseif ($_POST['form_type'] == 'benevolat') {
        // Récupération des données
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $tel = $_POST['tel'];
        $dispo = $_POST['dispo'];
        $skills = $_POST['skills'];
        
        $lien_cv = "Aucun CV fourni";
        
        // ========== GESTION DE L'UPLOAD DU CV ==========
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
            
            // Vérification de la taille (max 5 Mo)
            if ($_FILES['cv']['size'] <= 5000000) {
                
                // Récupération de l'extension
                $fileInfo = pathinfo($_FILES['cv']['name']);
                $extension = strtolower($fileInfo['extension']);
                
                // Extensions autorisées
                $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'png'];
                
                if (in_array($extension, $allowedExtensions)) {
                    // Renommage du fichier pour éviter les conflits
                    $new_filename = 'cv_' . preg_replace('/[^a-zA-Z0-9]/', '', $nom) . '_' . time() . '.' . $extension;
                    
                    // Déplacement du fichier dans le dossier uploads
                    if (move_uploaded_file($_FILES['cv']['tmp_name'], '../uploads/' . $new_filename)) {
                        $lien_cv = "📄 TÉLÉCHARGER LE CV : http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/../uploads/" . $new_filename;
                    } else { 
                        $error_msg = "Erreur upload."; 
                    }
                } else { 
                    $error_msg = "Format non supporté."; 
                }
            } else { 
                $error_msg = "Fichier trop lourd."; 
            }
        }

        // Si pas d'erreur, on enregistre la candidature
        if (empty($error_msg)) {
            $msg_complet = "❤️ NOUVEAU BÉNÉVOLE !\n\nNom : $nom\nEmail : $email\nTéléphone : $tel\n\nDispos : $dispo\nAime faire : $skills\n\n$lien_cv";
            $stmt = $pdo->prepare("INSERT INTO messages (nom, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $email, $msg_complet]);
            $benevole_ok = true;
        }
    }
}

// Pré-remplissage des champs pour les membres connectés
$nom_user = isset($_SESSION['membre_nom']) ? $_SESSION['membre_nom'] : "";
$email_user = isset($_SESSION['membre_email']) ? $_SESSION['membre_email'] : "";
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Devenez bénévole chez Aujourd'hui vers Demain. Rejoignez notre équipe et participez à la vie du quartier.">
    <meta name="robots" content="index, follow">
    <title>Devenir Bénévole | Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/benevolat.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include '../includes/navbar.php'; ?>

    <div class="position-relative py-5 overflow-hidden benevolat-section">
        <div class="benevolat-bg-overlay"></div>
        
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="mb-4">
                        
                        <h2 class="display-5 fw-bold mb-3">Rejoignez l'aventure !</h2>
                        <p class="lead benevolat-text mb-4">
                            Devenir bénévole, c'est choisir de consacrer un peu de son temps pour faire une grande différence dans la vie du quartier.
                        </p>
                    </div>
                    
                    <div class="alert benevolat-alert border-start border-primary border-4 shadow-sm mb-4" role="alert">
                        <p class="mb-2 fw-semibold"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Aucune expertise requise !</p>
                        <p class="small benevolat-alert-text mb-0">Ce qui compte, c'est votre envie d'être utile et de partager un moment avec la communauté.</p>
                    </div>
                    
                    <h5 class="fw-bold mb-3">
                        <span class="border-bottom border-warning border-3 pb-1">Nos besoins actuels</span>
                    </h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="need-card p-3 rounded-3 border border-2 border-primary h-100 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-book-fill fs-4 text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">Aide aux devoirs</h6>
                                        <p class="small need-card-text mb-0">Accompagnement scolaire</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                            <div class="need-card p-3 rounded-3 border border-2 border-info h-100 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-info bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-laptop fs-4 text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">Atelier informatique</h6>
                                        <p class="small need-card-text mb-0">Initiation numérique</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                            <div class="need-card p-3 rounded-3 border border-2 border-warning h-100 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-warning bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-balloon-fill fs-4 text-dark"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">Organisation fêtes</h6>
                                        <p class="small need-card-text mb-0">Événements quartier</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                            <div class="need-card p-3 rounded-3 border border-2 border-success h-100 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-success bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-palette-fill fs-4 text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">Ateliers créatifs</h6>
                                        <p class="small need-card-text mb-0">Art & bricolage</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 benevolat-footer-text">
                        <svg width="20" height="20" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                        <small class="fw-semibold">Flexible : 1h/semaine ou plus selon vos disponibilités</small>
                    </div>
                </div>
                
                <div class="col-lg-6" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden volunteer-form-card">
                        <div class="card-header bg-gradient text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                            <div class="mb-2">
                                <i class="bi bi-rocket-takeoff-fill fs-1"></i>
                            </div>
                            <h3 class="mb-1 fw-bold form-header-title">Je me lance !</h3>
                            <p class="small mb-0 fw-semibold form-header-subtitle">Remplissez le formulaire ci-dessous</p>
                        </div>
                        <div class="card-body p-4">
                        
                        <?php if ($est_connecte): ?>
                            
                            <?php if ($benevole_ok): ?>
                                <div class="alert alert-success"><i class="bi bi-hand-thumbs-up-fill me-2"></i>Candidature envoyée !</div>
                            <?php endif; ?>
                            <?php if (!empty($error_msg)): ?>
                                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error_msg ?></div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data">
                                <?= champ_csrf() ?>
                                <input type="hidden" name="form_type" value="benevolat">
                                
                                <div class="mb-3">
                                    <label>Nom & Prénom</label>
                                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($nom_user) ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email_user) ?>" required>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label>Téléphone</label>
                                        <input type="tel" name="tel" class="form-control" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Votre CV (PDF, Word)</label>
                                    <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                                </div>

                                <div class="mb-3">
                                    <label>Mes disponibilités</label>
                                    <input type="text" name="dispo" class="form-control" placeholder="Ex: Mercredi après-midi" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label>Ce que j'aime faire</label>
                                    <textarea name="skills" class="form-control" rows="3" placeholder="J'aime cuisiner, aider en maths..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">Envoyer ma candidature</button>
                            </form>

                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="mb-3 display-4"><i class="bi bi-lock-fill text-secondary"></i></div>
                                <h5 class="fw-bold">Espace réservé</h5>
                                <p class="text-muted mb-4">Vous devez être membre pour postuler.</p>
                                <div class="d-grid gap-2">
                                    <a href="../auth/connexion.php" class="btn btn-primary rounded-pill fw-bold">Se connecter</a>
                                    <a href="../auth/inscription.php" class="btn btn-outline-primary rounded-pill fw-bold">Créer un compte</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../assets/js/script_theme.js"></script>
</body>
</html>