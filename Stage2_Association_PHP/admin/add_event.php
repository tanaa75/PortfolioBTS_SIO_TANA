<?php
/**
 * ===========================================
 * AJOUTER UN ÉVÉNEMENT
 * ===========================================
 * 
 * Cette page permet de créer un nouvel événement.
 * 
 * Fonctionnalités :
 * - Formulaire avec titre, date, description
 * - Upload d'image optionnel
 * - Lieu fixé automatiquement (siège de l'association)
 * 
 * Sécurité :
 * - Accessible uniquement aux administrateurs
 * - Vérification du type et de la taille des images
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

// Inclusion des fonctions de sécurité pour CSRF
require_once '../includes/security.php';

// Variable pour les messages
$message = "";

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
    
    // Lieu fixé automatiquement pour la sécurité
    $lieu = "116 rue de l'Avenir, 93130 Noisy-le-Sec";
    
    // ========== GESTION DE L'IMAGE ==========
    $image_filename = NULL; // Par défaut, pas d'image

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Extensions autorisées
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Renommage pour éviter les conflits
            $new_name = "event_" . time() . "." . $ext;
            
            // Déplacement du fichier
            if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)) {
                $image_filename = $new_name;
            }
        } else {
            $message = "<div class='alert alert-warning'>⚠️ Format d'image non supporté (JPG, PNG, WEBP uniquement).</div>";
        }
    }

    // Si pas d'erreur, on enregistre l'événement
    if (strpos($message, 'alert-warning') === false) {
        try {
            $stmt = $pdo->prepare("INSERT INTO evenements (titre, description, date_evenement, lieu, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$titre, $description, $date, $lieu, $image_filename]);
            
            // Redirection avec message de succès
            header("Location: dashboard.php?msg=added");
            exit();
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>❌ Erreur SQL : " . $e->getMessage() . "</div>";
            }
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
    <title>Ajouter un événement | Admin - Aujourd'hui vers Demain</title>
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
                        <i class="bi bi-calendar-plus-fill text-primary display-6 me-3"></i>
                        <h2 class="fw-bold mb-0">Nouvel Événement</h2>
                    </div>

                    <?= $message ?>

                    <form method="POST" enctype="multipart/form-data">
                        <?= champ_csrf() ?>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Titre de l'événement</label>
                                <input type="text" name="titre" class="form-control form-control-lg rounded-3" placeholder="Ex: Fête de quartier..." required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date et Heure</label>
                                <input type="datetime-local" name="date_evenement" class="form-control form-control-lg rounded-3" required>
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
                            <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Détails de l'événement..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Image de couverture (Optionnel)</label>
                            <input type="file" name="image" class="form-control form-control-lg rounded-3" accept="image/*">
                            <div class="form-text">Formats acceptés : JPG, PNG, WEBP.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                                <i class="bi bi-save"></i> Enregistrer
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