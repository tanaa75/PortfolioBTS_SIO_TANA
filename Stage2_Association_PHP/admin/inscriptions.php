<?php
/**
 * ===========================================
 * GESTION DES INSCRIPTIONS AIDE AUX DEVOIRS
 * ===========================================
 * 
 * Page d'administration pour gérer les inscriptions
 * à l'aide aux devoirs. Permet de :
 * - Voir toutes les inscriptions
 * - Exporter en CSV ou Excel
 * - Générer des reçus PDF
 * - Supprimer des inscriptions
 * 
 * Sécurité :
 * - Accessible uniquement aux administrateurs
 */

session_start();
require_once '../includes/db.php';
require_once '../includes/security.php';

// Vérification admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ========== SUPPRESSION (avec CSRF) ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    if (isset($_POST['csrf_token']) && verifier_token_csrf($_POST['csrf_token'])) {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$delete_id]);
        header("Location: inscriptions.php?msg=deleted");
        exit();
    }
}

// ========== RÉCUPÉRATION DES INSCRIPTIONS ==========
// On filtre les messages qui contiennent "INSCRIPTION AIDE AUX DEVOIRS"
$query = $pdo->query("SELECT * FROM messages WHERE message LIKE '%INSCRIPTION AIDE AUX DEVOIRS%' ORDER BY date_envoi DESC");
$inscriptions_raw = $query->fetchAll();

// Parser les inscriptions pour extraire les infos
$inscriptions = [];
foreach ($inscriptions_raw as $msg) {
    $inscription = [
        'id' => $msg['id'],
        'date' => $msg['date_envoi'],
        'email_contact' => $msg['email'],
        'nom' => '',
        'prenom' => '',
        'classe' => '',
        'adresse' => '',
        'telephone' => '',
        'email_parent' => ''
    ];
    
    // Extraction avec regex
    if (preg_match('/Enfant : (.+?) (.+)/', $msg['message'], $m)) {
        $inscription['nom'] = trim($m[1]);
        $inscription['prenom'] = trim($m[2]);
    }
    if (preg_match('/Classe : (.+)/', $msg['message'], $m)) {
        $inscription['classe'] = trim($m[1]);
    }
    if (preg_match('/Adresse : (.+)/', $msg['message'], $m)) {
        $inscription['adresse'] = trim($m[1]);
    }
    if (preg_match('/Téléphone : (.+)/', $msg['message'], $m)) {
        $inscription['telephone'] = trim($m[1]);
    }
    if (preg_match('/Email parent : (.+)/', $msg['message'], $m)) {
        $inscription['email_parent'] = trim($m[1]);
    }
    
    $inscriptions[] = $inscription;
}

// Statistiques
$total = count($inscriptions);
$ce_mois = 0;
$mois_actuel = date('Y-m');
foreach ($inscriptions as $i) {
    if (strpos($i['date'], $mois_actuel) === 0) {
        $ce_mois++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Inscriptions Aide aux Devoirs | Admin</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Styles spécifiques inscriptions */
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-box:hover { transform: translateY(-5px); }
        .stat-box.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-box h2 { font-size: 2.5rem; font-weight: 800; margin: 0; }
        .stat-box p { margin: 0; opacity: 0.9; }
        
        .export-btn-group .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .export-btn-group .btn:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        
        .table-inscriptions { border-radius: 16px; overflow: hidden; }
        .table-inscriptions thead { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .table-inscriptions thead th { color: white; font-weight: 600; border: none; padding: 16px; }
        .table-inscriptions tbody tr { transition: all 0.2s ease; }
        .table-inscriptions tbody tr:hover { background: rgba(102, 126, 234, 0.1); transform: scale(1.01); }
        
        .badge-classe {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .page-header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 0;
            margin: -1.5rem -12px 30px -12px;
            color: white;
            border-radius: 0 0 30px 30px;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .action-btn:hover { transform: scale(1.15); }
        
        .empty-state {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 20px;
            padding: 60px;
            text-align: center;
        }
        .empty-state i { font-size: 5rem; color: #667eea; margin-bottom: 20px; }
        
        [data-bs-theme="dark"] .empty-state {
            background: linear-gradient(135deg, #2d3436 0%, #1e272e 100%);
        }
        [data-bs-theme="dark"] .table-inscriptions tbody tr:hover {
            background: rgba(102, 126, 234, 0.2);
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container py-4">
        <!-- Header avec stats -->
        <div class="page-header-gradient mb-4 px-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="fw-bold mb-2">
                            <i class="bi bi-journal-check me-2"></i>Inscriptions Aide aux Devoirs
                        </h1>
                        <p class="opacity-75 mb-0">Gérez les demandes d'inscription des parents</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-3 mt-3 mt-lg-0">
                            <div class="col-6">
                                <div class="stat-box">
                                    <h2><?= $total ?></h2>
                                    <p><i class="bi bi-people-fill me-1"></i>Total inscrits</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box warning">
                                    <h2><?= $ce_mois ?></h2>
                                    <p><i class="bi bi-calendar-check me-1"></i>Ce mois</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message de confirmation -->
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Action effectuée avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Boutons d'export -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="export-btn-group d-flex flex-wrap gap-2">
                <a href="export_inscriptions.php?format=csv" class="btn btn-success">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Exporter CSV
                </a>
                <a href="export_inscriptions.php?format=excel" class="btn btn-primary">
                    <i class="bi bi-file-earmark-excel me-2"></i>Exporter Excel
                </a>
            </div>
            <a href="messages.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour Messagerie
            </a>
        </div>

        <!-- Tableau des inscriptions -->
        <?php if (empty($inscriptions)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3 class="fw-bold">Aucune inscription pour le moment</h3>
                <p class="text-muted">Les demandes d'inscription apparaîtront ici.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive shadow-lg">
                <table class="table table-inscriptions table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="bi bi-person-fill me-1"></i>Enfant</th>
                            <th><i class="bi bi-mortarboard me-1"></i>Classe</th>
                            <th><i class="bi bi-geo-alt me-1"></i>Adresse</th>
                            <th><i class="bi bi-telephone me-1"></i>Téléphone</th>
                            <th><i class="bi bi-envelope me-1"></i>Email</th>
                            <th><i class="bi bi-calendar me-1"></i>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inscriptions as $i): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($i['prenom'] . ' ' . $i['nom']) ?></td>
                            <td><span class="badge badge-classe"><?= htmlspecialchars($i['classe']) ?></span></td>
                            <td class="small"><?= htmlspecialchars($i['adresse']) ?></td>
                            <td>
                                <a href="tel:<?= htmlspecialchars($i['telephone']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($i['telephone']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($i['email_parent']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($i['email_parent']) ?>
                                </a>
                            </td>
                            <td><?= date('d/m/Y', strtotime($i['date'])) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Modifier -->
                                    <a href="edit_inscription.php?id=<?= $i['id'] ?>" 
                                       class="action-btn btn btn-outline-warning" 
                                       title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <!-- PDF -->
                                    <a href="generate_pdf.php?id=<?= $i['id'] ?>" 
                                       class="action-btn btn btn-outline-danger" 
                                       title="Générer Reçu PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    <!-- Email -->
                                    <a href="mailto:<?= htmlspecialchars($i['email_parent']) ?>?subject=Confirmation inscription aide aux devoirs" 
                                       class="action-btn btn btn-outline-primary" 
                                       title="Envoyer email">
                                        <i class="bi bi-envelope"></i>
                                    </a>
                                    <!-- Supprimer -->
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette inscription ?');">
                                        <?= champ_csrf() ?>
                                        <input type="hidden" name="delete_id" value="<?= $i['id'] ?>">
                                        <button type="submit" class="action-btn btn btn-outline-secondary" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Résumé en bas -->
            <div class="text-muted text-center mt-4">
                <small><i class="bi bi-info-circle me-1"></i><?= $total ?> inscription(s) au total</small>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script_theme.js"></script>
</body>
</html>
