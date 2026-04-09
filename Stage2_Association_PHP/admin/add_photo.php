<?php
/**
 * ===========================================
 * AJOUT D'UNE PHOTO À LA GALERIE
 * ===========================================
 * 
 * Formulaire permettant à l'administrateur d'ajouter
 * une nouvelle photo à la galerie.
 * 
 * Fonctionnalités :
 * - Upload de fichier image (jpg, png, webp, gif)
 * - Titre et description
 * - Sélection de catégorie (prédéfinie ou personnalisée)
 * 
 * Sécurité :
 * - Accessible uniquement aux administrateurs
 * - Validation du type de fichier
 * - Limite de taille (5 Mo)
 */

session_start();

// Vérification de sécurité
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../auth/login.php"); 
    exit(); 
}

require_once '../includes/db.php';

// Inclusion des fonctions de sécurité pour CSRF
require_once '../includes/security.php';

$message = "";
$message_type = "";

// Catégories prédéfinies
$categories_predefinies = [
    'Général',
    'Événements',
    'Bénévoles',
    'Ateliers',
    'Aide aux devoirs',
    'Vie de quartier',
    'Fêtes',
    'Sorties'
];

// ========== TRAITEMENT DU FORMULAIRE ==========
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifier_token_csrf($_POST['csrf_token'])) {
        $message = "Erreur de sécurité. Veuillez réessayer.";
        $message_type = "danger";
    } else {
    
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $categorie = trim($_POST['categorie']);
    
    // Si catégorie personnalisée
    if ($categorie == 'autre' && !empty($_POST['categorie_autre'])) {
        $categorie = trim($_POST['categorie_autre']);
    }
    
    // Validation
    if (empty($titre)) {
        $message = "Le titre est obligatoire.";
        $message_type = "danger";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $message = "Veuillez sélectionner une image.";
        $message_type = "danger";
    } else {
        // Gestion de l'upload
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $message = "Format non supporté. Utilisez JPG, PNG, GIF ou WEBP.";
            $message_type = "danger";
        } elseif ($_FILES['image']['size'] > 5000000) {
            $message = "L'image est trop lourde (max 5 Mo).";
            $message_type = "danger";
        } else {
            // Renommage unique du fichier
            $new_name = "gallery_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)) {
                // Insertion en base
                $stmt = $pdo->prepare("INSERT INTO photos (titre, description, image, categorie) VALUES (?, ?, ?, ?)");
                $stmt->execute([$titre, $description, $new_name, $categorie]);
                
                header("Location: galerie.php?msg=added");
                exit();
            } else {
                $message = "Erreur lors de l'upload de l'image.";
                $message_type = "danger";
            }
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
    <title>Ajouter une photo | Admin - Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="bg-body-tertiary">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <!-- Bouton retour -->
                <a href="galerie.php" class="btn btn-outline-secondary rounded-pill mb-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour à la galerie
                </a>
                
                <!-- Carte formulaire -->
                <div class="card form-card shadow-lg border-0">
                    <div class="card-header text-white">
                        <h3 class="mb-1">
                            <i class="bi bi-image me-2"></i>Ajouter une photo
                        </h3>
                        <p class="mb-0 opacity-75">Enrichissez votre galerie avec de nouvelles photos</p>
                    </div>
                    
                    <div class="card-body p-4">
                        
                        <!-- Message d'erreur/succès -->
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
                                <i class="bi bi-<?= $message_type == 'danger' ? 'exclamation-triangle' : 'check-circle' ?>-fill me-2"></i>
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <?= champ_csrf() ?>
                            
                            <!-- Zone d'upload -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-cloud-upload me-1"></i>Image *
                                </label>
                                <div class="upload-zone" id="uploadZone" onclick="document.getElementById('imageInput').click()">
                                    <i class="bi bi-image"></i>
                                    <h5 class="mt-3 mb-2">Glissez une image ici</h5>
                                    <p class="text-muted mb-0">ou cliquez pour sélectionner</p>
                                    <small class="text-muted">JPG, PNG, GIF, WEBP • Max 5 Mo</small>
                                </div>
                                <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" required>
                                
                                <!-- Prévisualisation -->
                                <div class="preview-container text-center" id="previewContainer">
                                    <img id="previewImage" src="" alt="Aperçu">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearPreview()">
                                        <i class="bi bi-x-circle me-1"></i>Changer l'image
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Titre -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-type me-1"></i>Titre *
                                </label>
                                <input type="text" name="titre" class="form-control form-control-lg" 
                                       placeholder="Ex: Fête de quartier 2026" required
                                       value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>">
                            </div>
                            
                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-text-paragraph me-1"></i>Description
                                </label>
                                <textarea name="description" class="form-control" rows="3" 
                                          placeholder="Une courte description de cette photo (optionnel)"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                            </div>
                            
                            <!-- Catégorie -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-tag me-1"></i>Catégorie
                                </label>
                                <select name="categorie" id="categorieSelect" class="form-select form-select-lg" onchange="toggleAutreCategorie()">
                                    <?php foreach ($categories_predefinies as $cat): ?>
                                        <option value="<?= $cat ?>"><?= $cat ?></option>
                                    <?php endforeach; ?>
                                    <option value="autre">➕ Autre (personnalisée)</option>
                                </select>
                                <input type="text" name="categorie_autre" id="categorieAutre" 
                                       class="form-control mt-2 d-none" placeholder="Nom de la nouvelle catégorie">
                            </div>
                            
                            <!-- Boutons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                    <i class="bi bi-cloud-upload me-2"></i>Ajouter la photo
                                </button>
                            </div>
                            
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script_theme.js"></script>
    <script>
        const uploadZone = document.getElementById('uploadZone');
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        
        // Drag and drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });
        
        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });
        
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            
            if (e.dataTransfer.files.length) {
                imageInput.files = e.dataTransfer.files;
                showPreview(e.dataTransfer.files[0]);
            }
        });
        
        // Changement de fichier
        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                showPreview(e.target.files[0]);
            }
        });
        
        function showPreview(file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                    uploadZone.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }
        
        function clearPreview() {
            imageInput.value = '';
            previewContainer.style.display = 'none';
            uploadZone.style.display = 'block';
        }
        
        // Toggle catégorie personnalisée
        function toggleAutreCategorie() {
            const select = document.getElementById('categorieSelect');
            const autre = document.getElementById('categorieAutre');
            
            if (select.value === 'autre') {
                autre.classList.remove('d-none');
                autre.required = true;
            } else {
                autre.classList.add('d-none');
                autre.required = false;
            }
        }
    </script>
</body>
</html>
