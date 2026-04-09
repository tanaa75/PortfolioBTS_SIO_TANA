<?php
/**
 * ===========================================
 * FONCTIONS DE SÉCURITÉ
 * ===========================================
 * 
 * Ce fichier contient les fonctions de sécurité :
 * - Protection CSRF (tokens)
 * - Limitation des tentatives de connexion
 * 
 * À inclure dans les fichiers qui ont des formulaires.
 */

/**
 * Génère un token CSRF unique et le stocke en session
 * À appeler dans chaque formulaire
 */
function generer_token_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie si le token CSRF envoyé est valide
 * À appeler lors du traitement du formulaire
 */
function verifier_token_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Régénère le token CSRF (après validation réussie)
 */
function regenerer_token_csrf() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Génère le champ HTML caché pour le token CSRF
 */
function champ_csrf() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generer_token_csrf()) . '">';
}

// ========================================
// LIMITATION DES TENTATIVES DE CONNEXION
// ========================================

/**
 * Vérifie si l'utilisateur est bloqué (trop de tentatives)
 * @param string $identifiant - Email ou identifiant
 * @return bool - true si bloqué
 */
function est_connexion_bloquee($identifiant) {
    $cle = 'tentatives_' . md5($identifiant);
    
    if (!isset($_SESSION[$cle])) {
        return false;
    }
    
    $data = $_SESSION[$cle];
    
    // Si plus de 5 tentatives en moins de 15 minutes → bloqué
    if ($data['count'] >= 5) {
        $temps_ecoule = time() - $data['last_attempt'];
        $temps_blocage = 15 * 60; // 15 minutes
        
        if ($temps_ecoule < $temps_blocage) {
            return true;
        } else {
            // Le blocage est terminé, on réinitialise
            unset($_SESSION[$cle]);
            return false;
        }
    }
    
    return false;
}

/**
 * Enregistre une tentative de connexion échouée
 */
function enregistrer_tentative_echec($identifiant) {
    $cle = 'tentatives_' . md5($identifiant);
    
    if (!isset($_SESSION[$cle])) {
        $_SESSION[$cle] = [
            'count' => 0,
            'last_attempt' => time()
        ];
    }
    
    $_SESSION[$cle]['count']++;
    $_SESSION[$cle]['last_attempt'] = time();
}

/**
 * Réinitialise le compteur après une connexion réussie
 */
function reinitialiser_tentatives($identifiant) {
    $cle = 'tentatives_' . md5($identifiant);
    unset($_SESSION[$cle]);
}

/**
 * Retourne le temps restant avant déblocage (en secondes)
 */
function temps_restant_blocage($identifiant) {
    $cle = 'tentatives_' . md5($identifiant);
    
    if (!isset($_SESSION[$cle])) {
        return 0;
    }
    
    $data = $_SESSION[$cle];
    $temps_ecoule = time() - $data['last_attempt'];
    $temps_blocage = 15 * 60; // 15 minutes
    
    return max(0, $temps_blocage - $temps_ecoule);
}
?>
