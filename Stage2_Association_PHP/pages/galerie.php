<?php
/**
 * ===========================================
 * PAGE GALERIE PHOTOS - VERSION DYNAMIQUE
 * ===========================================
 * 
 * Cette page affiche toutes les photos :
 * - Photos de la table 'photos' (galerie indépendante)
 * - Photos des événements (table 'evenements')
 * 
 * Fonctionnalités :
 * - Filtrage par catégorie (onglets)
 * - Tri par date (récent/ancien)
 * - Effet lightbox au clic
 * - Design responsive
 */

session_start();
require_once '../includes/db.php';

// ========== FILTRES ET TRI ==========
$categorie_filtre = isset($_GET['cat']) ? $_GET['cat'] : '';
$tri = isset($_GET['tri']) ? $_GET['tri'] : 'recent';

// Récupérer les photos de la galerie
$sql_photos = "SELECT id, titre, image, categorie, date_ajout as date_photo, 'galerie' as source FROM photos";
if ($categorie_filtre && $categorie_filtre != 'evenements') {
    $sql_photos .= " WHERE categorie = :cat";
}

// Récupérer les photos des événements
$sql_events = "SELECT id, titre, image, 'Événements' as categorie, date_evenement as date_photo, 'evenement' as source 
               FROM evenements WHERE image IS NOT NULL AND image != ''";

// Combiner les requêtes selon le filtre
if ($categorie_filtre == 'evenements') {
    // Seulement les événements
    $stmt = $pdo->query($sql_events);
    $all_photos = $stmt->fetchAll();
} elseif ($categorie_filtre) {
    // Seulement une catégorie de la galerie
    $stmt = $pdo->prepare($sql_photos);
    $stmt->execute(['cat' => $categorie_filtre]);
    $all_photos = $stmt->fetchAll();
} else {
    // Toutes les photos (galerie + événements)
    $photos_galerie = $pdo->query($sql_photos)->fetchAll();
    $photos_events = $pdo->query($sql_events)->fetchAll();
    $all_photos = array_merge($photos_galerie, $photos_events);
}

// Tri par date
usort($all_photos, function($a, $b) use ($tri) {
    $dateA = strtotime($a['date_photo']);
    $dateB = strtotime($b['date_photo']);
    return $tri == 'recent' ? $dateB - $dateA : $dateA - $dateB;
});

// Récupérer toutes les catégories disponibles
$categories = $pdo->query("SELECT DISTINCT categorie FROM photos ORDER BY categorie")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Galerie photos de l'association Aujourd'hui vers Demain. Revivez nos moments forts en images.">
    <meta name="robots" content="index, follow">
    <title>Galerie Photos | Aujourd'hui vers Demain - Association Noisy-le-Sec</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link rel="stylesheet" href="../assets/css/galerie.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <!-- Header -->
    <div class="gallery-header">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-images me-3"></i>Galerie Photos
            </h1>
            <p class="lead opacity-75 mb-4">Revivez nos moments forts en images</p>
            <span class="photo-count">
                <i class="bi bi-camera-fill me-2"></i><?= count($all_photos) ?> photo<?= count($all_photos) > 1 ? 's' : '' ?>
            </span>
        </div>
    </div>
    
    <!-- Barre de filtres -->
    <div class="filter-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="filter-pills">
                        <a href="galerie.php" class="btn <?= $categorie_filtre == '' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Toutes
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="galerie.php?cat=<?= urlencode($cat) ?>&tri=<?= $tri ?>" 
                               class="btn <?= $categorie_filtre == $cat ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <?= htmlspecialchars($cat) ?>
                            </a>
                        <?php endforeach; ?>
                        <a href="galerie.php?cat=evenements&tri=<?= $tri ?>" 
                           class="btn <?= $categorie_filtre == 'evenements' ? 'btn-warning' : 'btn-outline-warning' ?>">
                            <i class="bi bi-calendar-event me-1"></i>Événements
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 text-md-end mt-3 mt-md-0">
                    <select class="sort-select" onchange="window.location.href=this.value">
                        <option value="galerie.php?cat=<?= urlencode($categorie_filtre) ?>&tri=recent" <?= $tri == 'recent' ? 'selected' : '' ?>>
                            📅 Plus récentes
                        </option>
                        <option value="galerie.php?cat=<?= urlencode($categorie_filtre) ?>&tri=ancien" <?= $tri == 'ancien' ? 'selected' : '' ?>>
                            📅 Plus anciennes
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Galerie -->
    <div class="container py-5">
        <?php if (count($all_photos) > 0): ?>
            <div class="row g-4">
                <?php foreach ($all_photos as $index => $photo): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= min($index * 50, 300) ?>">
                        <div class="photo-card" onclick="openLightbox('../uploads/<?= htmlspecialchars($photo['image']) ?>', '<?= htmlspecialchars(addslashes($photo['titre'])) ?>', '<?= date('d/m/Y', strtotime($photo['date_photo'])) ?>')">
                            <img src="../uploads/<?= htmlspecialchars($photo['image']) ?>" alt="<?= htmlspecialchars($photo['titre']) ?>" loading="lazy">
                            
                            <!-- Badge catégorie -->
                            <span class="photo-badge badge bg-primary"><?= htmlspecialchars($photo['categorie']) ?></span>
                            
                            <!-- Badge source (événement ou galerie) -->
                            <?php if ($photo['source'] == 'evenement'): ?>
                                <span class="photo-source badge bg-warning text-dark">
                                    <i class="bi bi-calendar-event"></i>
                                </span>
                            <?php endif; ?>
                            
                            <div class="photo-overlay">
                                <h5 class="mb-1"><?= htmlspecialchars($photo['titre']) ?></h5>
                                <span class="photo-date">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d F Y', strtotime($photo['date_photo'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-photos">
                <i class="bi bi-camera"></i>
                <h3 class="mt-4 text-muted">Aucune photo trouvée</h3>
                <p class="text-muted">
                    <?php if ($categorie_filtre): ?>
                        Aucune photo dans cette catégorie.
                    <?php else: ?>
                        Les photos apparaîtront ici une fois ajoutées.
                    <?php endif; ?>
                </p>
                <a href="galerie.php" class="btn btn-primary rounded-pill mt-3">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Voir toutes les photos
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <span class="lightbox-close" onclick="closeLightbox()">
            <i class="bi bi-x"></i>
        </span>
        <img src="" id="lightbox-img" alt="Photo agrandie">
        <div class="lightbox-info">
            <h4 id="lightbox-title" class="mb-1"></h4>
            <p id="lightbox-date" class="mb-0 opacity-75"></p>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../assets/js/script_theme.js"></script>
    <script src="../assets/js/galerie.js"></script>
</body>
</html>
