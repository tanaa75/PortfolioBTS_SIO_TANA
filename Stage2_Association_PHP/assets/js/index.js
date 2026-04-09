/**
 * ===========================================
 * SCRIPTS PAGE D'ACCUEIL - AUJOURD'HUI VERS DEMAIN
 * ===========================================
 * 
 * Animations et interactions de la page d'accueil.
 * - Animation des compteurs (statistiques)
 * - Intersection Observer pour déclencher les animations
 * - Animation au clic sur les cartes d'événements
 * - Initialisation AOS
 */

/**
 * Anime un compteur de 0 jusqu'à sa valeur cible
 * @param {HTMLElement} element - Élément contenant le compteur
 */
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000; // 2 secondes
    const start = parseInt(element.textContent);
    const increment = (target - start) / (duration / 16); // 60 FPS
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= target) || (increment < 0 && current <= target)) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

/**
 * Configuration de l'Intersection Observer
 * Déclenche les animations quand les éléments deviennent visibles
 */
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.counter');
            counters.forEach(counter => {
                if (!counter.classList.contains('animated')) {
                    counter.classList.add('animated');
                    animateCounter(counter);
                }
            });
        }
    });
}, observerOptions);

/**
 * Initialisation au chargement du DOM
 */
document.addEventListener('DOMContentLoaded', () => {
    // Observer la section des statistiques
    const statsSection = document.querySelector('.bg-warning');
    if (statsSection) {
        observer.observe(statsSection);
    }

    // Initialiser AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    }

    // Animation au clic sur les cartes d'événements
    document.querySelectorAll('.card-event').forEach(card => {
        card.addEventListener('click', function (e) {
            // Ajoute la classe pour l'animation pulse
            this.classList.add('clicked');

            // Retire la classe après l'animation
            setTimeout(() => {
                this.classList.remove('clicked');
            }, 600);
        });
    });
});
