<?php
/**
 * ===========================================
 * PAGE DE CONTACT
 * ===========================================
 * 
 * Cette page permet aux membres/admins d'envoyer un message
 * à l'association via un formulaire.
 * 
 * Fonctionnalités :
 * - Formulaire de contact (réservé aux connectés)
 * - Affichage des coordonnées de l'association
 * - Carte Google Maps intégrée
 * 
 * Sécurité :
 * - Protection CSRF
 * - Validation et nettoyage des entrées
 * - Seuls les utilisateurs connectés peuvent envoyer un message
 */

// Démarrage de la session
session_start();

// Connexion à la base de données
require_once '../includes/db.php';

// ========== PROTECTION CSRF ==========
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Variables de suivi
$msg_envoye = false;
$error_msg = "";

// Vérification : est-ce qu'un membre OU un admin est connecté ?
$est_connecte = (isset($_SESSION['membre_id']) || isset($_SESSION['user_id']));

// ========== TRAITEMENT DU FORMULAIRE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && $est_connecte) {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_msg = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        // Nettoyage des entrées
        $nom = trim(htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'));
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));
        
        // Validation
        if (empty($nom) || empty($email) || empty($message)) {
            $error_msg = "Tous les champs sont obligatoires.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Adresse email invalide.";
        } else {
            // Insertion du message en base de données
            $stmt = $pdo->prepare("INSERT INTO messages (nom, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $email, $message]);
            $msg_envoye = true;
        }
    }
}

// Pré-remplissage des champs avec les infos du membre connecté
$nom_user = isset($_SESSION['membre_nom']) ? $_SESSION['membre_nom'] : "";
$email_user = isset($_SESSION['membre_email']) ? $_SESSION['membre_email'] : "";
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contactez l'association Aujourd'hui vers Demain. Nous sommes à votre écoute pour tout projet ou question.">
    <meta name="robots" content="index, follow">
    <title>Contact | Aujourd'hui vers Demain - Association Noisy-le-Sec</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/contact.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include '../includes/navbar.php'; ?>

    <!-- Header -->
    <div class="contact-header">
        <div class="container">
            <div class="text-center">
                <h1 class="display-4 fw-bold mb-3 contact-title">Nous Contacter</h1>
                <p class="lead contact-subtitle">Une question ? Un projet ? Parlons-en !</p>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            <!-- Formulaire -->
            <div class="col-lg-7" data-aos="fade-right">
                <div class="contact-form-card h-100">
                    <div class="p-4 p-md-5">
                        
                        <?php if ($est_connecte): ?>

                            <h4 class="mb-4 fw-bold"><i class="bi bi-envelope-heart-fill me-2 text-primary"></i>Envoyez-nous un message</h4>
                            
                            <?php if ($msg_envoye): ?>
                                <div class="alert alert-success text-center border-0 shadow-sm">
                                    <i class="bi bi-check-circle-fill me-2"></i>Message envoyé ! On vous répond très vite.
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($error_msg)): ?>
                                <div class="alert alert-danger text-center border-0 shadow-sm">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error_msg) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <div class="form-floating mb-3">
                                    <input type="text" name="nom" class="form-control rounded-3" id="floatingNom" required placeholder="Nom" value="<?= htmlspecialchars($nom_user) ?>" aria-label="Votre nom">
                                    <label for="floatingNom">Votre Nom</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" name="email" class="form-control rounded-3" id="floatingEmail" required placeholder="Email" value="<?= htmlspecialchars($email_user) ?>" aria-label="Votre email">
                                    <label for="floatingEmail">Votre Email</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <textarea name="message" class="form-control rounded-3" id="floatingMsg" style="height: 150px" required placeholder="Message" aria-label="Votre message"></textarea>
                                    <label for="floatingMsg">Votre Message</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow" aria-label="Envoyer le message">
                                    <i class="bi bi-send-fill me-2" aria-hidden="true"></i>Envoyer le message
                                </button>
                            </form>

                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-3" style="font-size: 4rem;"><i class="bi bi-lock-fill text-secondary"></i></div>
                                <h4 class="fw-bold">Espace réservé aux membres</h4>
                                <p class="text-muted mb-4">Vous devez avoir un compte pour nous envoyer un message.</p>
                                <div class="d-grid gap-2 col-8 mx-auto">
                                    <a href="../auth/connexion.php" class="btn btn-primary rounded-pill fw-bold py-2">Me connecter</a>
                                    <a href="../auth/inscription.php" class="btn btn-outline-primary rounded-pill fw-bold py-2">Créer un compte</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- Infos -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="d-flex flex-column gap-4">
                    
                    <!-- Coordonnées -->
                    <div class="contact-info-card p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Nos Coordonnées</h5>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <div class="contact-info-label">Adresse</div>
                                <div class="contact-info-value">116 rue de l'Avenir, 93130 Noisy-le-Sec</div>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <div class="contact-info-label">Téléphone</div>
                                <div class="contact-info-value">01 23 45 67 89</div>
                            </div>
                        </div>
                        
                        <div class="contact-info-item mb-0">
                            <div class="contact-info-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div>
                                <div class="contact-info-label">Email</div>
                                <div class="contact-info-value">contact@asso.fr</div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte -->
                    <div class="map-container shadow">
                        <iframe 
                            src="https://maps.google.com/maps?q=116+rue+de+l'Avenir+93130+Noisy-le-Sec&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../assets/js/script_theme.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>