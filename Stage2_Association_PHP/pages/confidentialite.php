<?php 
/**
 * ===========================================
 * PAGE POLITIQUE DE CONFIDENTIALITÉ
 * ===========================================
 * 
 * Page obligatoire RGPD.
 * Explique comment les données personnelles sont collectées,
 * utilisées et protégées.
 * 
 * SECTIONS :
 * 1. Données collectées
 * 2. Utilisation des données
 * 3. Conservation des données
 * 4. Droits des utilisateurs
 * 5. Sécurité
 * 6. Cookies
 * 
 * DESIGN :
 * - Cartes numérotées avec thème vert
 * - Liste avec checkmarks
 * - Adaptation mode clair/sombre
 */
session_start(); 
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Politique de confidentialité de l'association Aujourd'hui vers Demain. Protection de vos données personnelles conformément au RGPD.">
    <meta name="robots" content="index, follow">
    <title>Politique de Confidentialité | Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/legal.css">
</head>
<body class="d-flex flex-column min-vh-100 privacy-page">
    
    <?php include '../includes/navbar.php'; ?>

    <!-- Header -->
    <div class="privacy-header">
        <div class="container">
            <div class="text-center privacy-title">
                <h1 class="display-4 fw-bold mb-3">Politique de Confidentialité</h1>
                <p class="lead privacy-subtitle">Protection de vos données personnelles</p>
            </div>
        </div>
    </div>

    <!-- Contenu -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Introduction -->
                <div class="privacy-section" data-aos="fade-up">
                    <p class="lead mb-0">
                        L'association <strong>Aujourd'hui vers Demain</strong> s'engage à protéger la vie privée de ses membres et utilisateurs conformément au Règlement Général sur la Protection des Données (RGPD).
                    </p>
                </div>

                <!-- Section 1 : Données collectées -->
                <div class="privacy-section" data-aos="fade-up" data-aos-delay="100">
                    <div class="d-flex align-items-start">
                        <div class="privacy-section-number">1</div>
                        <div class="flex-grow-1">
                            <h4 class="privacy-section-title">Les données que nous collectons</h4>
                            <p class="mb-3">
                                Dans le cadre de nos activités (aide aux devoirs, bénévolat, contact), nous sommes amenés à collecter les informations suivantes via nos formulaires :
                            </p>
                            <ul class="privacy-list">
                                <li><strong>Identité :</strong> Nom, Prénom, Classe de l'enfant</li>
                                <li><strong>Coordonnées :</strong> Adresse email, Numéro de téléphone</li>
                                <li><strong>Professionnel :</strong> CV pour les candidatures bénévoles</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Utilisation -->
                <div class="privacy-section" data-aos="fade-up" data-aos-delay="200">
                    <div class="d-flex align-items-start">
                        <div class="privacy-section-number">2</div>
                        <div class="flex-grow-1">
                            <h4 class="privacy-section-title">Utilisation des données</h4>
                            <p class="mb-3">Vos données sont utilisées exclusivement pour :</p>
                            <ul class="privacy-list">
                                <li>Gérer les inscriptions à l'aide aux devoirs</li>
                                <li>Traiter les candidatures de bénévolat</li>
                                <li>Répondre à vos demandes de contact</li>
                                <li>Vous envoyer des informations sur la vie de l'association (si vous l'avez accepté)</li>
                            </ul>
                            <div class="privacy-alert">
                                <strong>⚠️ Important :</strong> Nous ne vendons ni ne louons jamais vos données à des tiers.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3 : Durée -->
                <div class="privacy-section" data-aos="fade-up" data-aos-delay="300">
                    <div class="d-flex align-items-start">
                        <div class="privacy-section-number">3</div>
                        <div class="flex-grow-1">
                            <h4 class="privacy-section-title">Durée de conservation</h4>
                            <p class="mb-0">
                                Les données sont conservées uniquement le temps nécessaire à la réalisation des finalités citées ci-dessus, et pour une durée maximale de <strong>3 ans</strong> après le dernier contact.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 4 : Sécurité -->
                <div class="privacy-section" data-aos="fade-up" data-aos-delay="400">
                    <div class="d-flex align-items-start">
                        <div class="privacy-section-number">4</div>
                        <div class="flex-grow-1">
                            <h4 class="privacy-section-title">Sécurité</h4>
                            <p class="mb-0">
                                Nous mettons en œuvre des mesures de sécurité techniques (mots de passe hachés, accès sécurisé à l'administration) pour protéger vos données contre tout accès non autorisé.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 5 : Vos droits -->
                <div class="privacy-section" data-aos="fade-up" data-aos-delay="500">
                    <div class="d-flex align-items-start">
                        <div class="privacy-section-number">5</div>
                        <div class="flex-grow-1">
                            <h4 class="privacy-section-title">Vos droits</h4>
                            <p class="mb-3">
                                Conformément à la loi, vous disposez d'un droit d'accès, de rectification et de suppression de vos données.
                            </p>
                            <p class="mb-0">
                                Pour exercer ce droit, contactez-nous par mail à : <strong>contact@asso.fr</strong> ou par courrier au 116 rue de l'Avenir, 93130 Noisy-le-Sec.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../assets/js/script_theme.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>