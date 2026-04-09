<?php
/**
 * ===========================================
 * MODIFICATION D'UNE INSCRIPTION
 * ===========================================
 * 
 * Permet de modifier les informations d'une inscription
 * à l'aide aux devoirs.
 * 
 * Utilisation : edit_inscription.php?id=123
 */

session_start();
require_once '../includes/db.php';
require_once '../includes/security.php';

// Vérification admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ID de l'inscription
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: inscriptions.php");
    exit();
}

// Récupérer l'inscription
$stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ? AND message LIKE '%INSCRIPTION AIDE AUX DEVOIRS%'");
$stmt->execute([$id]);
$msg = $stmt->fetch();

if (!$msg) {
    header("Location: inscriptions.php");
    exit();
}

// Parser les données existantes
$inscription = [
    'nom' => '',
    'prenom' => '',
    'classe' => '',
    'adresse' => '',
    'telephone' => '',
    'email' => ''
];

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
    $inscription['email'] = trim($m[1]);
}

// ========== TRAITEMENT DU FORMULAIRE ==========
$success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['csrf_token']) && verifier_token_csrf($_POST['csrf_token'])) {
        // Récupérer les données
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $classe = trim($_POST['classe']);
        $adresse = trim($_POST['adresse']);
        $telephone = trim($_POST['telephone']);
        $email = trim($_POST['email']);
        
        // Validation
        if (empty($nom) || empty($prenom) || empty($classe) || empty($email)) {
            $error = "Veuillez remplir tous les champs obligatoires.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } else {
            // Reconstruire le message
            $new_message = "🔔 INSCRIPTION AIDE AUX DEVOIRS\n\nEnfant : $nom $prenom\nClasse : $classe\nAdresse : $adresse\nTéléphone : $telephone\nEmail parent : $email";
            
            // Mettre à jour
            $stmt = $pdo->prepare("UPDATE messages SET message = ?, email = ? WHERE id = ?");
            $stmt->execute([$new_message, $email, $id]);
            
            $success = true;
            
            // Mettre à jour les valeurs affichées
            $inscription = [
                'nom' => $nom,
                'prenom' => $prenom,
                'classe' => $classe,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'email' => $email
            ];
        }
    } else {
        $error = "Erreur de sécurité. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Modifier Inscription | Admin</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/mobile-responsive.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .edit-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 0;
            margin: -1.5rem -12px 30px -12px;
            color: white;
            border-radius: 0 0 30px 30px;
        }
        .edit-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .edit-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px 20px 0 0 !important;
            padding: 20px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container py-4">
        <!-- Header -->
        <div class="edit-header mb-4 px-4">
            <div class="container">
                <div class="d-flex align-items-center">
                    <a href="inscriptions.php" class="btn btn-light btn-sm me-3 rounded-pill">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="fw-bold mb-1">
                            <i class="bi bi-pencil-square me-2"></i>Modifier l'inscription
                        </h1>
                        <p class="opacity-75 mb-0">ID: <?= $id ?> | <?= htmlspecialchars($inscription['prenom'] . ' ' . $inscription['nom']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Inscription modifiée avec succès !
                <a href="inscriptions.php" class="btn btn-sm btn-success ms-3">Retour à la liste</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card edit-card">
                    <div class="card-header text-white">
                        <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>Informations de l'enfant</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <?= champ_csrf() ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                    <input type="text" name="nom" class="form-control form-control-lg" 
                                           value="<?= htmlspecialchars($inscription['nom']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="prenom" class="form-control form-control-lg" 
                                           value="<?= htmlspecialchars($inscription['prenom']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Classe <span class="text-danger">*</span></label>
                                <input type="text" name="classe" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($inscription['classe']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Adresse</label>
                                <input type="text" name="adresse" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($inscription['adresse']) ?>"
                                       placeholder="Ex: 12 rue de la Paix, 93000 Bobigny">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Téléphone</label>
                                    <input type="tel" name="telephone" class="form-control form-control-lg" 
                                           value="<?= htmlspecialchars($inscription['telephone']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email parent <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-lg" 
                                           value="<?= htmlspecialchars($inscription['email']) ?>" required>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="d-flex justify-content-between">
                                <a href="inscriptions.php" class="btn btn-outline-secondary btn-lg rounded-pill">
                                    <i class="bi bi-x-lg me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="bi bi-check-lg me-2"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script_theme.js"></script>
</body>
</html>
