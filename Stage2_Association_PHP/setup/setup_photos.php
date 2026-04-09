<?php
/**
 * ===========================================
 * SCRIPT DE CRÉATION DE LA TABLE PHOTOS
 * ===========================================
 * 
 * Ce script crée la table 'photos' pour stocker
 * les photos de la galerie indépendantes des événements.
 * 
 * À exécuter une seule fois via le navigateur ou phpMyAdmin.
 */

require_once '../includes/db.php';

try {
    // Création de la table photos
    $sql = "CREATE TABLE IF NOT EXISTS photos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        image VARCHAR(255) NOT NULL,
        categorie VARCHAR(100) DEFAULT 'Général',
        date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    
    echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
    echo "<h2 style='color: #28a745;'>✅ Table 'photos' créée avec succès !</h2>";
    echo "<p>La table contient les colonnes suivantes :</p>";
    echo "<ul>";
    echo "<li><strong>id</strong> - Identifiant unique (auto-incrémenté)</li>";
    echo "<li><strong>titre</strong> - Titre de la photo</li>";
    echo "<li><strong>description</strong> - Description optionnelle</li>";
    echo "<li><strong>image</strong> - Nom du fichier image</li>";
    echo "<li><strong>categorie</strong> - Catégorie (Général, Événements, Bénévoles, etc.)</li>";
    echo "<li><strong>date_ajout</strong> - Date d'ajout automatique</li>";
    echo "</ul>";
    echo "<a href='../admin/galerie.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px;'>Aller à la Galerie Admin</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto;'>";
    echo "<h2 style='color: #dc3545;'>❌ Erreur</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
