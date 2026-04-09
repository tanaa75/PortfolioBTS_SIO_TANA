<?php
/**
 * ===========================================
 * DÉCONNEXION ADMINISTRATEUR
 * ===========================================
 * 
 * Ce fichier déconnecte l'administrateur et le redirige
 * vers la page de connexion admin (login.php).
 */

// Démarrage de la session
session_start();

// Destruction de toutes les variables de session
session_destroy();

// Redirection vers la page de connexion admin
header("Location: login.php");
exit();
?>