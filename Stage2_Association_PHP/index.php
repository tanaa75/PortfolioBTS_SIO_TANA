<?php
/**
 * ===========================================
 * PAGE D'ACCUEIL - AUJOURD'HUI VERS DEMAIN
 * ===========================================
 * 
 * Fichier principal du site de l'association.
 * Affiche toutes les sections importantes.
 * 
 * SECTIONS :
 * - Hero Banner (Bannière d'accueil)
 * - Statistiques animées (Bénévoles, Enfants, Création)
 * - Qui sommes-nous ? (Présentation)
 * - Nos Actions (Aide aux devoirs + formulaire inscription)
 * - Rejoignez l'équipe (Bénévolat + formulaire candidature)
 * - Nos Actualités (Événements + recherche)
 * 
 * FORMULAIRES :
 * - Inscription aide aux devoirs → envoi vers table 'messages'
 * - Candidature bénévole → envoi vers table 'messages' + upload CV
 * 
 * SÉCURITÉ :
 * - Formulaires accessibles uniquement aux utilisateurs connectés
 * - Upload CV : vérification type et taille
 */

// Démarrage de la session
session_start(); 

// Connexion à la base de données
require_once 'includes/db.php';

// ========== PROTECTION CSRF ==========
// Génération d'un token unique pour protéger les formulaires
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

/**
 * Fonction de nettoyage des entrées utilisateur
 * @param string $data - Donnée à nettoyer
 * @return string - Donnée nettoyée
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validation du token CSRF
 * @return bool - True si valide, false sinon
 */
function verify_csrf_token() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// ========== VARIABLES DE SUIVI ==========
$inscription_ok = false;   // Inscription devoirs réussie ?
$benevole_ok = false;      // Candidature bénévole réussie ?
$error_msg = "";           // Message d'erreur éventuel

// Vérification si un utilisateur est connecté (membre OU admin)
$est_connecte = (isset($_SESSION['membre_id']) || isset($_SESSION['user_id']));

// ========== TRAITEMENT DES FORMULAIRES ==========
// On ne traite les formulaires QUE si l'utilisateur est connecté ET le token CSRF est valide
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $est_connecte) {
    
    // Vérification du token CSRF
    if (!verify_csrf_token()) {
        $error_msg = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        // ------------------------------------------
        // CAS 1 : INSCRIPTION AIDE AUX DEVOIRS
        // ------------------------------------------
        if ($_POST['form_type'] == 'devoirs') {
            // Nettoyage et validation des entrées
            $nom = sanitize_input($_POST['nom']);
            $prenom = sanitize_input($_POST['prenom']);
            $classe = sanitize_input($_POST['classe']);
            $tel = sanitize_input($_POST['tel']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $adresse = sanitize_input($_POST['adresse']);
            
            // Validation de l'email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_msg = "Adresse email invalide.";
            } elseif (!preg_match('/^[0-9\s\+\-\.]+$/', $tel)) {
                $error_msg = "Numéro de téléphone invalide.";
            } else {
                // Construction du message formaté
                $msg = "🔔 INSCRIPTION AIDE AUX DEVOIRS\n\nEnfant : $nom $prenom\nClasse : $classe\nAdresse : $adresse\nTéléphone : $tel\nEmail parent : $email";
                
                // Insertion en base de données
                $stmt = $pdo->prepare("INSERT INTO messages (nom, email, message) VALUES (?, ?, ?)");
                $stmt->execute(["Parent de $prenom", $email, $msg]);
                $inscription_ok = true;
            }
        }

        // ------------------------------------------
        // CAS 2 : CANDIDATURE BÉNÉVOLE
        // ------------------------------------------
        if ($_POST['form_type'] == 'benevolat') {
            // Nettoyage et validation des entrées
            $nom = sanitize_input($_POST['nom']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $tel = sanitize_input($_POST['tel']);
            $dispo = sanitize_input($_POST['dispo']);
            $skills = sanitize_input($_POST['skills']);
            
            // Validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_msg = "Adresse email invalide.";
            } elseif (!preg_match('/^[0-9\s\+\-\.]+$/', $tel)) {
                $error_msg = "Numéro de téléphone invalide.";
            } else {
                $lien_cv = "Aucun CV fourni";
                
                // Gestion de l'upload du CV
                if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
                    // Vérification taille (max 5 Mo)
                    if ($_FILES['cv']['size'] <= 5000000) {
                        $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
                        
                        // Vérification extension
                        if (in_array($ext, ['pdf', 'doc', 'docx', 'jpg', 'png'])) {
                            // Renommage sécurisé
                            $newname = 'cv_' . preg_replace('/[^a-zA-Z0-9]/', '', $nom) . '_' . time() . '.' . $ext;
                            
                            // Déplacement dans le dossier uploads
                            if(move_uploaded_file($_FILES['cv']['tmp_name'], 'uploads/' . $newname)) {
                                $lien_cv = "📄 TÉLÉCHARGER LE CV : http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $newname;
                            }
                        } else { 
                            $error_msg = "Format invalide (PDF, DOC, DOCX, JPG, PNG uniquement)."; 
                        }
                    } else { 
                        $error_msg = "Fichier trop lourd (max 5 Mo)."; 
                    }
                }

                // Si pas d'erreur, on enregistre la candidature
                if (empty($error_msg)) {
                    $msg = "❤️ NOUVEAU BÉNÉVOLE !\n\nNom : $nom\nEmail : $email\nTéléphone : $tel\n\nDispos : $dispo\nAime faire : $skills\n\n$lien_cv";
                    $stmt = $pdo->prepare("INSERT INTO messages (nom, email, message) VALUES (?, ?, ?)");
                    $stmt->execute([$nom, $email, $msg]);
                    $benevole_ok = true;
                }
            }
        }
    } // Fin de la vérification CSRF
}

// ========== PRÉ-REMPLISSAGE DES FORMULAIRES ==========
// Si l'utilisateur est un membre connecté, on récupère ses infos
$nom_user = isset($_SESSION['membre_nom']) ? $_SESSION['membre_nom'] : "";
$email_user = isset($_SESSION['membre_email']) ? $_SESSION['membre_email'] : "";

// ========== RÉCUPÉRATION DES ÉVÉNEMENTS AVEC PAGINATION ==========
$search = "";
$events_per_page = 6; // Nombre d'événements par page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $events_per_page;

if (isset($_GET['search']) && !empty($_GET['search'])) {
    // Recherche par mot-clé avec pagination
    $search = trim($_GET['search']);
    
    // Compter le nombre total de résultats
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM evenements WHERE titre LIKE :s OR description LIKE :s OR lieu LIKE :s");
    $count_stmt->execute(['s' => "%$search%"]);
    $total_events = $count_stmt->fetchColumn();
    
    // Récupérer les événements pour la page actuelle
    $stmt = $pdo->prepare("SELECT * FROM evenements WHERE titre LIKE :s OR description LIKE :s OR lieu LIKE :s ORDER BY date_evenement DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('s', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue('limit', $events_per_page, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll();
} else {
    // Compter le nombre total d'événements
    $total_events = $pdo->query("SELECT COUNT(*) FROM evenements")->fetchColumn();
    
    // Récupérer les événements pour la page actuelle
    $stmt = $pdo->prepare("SELECT * FROM evenements ORDER BY date_evenement DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('limit', $events_per_page, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll();
}

// Calculer le nombre total de pages
$total_pages = ceil($total_events / $events_per_page);
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aujourd'hui vers Demain - Association de quartier à Noisy-le-Sec. Aide aux devoirs, animations locales et bénévolat pour construire ensemble l'avenir de nos quartiers.">
    <meta name="keywords" content="association, Noisy-le-Sec, aide aux devoirs, bénévolat, quartier, solidarité, enfants, soutien scolaire">
    <meta name="author" content="Aujourd'hui vers Demain">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="Aujourd'hui vers Demain - Association de quartier">
    <meta property="og:description" content="Au cœur de Noisy-le-Sec, pour l'avenir de nos quartiers. Aide aux devoirs et animations locales.">
    <meta property="og:type" content="website">
    <title>Aujourd'hui vers Demain | Association de quartier à Noisy-le-Sec</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/mobile-responsive.css">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include 'includes/navbar.php'; ?>

    <div class="hero-banner text-center">
        <div class="container">
            <h1 class="display-3 fw-bold" data-aos="fade-down">Construire demain, dès aujourd'hui.</h1>
            <p class="lead mb-4" data-aos="fade-in">Au cœur de Noisy-le-Sec, pour l'avenir de nos quartiers.</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center" data-aos="zoom-in">
                <a href="#actions" class="btn btn-warning btn-lg rounded-pill shadow fw-bold" aria-label="Découvrir l'aide aux devoirs"><i class="bi bi-book-fill me-2" aria-hidden="true"></i>Aide aux devoirs</a>
                <a href="#benevolat" class="btn btn-outline-light btn-lg rounded-pill" aria-label="Devenir bénévole de l'association"><i class="bi bi-people-fill me-2" aria-hidden="true"></i>Devenir Bénévole</a>
            </div>
        </div>
    </div>

    <!-- Section Statistiques Animées -->
    <div class="bg-warning text-dark py-4 text-center fw-bold shadow position-relative overflow-hidden">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row g-3">
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-card p-3 rounded-3 bg-white bg-opacity-25 backdrop-blur h-100 hover-stat">
                        <div class="stat-icon mb-2">
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                        <h2 class="display-6 fw-bold mb-1">
                            <span class="counter" data-target="15">0</span>+
                        </h2>
                        <p class="fs-6 mb-0 text-uppercase letter-spacing-wide">Bénévoles</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-card p-3 rounded-3 bg-white bg-opacity-25 backdrop-blur h-100 hover-stat">
                        <div class="stat-icon mb-2">
                            <i class="bi bi-person-hearts fs-1"></i>
                        </div>
                        <h2 class="display-6 fw-bold mb-1">
                            <span class="counter" data-target="50">0</span>+
                        </h2>
                        <p class="fs-6 mb-0 text-uppercase letter-spacing-wide">Enfants</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-card p-3 rounded-3 bg-white bg-opacity-25 backdrop-blur h-100 hover-stat">
                        <div class="stat-icon mb-2">
                            <i class="bi bi-bullseye fs-1"></i>
                        </div>
                        <h2 class="display-6 fw-bold mb-1">
                            <span class="counter" data-target="2020">2000</span>
                        </h2>
                        <p class="fs-6 mb-0 text-uppercase letter-spacing-wide">Création</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Éléments décoratifs animés -->
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
    </div>

    <div class="container py-5 my-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="fw-bold text-primary mb-4">Qui sommes-nous ?</h2>
                <p class="lead text-muted">
                    Plus qu'une simple association, <strong>Aujourd'hui vers Demain</strong> est le fruit d'une solidarité entre voisins. Tout a commencé en 2020, dans les quartiers Langevin et La Boissière, avec une idée simple : on est plus forts ensemble.
                </p>
                <p>
                    Ici, pas de grands discours, mais du concret. Nous sommes des habitants, des parents et des jeunes qui avons décidé de nous bouger pour notre ville. Notre but ? Que chacun trouve sa place, que ce soit par le soutien scolaire pour les plus jeunes ou l'organisation de moments festifs pour tous.
                </p>
                <p class="mb-4">
                    On croit en la force du collectif pour changer les choses, une action à la fois.
                </p>
            </div>
            <div class="col-lg-6" data-aos="zoom-in">
                <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Enfants faisant leurs devoirs avec un bénévole" class="img-fluid rounded-4 shadow-lg" loading="lazy">
            </div>
        </div>
    </div>

    <hr class="container my-5">

    <div class="actions-section py-5" id="actions">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-down">
                
                <h2 class="display-5 fw-bold mb-3">L'Aide aux Devoirs</h2>
                <p class="lead actions-subtitle">Accompagner chaque enfant vers la réussite</p>
            </div>
            
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="actions-info-card p-4 rounded-4 shadow-lg h-100">
                        <div class="d-flex align-items-start mb-4">
                            <div class="flex-shrink-0 me-3">
                                <div class="actions-icon-wrapper">
                                    <i class="bi bi-pencil-fill fs-1 text-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="fw-bold mb-3 actions-title">Notre Mission</h3>
                                <p class="actions-text mb-3">
                                    L'école, ce n'est pas toujours facile, et à la maison, on n'a pas toujours le temps ou les clés pour aider. Notre accompagnement ne sert pas juste à "finir les devoirs", mais à <strong>redonner confiance</strong>.
                                </p>
                                <p class="actions-text">
                                    Dans une ambiance calme, nos bénévoles prennent le temps d'expliquer, de réviser les bases et surtout d'apprendre à s'organiser. L'objectif : que chaque enfant reparte fier de son travail et l'esprit plus léger.
                                </p>
                            </div>
                        </div>
                        
                        <div class="border-top actions-border pt-4">
                            <h5 class="fw-bold mb-3 actions-subtitle-small"><i class="bi bi-clipboard-check me-2"></i>Informations pratiques</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-badge p-3 rounded-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-event me-2 fs-5 text-primary"></i>
                                            <div>
                                                <small class="d-block text-uppercase fw-semibold actions-label">Jours</small>
                                                <span class="fw-bold actions-value">Lun, Mar, Jeu, Ven</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-badge p-3 rounded-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-2 fs-5 text-success"></i>
                                            <div>
                                                <small class="d-block text-uppercase fw-semibold actions-label">Horaires</small>
                                                <span class="fw-bold actions-value">16h30 - 18h00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="info-badge p-3 rounded-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-mortarboard me-2 fs-5 text-info"></i>
                                            <div>
                                                <small class="d-block text-uppercase fw-semibold actions-label">Niveaux</small>
                                                <span class="fw-bold actions-value">Du CP au CM2</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card border-primary shadow">
                    <div class="card-header bg-primary text-white text-center"><h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Inscrire mon enfant</h5></div>
                    <div class="card-body">
                        
                        <?php if ($est_connecte): ?>
                            
                            <?php if ($inscription_ok): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Inscription envoyée !</div><?php endif; ?>
                            <?php if (!empty($error_msg) && isset($_POST['form_type']) && $_POST['form_type'] == 'devoirs'): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error_msg) ?></div><?php endif; ?>
                            <form method="POST" action="#actions">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="form_type" value="devoirs">
                                <div class="row">
                                    <div class="col-6 mb-3"><label>Nom enfant</label><input type="text" name="nom" class="form-control" required></div>
                                    <div class="col-6 mb-3"><label>Prénom</label><input type="text" name="prenom" class="form-control" required></div>
                                </div>
                                <div class="mb-3"><label>Email parent</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email_user) ?>" required></div>
                                <div class="mb-3"><label>Adresse</label><input type="text" name="adresse" class="form-control" placeholder="Ex: 12 rue de la Paix, 93000 Bobigny" required></div>
                                <div class="row">
                                    <div class="col-6 mb-3"><label>Classe</label><input type="text" name="classe" class="form-control" placeholder="Ex: CM1" required></div>
                                    <div class="col-6 mb-3"><label>Téléphone</label><input type="tel" name="tel" class="form-control" required></div>
                                </div>
                                <button class="btn btn-warning w-100 fw-bold">Valider l'inscription</button>
                            </form>

                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="mb-3 display-4"><i class="bi bi-lock-fill text-secondary"></i></div>
                                <h5 class="fw-bold">Réservé aux membres</h5>
                                <p class="text-muted mb-4">Connectez-vous pour inscrire votre enfant.</p>
                                <div class="d-grid gap-2">
                                    <a href="auth/connexion.php" class="btn btn-primary rounded-pill fw-bold">Se connecter</a>
                                    <a href="auth/inscription.php" class="btn btn-outline-primary rounded-pill fw-bold">Créer un compte</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-4">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h3 class="fw-bold text-success"><i class="bi bi-houses-fill me-2"></i>Vie de Quartier & Citoyenneté</h3>
                <p class="text-muted">Parce qu'un quartier vivant, c'est l'affaire de tous.</p>
            </div>
            
            <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card p-4 shadow-sm border-0 h-100 hover-card text-center">
                    <div class="icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); box-shadow: 0 10px 30px rgba(238, 90, 36, 0.3);">
                        <i class="bi bi-stars text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold">Animations Locales</h5>
                    <p class="small text-muted mb-0">Fêtes de quartier, repas partagés et sorties culturelles pour tous.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="card p-4 shadow-sm border-0 h-100 hover-card text-center">
                    <div class="icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 10px 30px rgba(118, 75, 162, 0.3);">
                        <i class="bi bi-megaphone-fill text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold">Conseil Citoyen</h5>
                    <p class="small text-muted mb-0">Votre voix compte ! Participez aux décisions pour améliorer la vie de la cité.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="card p-4 shadow-sm border-0 h-100 hover-card text-center">
                    <div class="icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); box-shadow: 0 10px 30px rgba(56, 239, 125, 0.3);">
                        <i class="bi bi-people-fill text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold">Médiation Sociale</h5>
                    <p class="small text-muted mb-0">Une oreille attentive pour orienter les familles et résoudre les conflits.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="position-relative py-5 overflow-hidden benevolat-section" id="benevolat">
        <!-- Éléments décoratifs -->
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
                            
                            <?php if ($benevole_ok): ?><div class="alert alert-success">Candidature envoyée !</div><?php endif; ?>
                            <?php if (!empty($error_msg)): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>

                            <form method="POST" action="#benevolat" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="form_type" value="benevolat">
                                <div class="mb-3"><label>Nom & Prénom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($nom_user) ?>" required></div>
                                <div class="row">
                                    <div class="col-6 mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email_user) ?>" required></div>
                                    <div class="col-6 mb-3"><label>Téléphone</label><input type="tel" name="tel" class="form-control" required></div>
                                </div>
                                <div class="mb-3"><label>CV (PDF/Word)</label><input type="file" name="cv" class="form-control"></div>
                                <div class="mb-3"><label>Disponibilités</label><input type="text" name="dispo" class="form-control" required></div>
                                <div class="mb-3"><textarea name="skills" class="form-control" rows="2" placeholder="Ce que j'aime faire..."></textarea></div>
                                <button class="btn btn-primary w-100 rounded-pill fw-bold">Envoyer</button>
                            </form>

                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-3 display-4"><i class="bi bi-lock-fill text-secondary"></i></div>
                                <h5 class="fw-bold">Espace réservé</h5>
                                <p class="text-muted mb-4">Vous devez être membre pour postuler.</p>
                                <div class="d-grid gap-2">
                                    <a href="auth/connexion.php" class="btn btn-primary rounded-pill fw-bold">Se connecter</a>
                                    <a href="auth/inscription.php" class="btn btn-outline-primary rounded-pill fw-bold">Créer un compte</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5" id="events">
        <h2 class="text-center mb-5 text-primary fw-bold">Nos Actualités</h2>
        
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <form method="GET" action="#events" class="d-flex gap-2 p-2 bg-body-tertiary rounded-pill shadow" style="position: relative; z-index: 10;" role="search">
                    <input type="text" name="search" class="form-control border-0 bg-transparent rounded-pill ps-4" placeholder="Rechercher un événement..." value="<?= htmlspecialchars($search) ?>" style="cursor: text;" aria-label="Rechercher un événement">
                    <button class="btn btn-primary rounded-circle" type="submit" style="min-width: 45px;" aria-label="Lancer la recherche"><i class="bi bi-search"></i></button>
                    <?php if(!empty($search)): ?><a href="index.php#events" class="btn btn-secondary rounded-circle" style="min-width: 45px;" aria-label="Effacer la recherche"><i class="bi bi-x-lg"></i></a><?php endif; ?>
                </form>
            </div>
        </div>

        <?php if(empty($events)): ?>
            <div class="alert alert-warning text-center">Aucun événement trouvé.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach($events as $evt): ?>
                    <div class="col-md-4 mb-3" data-aos="fade-up">
                        <div class="card h-100 shadow border-0 card-event">
                            <?php if (!empty($evt['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($evt['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($evt['titre']) ?>" style="height: 200px; object-fit: cover;" loading="lazy">
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="badge bg-warning text-dark mb-2"><?= date('d/m/Y', strtotime($evt['date_evenement'])) ?></span>
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($evt['titre']) ?></h5>
                                <p class="small text-muted mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($evt['lieu']) ?></p>
                                <p class="card-text"><?= nl2br(htmlspecialchars($evt['description'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Navigation des événements" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Bouton Précédent -->
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link rounded-pill me-1" href="?page=<?= $current_page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>#events" aria-label="Page précédente">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Numéros de page -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                            <a class="page-link rounded-pill mx-1" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>#events" <?= $i == $current_page ? 'aria-current="page"' : '' ?>>
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Bouton Suivant -->
                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link rounded-pill ms-1" href="?page=<?= $current_page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>#events" aria-label="Page suivante">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <p class="text-center text-muted small mb-0">
                    Affichage de <?= count($events) ?> événement<?= count($events) > 1 ? 's' : '' ?> sur <?= $total_events ?>
                </p>
            </nav>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>

    <!-- Section Call-to-Action -->
    <section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);" data-aos="fade-up">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8 text-white mb-4 mb-lg-0">
                    <h2 class="display-6 fw-bold mb-3">
                        <i class="bi bi-heart-fill me-2" aria-hidden="true"></i>Envie de nous soutenir ?
                    </h2>
                    <p class="lead mb-0 opacity-90">
                        Chaque geste compte ! Que ce soit par un don, du temps, ou simplement en parlant de nous autour de vous, vous participez à l'avenir de notre quartier.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-lg-end">
                        <a href="pages/contact.php" class="btn btn-light btn-lg rounded-pill fw-bold shadow-sm" aria-label="Nous contacter">
                            <i class="bi bi-envelope-fill me-2" aria-hidden="true"></i>Nous contacter
                        </a>
                        <a href="#benevolat" class="btn btn-outline-light btn-lg rounded-pill fw-bold" aria-label="Rejoindre notre équipe de bénévoles">
                            <i class="bi bi-hand-thumbs-up-fill me-2" aria-hidden="true"></i>Rejoindre l'équipe
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Décorations flottantes -->
        <div class="floating-shape" style="width: 200px; height: 200px; top: -50px; right: -50px; opacity: 0.1;"></div>
        <div class="floating-shape" style="width: 150px; height: 150px; bottom: -30px; left: 10%; opacity: 0.1;"></div>
    </section>

    <!-- Bouton flottant Contact -->
    <a href="pages/contact.php" class="floating-contact-btn" title="Nous contacter">
        <i class="bi bi-chat-dots-fill"></i>
    </a>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/script_theme.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>