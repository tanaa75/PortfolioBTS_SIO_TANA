<?php 
/**
 * ===========================================
 * PAGE MENTIONS LÉGALES
 * ===========================================
 * 
 * Page obligatoire pour tout site web.
 * Contient les informations légales de l'association.
 * 
 * SECTIONS :
 * 1. Éditeur du site
 * 2. Hébergement
 * 3. Propriété intellectuelle
 * 4. Données personnelles
 * 5. Cookies
 * 6. Droit applicable
 * 
 * DESIGN :
 * - Cartes numérotées avec hover
 * - Adaptation mode clair/sombre
 */
session_start(); 
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mentions légales du site Aujourd'hui vers Demain. Informations sur l'éditeur, l'hébergeur et la propriété intellectuelle.">
    <meta name="robots" content="index, follow">
    <title>Mentions Légales | Aujourd'hui vers Demain</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2904/2904869.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/legal.css">
</head>
<body class="d-flex flex-column min-vh-100 legal-page">
    
    <?php include '../includes/navbar.php'; ?>

    <!-- Header -->
    <div class="legal-header">
        <div class="container">
            <div class="text-center legal-title">
                <h1 class="display-4 fw-bold mb-3">Mentions Légales</h1>
                <p class="lead legal-subtitle">Informations légales et réglementaires</p>
            </div>
        </div>
    </div>

    <!-- Contenu -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Section 1 : Éditeur -->
                <div class="legal-section" data-aos="fade-up">
                    <div class="d-flex align-items-start">
                        <div class="legal-section-number">1</div>
                        <div class="flex-grow-1">
                            <h4 class="legal-section-title">Éditeur du site</h4>
                            <p class="mb-3">
                                Le site internet <strong>Aujourd'hui vers Demain</strong> est édité par l'association régie par la loi du 1er juillet 1901.
                            </p>
                            <div class="legal-info-item">
                                <span class="legal-info-label">Nom de l'association :</span>
                                <span>Aujourd'hui vers Demain</span>
                            </div>
                            <div class="legal-info-item">
                                <span class="legal-info-label">Siège social :</span>
                                <span>116 rue de l'Avenir, 93130 Noisy-le-Sec</span>
                            </div>
                            <div class="legal-info-item">
                                <span class="legal-info-label">Email :</span>
                                <span>contact@asso.fr</span>
                            </div>
                            <div class="legal-info-item">
                                <span class="legal-info-label">Téléphone :</span>
                                <span>01 23 45 67 89</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Directeur -->
                <div class="legal-section" data-aos="fade-up" data-aos-delay="100">
                    <div class="d-flex align-items-start">
                        <div class="legal-section-number">2</div>
                        <div class="flex-grow-1">
                            <h4 class="legal-section-title">Directeur de la publication</h4>
                            <p class="mb-0">
                                Le directeur de la publication est le Président de l'association.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 3 : Hébergement -->
                <div class="legal-section" data-aos="fade-up" data-aos-delay="200">
                    <div class="d-flex align-items-start">
                        <div class="legal-section-number">3</div>
                        <div class="flex-grow-1">
                            <h4 class="legal-section-title">Hébergement</h4>
                            <p class="mb-3">Ce site est hébergé par :</p>
                            <div class="legal-info-item">
                                <span class="legal-info-label">Hébergeur :</span>
                                <span>OVH (exemple)</span>
                            </div>
                            <div class="legal-info-item">
                                <span class="legal-info-label">Adresse :</span>
                                <span>2 rue Kellermann, 59100 Roubaix - France</span>
                            </div>
                            <div class="legal-info-item">
                                <span class="legal-info-label">Site web :</span>
                                <a href="https://www.ovh.com" target="_blank" class="text-decoration-none">www.ovh.com</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4 : Propriété intellectuelle -->
                <div class="legal-section" data-aos="fade-up" data-aos-delay="300">
                    <div class="d-flex align-items-start">
                        <div class="legal-section-number">4</div>
                        <div class="flex-grow-1">
                            <h4 class="legal-section-title">Propriété intellectuelle</h4>
                            <p class="mb-0">
                                L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 5 : Données personnelles -->
                <div class="legal-section" data-aos="fade-up" data-aos-delay="400">
                    <div class="d-flex align-items-start">
                        <div class="legal-section-number">5</div>
                        <div class="flex-grow-1">
                            <h4 class="legal-section-title">Protection des données personnelles</h4>
                            <p class="mb-0">
                                Conformément à la loi "Informatique et Libertés" du 6 janvier 1978 modifiée et au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification et de suppression des données vous concernant. Pour exercer ce droit, veuillez nous contacter à l'adresse : <strong>contact@asso.fr</strong>
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