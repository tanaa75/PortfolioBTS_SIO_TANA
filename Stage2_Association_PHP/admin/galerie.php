<?php
/**
 * ===========================================
 * ADMINISTRATION DE LA GALERIE PHOTOS
 * ===========================================
 * 
 * Cette page permet à l'administrateur de :
 * - Voir toutes les photos de la galerie
 * - Ajouter de nouvelles photos
 * - Supprimer des photos existantes
 * - Filtrer par catégorie
 * 
 * Sécurité :
 * - Accessible uniquement aux administrateurs connectés
 */

session_start();

// Vérification de sécurité : redirection si non connecté
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../auth/login.php"); 
    exit(); 
}

require_once '../includes/db.php';

// Inclusion des fonctions de sécurité pour CSRF
require_once '../includes/security.php';

// ========== SUPPRESSION D'UNE PHOTO (MÉTHODE POST SÉCURISÉE) ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifier_token_csrf($_POST['csrf_token'])) {
        $error_msg = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $id = intval($_POST['delete_photo']);
        
        // Récupérer le nom du fichier avant suppression
        $stmt = $pdo->prepare("SELECT image FROM photos WHERE id = ?");
        $stmt->execute([$id]);
        $photo = $stmt->fetch();
        
        if ($photo) {
            // Supprimer le fichier image
            $filepath = "../uploads/" . $photo['image'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Supprimer l'enregistrement en base
            $stmt = $pdo->prepare("DELETE FROM photos WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        header("Location: galerie.php?msg=deleted");
        exit();
    }
}

// ========== FILTRE PAR CATÉGORIE ==========
$categorie_filtre = isset($_GET['cat']) ? $_GET['cat'] : '';

if ($categorie_filtre) {
    $stmt = $pdo->prepare("SELECT * FROM photos WHERE categorie = ? ORDER BY date_ajout DESC");
    $stmt->execute([$categorie_filtre]);
} else {
    $stmt = $pdo->query("SELECT * FROM photos ORDER BY date_ajout DESC");
}
$photos = $stmt->fetchAll();

// Récupérer toutes les catégories pour le filtre
$categories = $pdo->query("SELECT DISTINCT categorie FROM photos ORDER BY categorie")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Gestion Galerie | Admin - Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="bg-body-tertiary">
    <?php include '../includes/navbar.php'; ?>
    
    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="bi bi-images me-2"></i>Gestion de la Galerie
                    </h1>
                    <p class="mb-0 opacity-75">Gérez les photos de votre association</p>
                </div>
                <a href="add_photo.php" class="btn btn-light btn-lg rounded-pill shadow">
                    <i class="bi bi-plus-circle me-2"></i>Ajouter une photo
                </a>
            </div>
        </div>
    </div>
    
    <div class="container pb-5">
        
        <!-- Message de confirmation -->
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php 
                    if($_GET['msg'] == 'deleted') echo "Photo supprimée avec succès !";
                    elseif($_GET['msg'] == 'added') echo "Photo ajoutée avec succès !";
                    else echo "Action effectuée !";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistiques rapides -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card bg-primary text-white shadow">
                    <i class="bi bi-images fs-1"></i>
                    <h2 class="fw-bold mb-0"><?= count($photos) ?></h2>
                    <small>Photos totales</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success text-white shadow">
                    <i class="bi bi-tags fs-1"></i>
                    <h2 class="fw-bold mb-0"><?= count($categories) ?></h2>
                    <small>Catégories</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-info text-white shadow">
                    <i class="bi bi-calendar-event fs-1"></i>
                    <h2 class="fw-bold mb-0">
                        <?php 
                        $stmt_events = $pdo->query("SELECT COUNT(*) FROM evenements WHERE image IS NOT NULL AND image != ''");
                        echo $stmt_events->fetchColumn();
                        ?>
                    </h2>
                    <small>Photos d'événements</small>
                </div>
            </div>
        </div>
        
        <!-- Filtres par catégorie -->
        <?php if (count($categories) > 0): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filtrer par catégorie</h6>
                <nav class="filter-pills">
                    <a href="galerie.php" class="btn <?= $categorie_filtre == '' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        Toutes
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="galerie.php?cat=<?= urlencode($cat) ?>" 
                           class="btn <?= $categorie_filtre == $cat ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <?= htmlspecialchars($cat) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Grille de photos -->
        <?php if (count($photos) > 0): ?>
            <div class="row g-4">
                <?php foreach ($photos as $photo): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="photo-admin-card shadow bg-body">
                            <img src="../uploads/<?= htmlspecialchars($photo['image']) ?>" 
                                 alt="<?= htmlspecialchars($photo['titre']) ?>">
                            
                            <!-- Badge catégorie -->
                            <span class="photo-badge badge bg-primary"><?= htmlspecialchars($photo['categorie']) ?></span>
                            
                            <!-- Boutons d'action -->
                            <div class="photo-actions">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette photo ?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="delete_photo" value="<?= $photo['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm rounded-circle" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Info photo -->
                            <div class="p-3">
                                <h6 class="fw-bold mb-1 text-truncate"><?= htmlspecialchars($photo['titre']) ?></h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d/m/Y', strtotime($photo['date_ajout'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-camera display-1 text-muted opacity-50"></i>
                <h4 class="mt-3 text-muted">Aucune photo dans la galerie</h4>
                <p class="text-muted">Commencez par ajouter votre première photo !</p>
                <a href="add_photo.php" class="btn btn-primary rounded-pill">
                    <i class="bi bi-plus-circle me-2"></i>Ajouter une photo
                </a>
            </div>
        <?php endif; ?>
        
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script_theme.js"></script>
</body>
</html>
