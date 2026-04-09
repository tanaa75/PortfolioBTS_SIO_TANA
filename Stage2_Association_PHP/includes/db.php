<?php
/**
 * ===========================================
 * FICHIER DE CONNEXION À LA BASE DE DONNÉES
 * ===========================================
 * 
 * Ce fichier établit la connexion avec MySQL via PDO.
 * Il est inclus dans tous les fichiers qui ont besoin
 * d'accéder à la base de données.
 */

// Configuration de la base de données
$host = 'localhost';           // Serveur MySQL (localhost pour Laragon/WAMP)
$dbname = 'asso_db';          // Nom de la base de données
$username = 'root';           // Utilisateur MySQL
$password = '';               // Mot de passe (vide par défaut sur Laragon)

try {
    // Création de la connexion PDO avec le charset UTF-8
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Mode d'erreur : affiche les exceptions pour le débogage
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Si la connexion échoue, on arrête le script avec un message d'erreur
    die("Erreur de connexion : " . $e->getMessage());
}
?>