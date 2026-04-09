/**
 * ===========================================
 * SCRIPTS GALERIE PHOTOS - AUJOURD'HUI VERS DEMAIN
 * ===========================================
 */

// Initialisation AOS
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, once: true });
    }
});

/**
 * Ouvre la lightbox avec l'image sélectionnée
 * @param {string} src - Chemin de l'image
 * @param {string} title - Titre de la photo
 * @param {string} date - Date de la photo
 */
function openLightbox(src, title, date) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-title').textContent = title;
    document.getElementById('lightbox-date').textContent = date;
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Ferme la lightbox
 */
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Fermer la lightbox avec la touche Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLightbox();
});

// Fermer la lightbox en cliquant en dehors de l'image
document.addEventListener('DOMContentLoaded', function () {
    const lightbox = document.getElementById('lightbox');
    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === this) closeLightbox();
        });
    }
});
