<?php
require_once '../includes/db.php';
// Mots de passe : admin / admin123
$pass = password_hash("admin123", PASSWORD_DEFAULT);
$pdo->exec("INSERT INTO utilisateurs (identifiant, mot_de_passe) VALUES ('admin', '$pass')");
echo "Admin créé avec succès ! Tu peux supprimer ce fichier.";
?>