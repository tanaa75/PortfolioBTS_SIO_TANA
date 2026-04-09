<?php
/**
 * ===========================================
 * MODIFIER UN ÉVÉNEMENT
 * ===========================================
 * 
 * Cette page permet de modifier un événement existant.
 * 
 * Fonctionnalités :
 * - Pré-remplissage des champs avec les données actuelles
 * - Modification du titre, date, description
 * - Remplacement optionnel de l'image
 * 
 * Sécurité :
 * - Accessible uniquement aux administrateurs
 * - Vérification que l'événement existe
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

// Vérification qu'un ID est passé dans l'URL
if (!isset($_GET['id'])) { 
    header("Location: dashboard.php"); 
    exit(); 
}

// Récupération de l'événement à modifier
$stmt = $pdo->prepare("SELECT * FROM evenements WHERE id = ?");
$stmt->execute([$_GET['id']]);
$event = $stmt->fetch();

// Si l'événement n'existe pas, retour au dashboard
if (!$event) { 
    header("Location: admin_dashboard.php"); 
    exit(); 
}

// Variable pour les messages
$message = "";

// Inclusion des fonctions de sécurité pour CSRF
require_once '../includes/security.php';

// ========== TRAITEMENT DU FORMULAIRE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifier_token_csrf($_POST['csrf_token'])) {
        $message = "<div class='alert alert-danger'>Erreur de sécurité. Veuillez réessayer.</div>";
    } else {
    // Récupération des données
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date = $_POST['date_evenement'];
    $lieu = "116 rue de l'Avenir, 93130 Noisy-le-Sec";
    
    // ========== GESTION DE L'IMAGE ==========
    $image_sql = "";           // Par défaut, on ne touche pas à l'image
    $params = [$titre, $description, $date, $lieu];

    // Si une nouvelle image est envoyée
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = "event_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)) {
                // On ajoute la mise à jour de l'image à la requête
                $image_sql = ", image = ?";
                $params[] = $new_name;
            }
        }
    }

    // Ajout de l'ID pour la clause WHERE
    $params[] = $_GET['id'];

    try {
        // Requête dynamique selon si on a une nouvelle image ou pas
        $sql = "UPDATE evenements SET titre=?, description=?, date_evenement=?, lieu=? $image_sql WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Redirection avec message de succès
        header("Location: dashboard.php?msg=updated");
        exit();
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>❌ Erreur SQL : " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Modifier l'événement | Admin - Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-form-page">

    <?php include '../includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="card card-form p-4 p-md-5">
                    
                    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                        <i class="bi bi-pencil-square text-warning display-6 me-3"></i>
                        <h2 class="fw-bold mb-0">Modifier l'Événement</h2>
                    </div>

                    <?= $message ?>

                    <form method="POST" enctype="multipart/form-data">
                        <?= champ_csrf() ?>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Titre de l'événement</label>
                                <input type="text" name="titre" class="form-control form-control-lg rounded-3" value="<?= htmlspecialchars($event['titre']) ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date et Heure</label>
                                <input type="datetime-local" name="date_evenement" class="form-control form-control-lg rounded-3" value="<?= date('Y-m-d\TH:i', strtotime($event['date_evenement'])) ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lieu <small class="text-muted">(Fixe)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                    <input type="text" name="lieu" class="form-control form-control-lg input-locked" value="116 rue de l'Avenir, 93130 Noisy-le-Sec" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control rounded-3" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Image de couverture</label>
                            
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <?php if (!empty($event['image'])): ?>
                                    <div class="text-center">
                                        <img src="../uploads/<?= htmlspecialchars($event['image']) ?>" class="current-img-preview shadow-sm" alt="Actuelle">
                                        <div class="small text-muted mt-1">Actuelle</div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted small fst-italic">Pas d'image actuellement</div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <input type="file" name="image" class="form-control form-control-lg rounded-3" accept="image/*">
                                    <div class="form-text">Laisser vide pour garder l'image actuelle.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow text-white">
                                <i class="bi bi-check-lg"></i> Mettre à jour
                            </button>
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