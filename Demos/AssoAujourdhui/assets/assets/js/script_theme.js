/**
 * ===========================================
 * SCRIPT DE GESTION DU THÈME (JOUR/NUIT)
 * ===========================================
 * 
 * Ce fichier gère le basculement entre le mode clair et sombre.
 * Le choix de l'utilisateur est sauvegardé dans localStorage
 * pour être conservé même après fermeture du navigateur.
 */

/**
 * Fonction pour basculer entre thème clair et sombre
 * Appelée quand on clique sur le bouton 🌙/☀️ dans la navbar
 */
const toggleTheme = () => {
    // On récupère l'élément HTML principal
    const html = document.documentElement;

    // On lit le thème actuel (data-bs-theme est un attribut Bootstrap 5.3+)
    const currentTheme = html.getAttribute('data-bs-theme');

    // On inverse : si c'est dark → light, sinon → dark
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    // On applique le nouveau thème
    html.setAttribute('data-bs-theme', newTheme);

    // On sauvegarde dans localStorage pour la prochaine visite
    localStorage.setItem('theme', newTheme);

    // On met à jour l'icône du bouton
    updateIcon(newTheme);
}

/**
 * Met à jour l'icône du bouton selon le thème actif
 * - Mode clair → affiche 🌙 (pour passer en sombre)
 * - Mode sombre → affiche ☀️ (pour passer en clair)
 */
const updateIcon = (theme) => {
    const icon = document.getElementById('theme-icon');

    if (theme === 'dark') {
        icon.innerHTML = '<i class="bi bi-sun-fill"></i>'; // Soleil pour revenir en mode clair
    } else {
        icon.innerHTML = '<i class="bi bi-moon-fill"></i>'; // Lune pour passer en mode sombre
    }
}

/**
 * Au chargement de la page :
 * 1. On récupère le thème sauvegardé (ou 'light' par défaut)
 * 2. On l'applique à la page
 * 3. On met à jour l'icône
 * 4. On initialise les animations AOS si disponibles
 */
document.addEventListener('DOMContentLoaded', () => {
    // Récupère le thème depuis localStorage, ou 'light' si rien n'est sauvé
    const savedTheme = localStorage.getItem('theme') || 'light';

    // Applique le thème à la page
    document.documentElement.setAttribute('data-bs-theme', savedTheme);

    // Met à jour l'icône du bouton
    updateIcon(savedTheme);

    // Initialise les animations AOS (Animate On Scroll) si la librairie est chargée
    if (typeof AOS !== 'undefined') {
        AOS.init();
    }
});