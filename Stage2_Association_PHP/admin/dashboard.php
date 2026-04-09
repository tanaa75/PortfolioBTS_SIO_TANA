<?php
/**
 * ===========================================
 * TABLEAU DE BORD ADMINISTRATEUR
 * ===========================================
 * 
 * Page principale de l'espace admin.
 * Permet de gérer les événements de l'association.
 * 
 * Fonctionnalités :
 * - Liste de tous les événements
 * - Bouton pour ajouter un événement
 * - Boutons pour modifier/supprimer chaque événement
 * 
 * Sécurité :
 * - Accessible uniquement aux administrateurs connectés
 * - Suppression protégée par CSRF (méthode POST)
 */

// Démarrage de la session
session_start();

// Vérification de sécurité : redirection si non connecté
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../auth/login.php"); 
    exit(); 
}

// Connexion à la base de données
require_once '../includes/db.php';

// Inclusion des fonctions de sécurité pour CSRF
require_once '../includes/security.php';

// ========== SUPPRESSION D'UN ÉVÉNEMENT (via POST pour sécurité) ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    // Vérification du token CSRF
    if (isset($_POST['csrf_token']) && verifier_token_csrf($_POST['csrf_token'])) {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $pdo->prepare("DELETE FROM evenements WHERE id = ?");
        $stmt->execute([$delete_id]);
        
        // Redirection avec message de confirmation
        header("Location: dashboard.php?msg=deleted");
        exit();
    }
}

// Récupération de tous les événements (du plus récent au plus ancien)
$events = $pdo->query("SELECT * FROM evenements ORDER BY date_evenement DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Gestion des Événements | Admin - Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="bg-body-tertiary"> <?php include '../includes/navbar.php'; ?>
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h1>Gestion des Événements</h1>
            <a href="add_event.php" class="btn btn-primary">+ Nouvel Événement</a>
        </div>
        <?php if(isset($_GET['msg'])) echo "<div class='alert alert-success'>Action effectuée !</div>"; ?>
        
        <div class="table-responsive">
            <table class="table table-hover shadow rounded overflow-hidden">
                <thead class="table-dark"><tr><th>Titre</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['titre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($event['date_evenement'])) ?></td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning btn-action" aria-label="Modifier">
                                <i class="bi bi-pencil-fill" aria-hidden="true"></i> Modifier
                            </a>
                            <form method="POST" action="dashboard.php" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">
                                <?= champ_csrf() ?>
                                <input type="hidden" name="delete_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger btn-action" aria-label="Supprimer">
                                    <i class="bi bi-trash-fill" aria-hidden="true"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script_theme.js"></script>
</body>
</html>