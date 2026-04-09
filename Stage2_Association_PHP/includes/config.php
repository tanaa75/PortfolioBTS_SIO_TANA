<?php
/**
 * ===========================================
 * FICHIER DE CONFIGURATION
 * ===========================================
 * 
 * Définit les chemins de base pour le site.
 * À inclure dans tous les fichiers qui ont besoin
 * de créer des liens absolus.
 */

// Chemin de base du site (auto-détecté)
// Cela permet aux liens de fonctionner depuis n'importe quel sous-dossier
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// Détecte le chemin de base en fonction de la position du script
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptPath;

// Si on est dans un sous-dossier (admin/, auth/, pages/, etc.), on remonte d'un niveau
if (preg_match('#/(admin|auth|pages|setup|includes)$#', $scriptPath)) {
    $basePath = dirname($scriptPath);
}

// URL de base complète
define('BASE_URL', $protocol . $host . $basePath . '/');

// Chemins relatifs depuis la racine
define('ADMIN_PATH', 'admin/');
define('AUTH_PATH', 'auth/');
define('PAGES_PATH', 'pages/');
define('ASSETS_PATH', 'assets/');
define('INCLUDES_PATH', 'includes/');
define('UPLOADS_PATH', 'uploads/');
?>
