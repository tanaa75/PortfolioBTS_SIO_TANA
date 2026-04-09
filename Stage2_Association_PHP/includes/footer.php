<?php
// Inclusion de la configuration si pas déjà fait
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config.php';
}
?>
<!--
===========================================
FOOTER - PIED DE PAGE
===========================================

Ce fichier contient le pied de page du site.
Il est inclus dans toutes les pages via include.

SECTIONS :
- Logo et description de l'association
- Liens de navigation
- Coordonnées de contact (📍 adresse, ✉️ email, 📞 téléphone)
- Newsletter (formulaire d'inscription)
- Liens réseaux sociaux
- Mentions légales et confidentialité

STYLES :
- Design premium avec ligne gradient animée
- Adaptation automatique mode clair/sombre
- Boutons sociaux circulaires avec hover
-->

<footer class="footer-premium position-relative" style="margin-top: auto; overflow: hidden;">
    
    <!-- Ligne décorative gradient animée -->
    <div class="footer-gradient-top"></div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row g-4 py-5">
            
            <!-- À propos -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-section">
                    <div class="d-flex align-items-center mb-4">
                         <h4 class="footer-brand mb-0">Aujourd'hui vers Demain</h4>
                    </div>
                    <p class="footer-description mb-4">
                        Notre mission est de renforcer les liens sociaux et d'apporter une aide concrète aux habitants de Noisy-le-Sec pour un avenir solidaire.
                    </p>
                    <div class="footer-social-links">
                        <a href="#" class="footer-social-btn" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="footer-social-btn" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="footer-social-btn" title="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="col-lg-2 col-md-6">
                <div class="footer-section">
                    <h5 class="footer-title mb-4">Navigation</h5>
                    <ul class="footer-menu list-unstyled">
                        <li class="mb-3">
                            <a href="<?= BASE_URL ?>index.php" class="footer-menu-link">
                                <i class="bi bi-chevron-right me-2"></i>Accueil
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="<?= BASE_URL ?>pages/actions.php" class="footer-menu-link">
                                <i class="bi bi-chevron-right me-2"></i>Nos Actions
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="<?= BASE_URL ?>index.php#benevolat" class="footer-menu-link">
                                <i class="bi bi-chevron-right me-2"></i>Devenir Bénévole
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contact -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-section">
                    <h5 class="footer-title mb-4">Nous Contacter</h5>
                    <ul class="footer-contact list-unstyled">
                        <li class="mb-3">
                            <div class="d-flex align-items-start">
                                <div class="footer-contact-icon me-3">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <div class="footer-contact-text">116 rue de l'Avenir</div>
                                    <div class="footer-contact-text">93130 Noisy-le-Sec</div>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="footer-contact-icon me-3">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <a href="mailto:contact@asso.fr" class="footer-contact-link">contact@asso.fr</a>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="footer-contact-icon me-3">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <a href="tel:0123456789" class="footer-contact-link">01 23 45 67 89</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-section">
                    <h5 class="footer-title mb-4">Newsletter</h5>
                    <p class="footer-newsletter-text mb-3">
                        Restez informé de nos actualités et événements !
                    </p>
                    <form action="#" method="POST" class="footer-newsletter-form">
                        <div class="position-relative">
                            <input type="email" class="footer-newsletter-input" placeholder="Votre email" required>
                            <button type="submit" class="footer-newsletter-btn">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Ligne de séparation -->
        <div class="footer-divider"></div>

        <!-- Copyright -->
        <div class="footer-bottom py-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="footer-copyright mb-0">
                        © 2026 <span class="fw-bold">Association Aujourd'hui vers Demain</span>
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="<?= BASE_URL ?>pages/mentions_legales.php" class="footer-legal-link me-3">Mentions Légales</a>
                    <span class="footer-separator">•</span>
                    <a href="<?= BASE_URL ?>pages/confidentialite.php" class="footer-legal-link ms-3">Confidentialité</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* FOOTER PREMIUM - BASE */
        .footer-premium {
            position: relative;
            padding-top: 0;
        }
        
        .footer-gradient-top {
            height: 5px;
            background: linear-gradient(90deg, #0d6efd 0%, #6610f2 25%, #d63384 50%, #fd7e14 75%, #ffc107 100%);
            background-size: 200% 100%;
            animation: gradientShift 8s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        
        /* Logo et Brand */
        .footer-logo-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        
        /* Boutons sociaux */
        .footer-social-links {
            display: flex;
            gap: 12px;
        }
        
        .footer-social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .footer-social-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: width 0.4s, height 0.4s, top 0.4s, left 0.4s;
        }
        
        .footer-social-btn:hover::before {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        
        .footer-social-btn:hover {
            transform: translateY(-5px) scale(1.05);
        }
        
        /* Menu */
        .footer-menu-link {
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            font-size: 0.95rem;
        }
        
        .footer-menu-link i {
            transition: transform 0.3s ease;
        }
        
        .footer-menu-link:hover i {
            transform: translateX(5px);
        }
        
        /* Contact */
        .footer-contact-icon {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.3rem;
        }
        
        .footer-contact-link {
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        /* Newsletter */
        .footer-newsletter-form {
            position: relative;
        }
        
        .footer-newsletter-input {
            width: 100%;
            padding: 14px 60px 14px 18px;
            border-radius: 12px;
            border: 2px solid transparent;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .footer-newsletter-input:focus {
            outline: none;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .footer-newsletter-btn {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 45px;
            height: 45px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .footer-newsletter-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
        }
        
        .footer-divider {
            height: 1px;
            opacity: 0.2;
        }
        
        .footer-separator {
            opacity: 0.5;
        }
        
        .footer-legal-link {
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .footer-legal-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            transition: width 0.3s ease;
        }
        
        .footer-legal-link:hover::after {
            width: 100%;
        }
        
        /* MODE CLAIR */
        [data-bs-theme="light"] .footer-premium {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        [data-bs-theme="light"] .footer-brand {
            color: #212529;
        }
        
        [data-bs-theme="light"] .footer-description,
        [data-bs-theme="light"] .footer-newsletter-text {
            color: #6c757d;
        }
        
        [data-bs-theme="light"] .footer-title {
            color: #0d6efd;
            font-weight: 600;
        }
        
        [data-bs-theme="light"] .footer-social-btn {
            background: #ffffff;
            color: #495057;
            border: 2px solid #e9ecef;
        }
        
        [data-bs-theme="light"] .footer-social-btn:hover {
            background: #0d6efd;
            color: #ffffff;
            border-color: #0d6efd;
        }
        
        [data-bs-theme="light"] .footer-menu-link {
            color: #495057;
        }
        
        [data-bs-theme="light"] .footer-menu-link:hover {
            color: #0d6efd;
        }
        

        
        [data-bs-theme="light"] .footer-contact-text {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        [data-bs-theme="light"] .footer-contact-link {
            color: #495057;
        }
        
        [data-bs-theme="light"] .footer-contact-link:hover {
            color: #0d6efd;
        }
        
        [data-bs-theme="light"] .footer-newsletter-input {
            background: #ffffff;
            color: #212529;
            border-color: #e9ecef;
        }
        
        [data-bs-theme="light"] .footer-newsletter-input:focus {
            border-color: #0d6efd;
        }
        
        [data-bs-theme="light"] .footer-divider {
            background: #dee2e6;
        }
        
        [data-bs-theme="light"] .footer-copyright {
            color: #495057;
        }
        
        [data-bs-theme="light"] .footer-legal-link {
            color: #6c757d;
        }
        
        [data-bs-theme="light"] .footer-legal-link:hover {
            color: #0d6efd;
        }
        
        [data-bs-theme="light"] .footer-legal-link::after {
            background: #0d6efd;
        }
        
        [data-bs-theme="light"] .footer-separator {
            color: #6c757d;
        }
        
        /* MODE SOMBRE */
        [data-bs-theme="dark"] .footer-premium {
            background: linear-gradient(135deg, #1a1d20 0%, #2d3238 100%);
        }
        
        [data-bs-theme="dark"] .footer-brand {
            color: #f8f9fa;
        }
        
        [data-bs-theme="dark"] .footer-description,
        [data-bs-theme="dark"] .footer-newsletter-text {
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .footer-title {
            color: #ffc107;
            font-weight: 600;
        }
        
        [data-bs-theme="dark"] .footer-social-btn {
            background: rgba(255, 255, 255, 0.05);
            color: #f8f9fa;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        [data-bs-theme="dark"] .footer-social-btn:hover {
            background: #0d6efd;
            color: #ffffff;
            border-color: #0d6efd;
        }
        
        [data-bs-theme="dark"] .footer-menu-link {
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .footer-menu-link:hover {
            color: #ffc107;
        }
        
        [data-bs-theme="dark"] .footer-contact-icon {
            background: rgba(77, 171, 247, 0.15);
            color: #4dabf7;
            border: 2px solid rgba(77, 171, 247, 0.3);
        }
        
        [data-bs-theme="dark"] .footer-contact-text {
            color: #adb5bd;
            font-size: 0.9rem;
        }
        
        [data-bs-theme="dark"] .footer-contact-link {
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .footer-contact-link:hover {
            color: #ffc107;
        }
        
        [data-bs-theme="dark"] .footer-newsletter-input {
            background: rgba(255, 255, 255, 0.08);
            color: #f8f9fa;
            border-color: rgba(255, 255, 255, 0.15);
        }
        
        [data-bs-theme="dark"] .footer-newsletter-input::placeholder {
            color: #868e96;
        }
        
        [data-bs-theme="dark"] .footer-newsletter-input:focus {
            border-color: #0d6efd;
            background: rgba(255, 255, 255, 0.12);
        }
        
        [data-bs-theme="dark"] .footer-divider {
            background: rgba(255, 255, 255, 0.1);
        }
        
        [data-bs-theme="dark"] .footer-copyright {
            color: #adb5bd;
        }
        
        [data-bs-theme="dark"] .footer-legal-link {
            color: #868e96;
        }
        
        [data-bs-theme="dark"] .footer-legal-link:hover {
            color: #ffc107;
        }
        
        [data-bs-theme="dark"] .footer-legal-link::after {
            background: #ffc107;
        }
        
        [data-bs-theme="dark"] .footer-separator {
            color: #868e96;
        }
    </style>
</footer>
