<?php
/**
 * ===========================================
 * DÉCONNEXION MEMBRE
 * ===========================================
 * 
 * Ce fichier déconnecte un MEMBRE (pas l'admin).
 * On supprime uniquement les variables de session du membre,
 * au cas où un admin serait aussi connecté en parallèle.
 */

// Démarrage de la session
session_start();

// Suppression des variables de session du membre uniquement
unset($_SESSION['membre_id']);
unset($_SESSION['membre_nom']);
unset($_SESSION['membre_email']);

// Note : si on voulait tout détruire, on utiliserait session_destroy();

// Redirection vers la page d'accueil
header("Location: ../index.php");
exit();
?>