<?php
/**
 * ===========================================
 * BARRE DE NAVIGATION (NAVBAR)
 * ===========================================
 * 
 * Ce fichier contient le menu de navigation du site.
 * Il est inclus dans toutes les pages via include.
 * 
 * Fonctionnalités :
 * - Logo et nom de l'association
 * - Liens vers les pages principales
 * - Menu admin (si administrateur connecté)
 * - Menu membre (si membre connecté)
 * - Bouton de connexion (si non connecté)
 * - Toggle mode jour/nuit
 */

// On démarre la session seulement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Inclusion du fichier de configuration pour les chemins
require_once __DIR__ . '/config.php';
?>

<!-- Navigation principale - sticky-top = reste en haut lors du scroll -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 sticky-top shadow">
  <div class="container">
    
    <!-- Logo et nom de l'association -->
    <a class="navbar-brand d-flex align-items-center fw-bold" href="<?= BASE_URL ?>index.php">
        <img src="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" alt="Logo" width="35" height="35" class="d-inline-block align-text-top me-2 animate-logo">
        Aujourd'hui vers Demain
    </a>
    
    <!-- Bouton hamburger pour mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Menu de navigation -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <!-- Liens principaux -->
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php"><i class="bi bi-house-fill me-1"></i>Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>pages/actions.php"><i class="bi bi-book-fill me-1"></i>Nos Actions</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>pages/benevolat.php"><i class="bi bi-people-fill me-1"></i>Bénévolat</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>pages/galerie.php"><i class="bi bi-camera-fill me-1"></i>Galerie</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>pages/contact.php"><i class="bi bi-envelope-fill me-1"></i>Contact</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- ========== MENU ADMINISTRATEUR ========== -->
            <!-- Affiché seulement si un admin est connecté -->
            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle btn btn-warning text-dark px-3 rounded-pill fw-bold shadow-sm" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-gear-fill me-1"></i>ADMIN
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/dashboard.php"><i class="bi bi-calendar-event me-2"></i>Gérer événements</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/galerie.php"><i class="bi bi-images me-2"></i>Gérer Galerie</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/inscriptions.php"><i class="bi bi-journal-check me-2"></i>Inscriptions Devoirs</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/messages.php"><i class="bi bi-inbox-fill me-2"></i>Messagerie</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/security.php"><i class="bi bi-shield-lock-fill me-2"></i>Sécurité</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout.php">Déconnexion</a></li>
                </ul>
            </li>

        <?php elseif (isset($_SESSION['membre_id'])): ?>
            <!-- ========== MENU MEMBRE ========== -->
            <!-- Affiché seulement si un membre est connecté -->
            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle btn bg-white text-dark px-3 rounded-pill fw-bold shadow-sm border-0" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['membre_nom']) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <!-- Affiche l'email du membre -->
                    <li><span class="dropdown-item-text text-muted small"><i class="bi bi-envelope"></i> <?= htmlspecialchars($_SESSION['membre_email']) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger fw-bold" href="<?= BASE_URL ?>auth/logout_membre.php"><i class="bi bi-box-arrow-right"></i> Se déconnecter</a></li>
                </ul>
            </li>

        <?php else: ?>
            <!-- ========== BOUTON CONNEXION ========== -->
            <!-- Affiché si personne n'est connecté -->
            <li class="nav-item ms-2">
                <a class="btn btn-outline-light rounded-pill px-4 fw-bold" href="<?= BASE_URL ?>auth/connexion.php">Se connecter</a>
            </li>
        <?php endif; ?>

        <!-- Bouton toggle mode jour/nuit -->
        <li class="nav-item ms-2">
            <button class="btn btn-outline-light rounded-circle" onclick="toggleTheme()" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <span id="theme-icon"><i class="bi bi-moon-fill"></i></span>
            </button>
        </li>
        
      </ul>
    </div>
  </div>
</nav>