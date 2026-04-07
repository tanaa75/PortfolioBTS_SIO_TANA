/*
    Dimension by HTML5 UP
    html5up.net | @ajlkn
    Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

(function ($) {

    var $window = $(window),
        $body = $('body'),
        $wrapper = $('#wrapper'),
        $header = $('#header'),
        $footer = $('#footer'),
        $main = $('#main'),
        $main_articles = $main.children('article');

    // Breakpoints.
    breakpoints({
        xlarge: ['1281px', '1680px'],
        large: ['981px', '1280px'],
        medium: ['737px', '980px'],
        small: ['481px', '736px'],
        xsmall: ['361px', '480px'],
        xxsmall: [null, '360px']
    });

    // Play initial animations on page load.
    $window.on('load', function () {
        window.setTimeout(function () {
            $body.removeClass('is-preload');
        }, 100);
    });

    // Fix: Flexbox min-height bug on IE.
    if (browser.name == 'ie') {

        var flexboxFixTimeoutId;

        $window.on('resize.flexbox-fix', function () {

            clearTimeout(flexboxFixTimeoutId);

            flexboxFixTimeoutId = setTimeout(function () {

                if ($wrapper.prop('scrollHeight') > $window.height())
                    $wrapper.css('height', 'auto');
                else
                    $wrapper.css('height', '100vh');

            }, 250);

        }).triggerHandler('resize.flexbox-fix');

    }

    // Nav.
    var $nav = $header.children('nav'),
        $nav_li = $nav.find('li');

    // Add "middle" alignment classes if we're dealing with an even number of items.
    if ($nav_li.length % 2 == 0) {

        $nav.addClass('use-middle');
        $nav_li.eq(($nav_li.length / 2)).addClass('is-middle');

    }

    // Main.
    var delay = 325,
        locked = false;

    // Methods.
    $main._show = function (id, initial) {
        if (window.setSpaceTheme) window.setSpaceTheme(id);
        var $article = $main_articles.filter('#' + id);

        // No such article? Bail.
        if ($article.length == 0)
            return;

        // Handle lock.

        // Already locked? Speed through "show" steps w/o delays.
        if (locked || (typeof initial != 'undefined' && initial === true)) {

            // Mark as switching.
            $body.addClass('is-switching');

            // Mark as visible.
            $body.addClass('is-article-visible');

            // Deactivate all articles (just in case one's already active).
            $main_articles.removeClass('active');

            // Hide header, footer.
            $header.hide();
            $footer.hide();

            // Show main, article.
            $main.show();
            $article.show();

            // Activate article.
            $article.addClass('active');

            // Unlock.
            locked = false;

            // Unmark as switching.
            setTimeout(function () {
                $body.removeClass('is-switching');
            }, (initial ? 1000 : 0));

            return;

        }

        // Lock.
        locked = true;

        // Article already visible? Just swap articles.
        if ($body.hasClass('is-article-visible')) {

            // Deactivate current article.
            var $currentArticle = $main_articles.filter('.active');

            $currentArticle.removeClass('active');

            // Show article.
            setTimeout(function () {

                // Hide current article.
                $currentArticle.hide();

                // Show article.
                $article.show();

                // Activate article.
                setTimeout(function () {

                    $article.addClass('active');

                    // Window stuff.
                    $window
                        .scrollTop(0)
                        .triggerHandler('resize.flexbox-fix');

                    // Unlock.
                    setTimeout(function () {
                        locked = false;
                    }, delay);

                }, 25);

            }, delay);

        }

        // Otherwise, handle as normal.
        else {

            // Mark as visible.
            $body
                .addClass('is-article-visible');

            // Show article.
            setTimeout(function () {

                // Hide header, footer.
                $header.hide();
                $footer.hide();

                // Show main, article.
                $main.show();
                $article.show();

                // Activate article.
                setTimeout(function () {

                    $article.addClass('active');

                    // Window stuff.
                    $window
                        .scrollTop(0)
                        .triggerHandler('resize.flexbox-fix');

                    // Unlock.
                    setTimeout(function () {
                        locked = false;
                    }, delay);

                }, 25);

            }, delay);

        }

    };

    $main._hide = function (addState) {
        if (window.setSpaceTheme) window.setSpaceTheme('default');
        var $article = $main_articles.filter('.active');

        // Article not visible? Bail.
        if (!$body.hasClass('is-article-visible'))
            return;

        // Add state?
        if (typeof addState != 'undefined'
            && addState === true)
            history.pushState(null, null, '#');

        // Handle lock.

        // Already locked? Speed through "hide" steps w/o delays.
        if (locked) {

            // Mark as switching.
            $body.addClass('is-switching');

            // Deactivate article.
            $article.removeClass('active');

            // Hide article, main.
            $article.hide();
            $main.hide();

            // Show footer, header.
            $footer.show();
            $header.show();

            // Unmark as visible.
            $body.removeClass('is-article-visible');

            // Unlock.
            locked = false;

            // Unmark as switching.
            $body.removeClass('is-switching');

            // Window stuff.
            $window
                .scrollTop(0)
                .triggerHandler('resize.flexbox-fix');

            return;

        }

        // Lock.
        locked = true;

        // Deactivate article.
        $article.removeClass('active');

        // Hide article.
        setTimeout(function () {

            // Hide article, main.
            $article.hide();
            $main.hide();

            // Show footer, header.
            $footer.show();
            $header.show();

            // Unmark as visible.
            setTimeout(function () {

                $body.removeClass('is-article-visible');

                // Window stuff.
                $window
                    .scrollTop(0)
                    .triggerHandler('resize.flexbox-fix');

                // Unlock.
                setTimeout(function () {
                    locked = false;
                }, delay);

            }, 25);

        }, delay);


    };

    // Articles.
    $main_articles.each(function () {

        var $this = $(this);

        // Close.
        $('<div class="close">Close</div>')
            .appendTo($this)
            .on('click', function () {
                location.hash = '';
            });

        // Prevent clicks from inside article from bubbling.
        $this.on('click', function (event) {
            event.stopPropagation();
        });

    });

    // Events.
    $body.on('click', function (event) {

        // Article visible? Hide.
        if ($body.hasClass('is-article-visible'))
            $main._hide(true);

    });

    $window.on('keyup', function (event) {

        switch (event.keyCode) {

            case 27:

                // Article visible? Hide.
                if ($body.hasClass('is-article-visible'))
                    $main._hide(true);

                break;

            default:
                break;

        }

    });

    $window.on('hashchange', function (event) {

        // Empty hash?
        if (location.hash == ''
            || location.hash == '#') {

            // Prevent default.
            event.preventDefault();
            event.stopPropagation();

            // Hide.
            $main._hide();

        }

        // Otherwise, check for a matching article.
        else if ($main_articles.filter(location.hash).length > 0) {

            // Prevent default.
            event.preventDefault();
            event.stopPropagation();

            // Show article.
            $main._show(location.hash.substr(1));

        }

    });

    // Scroll restoration.
    // This prevents the page from scrolling back to the top on a hashchange.
    if ('scrollRestoration' in history)
        history.scrollRestoration = 'manual';
    else {

        var oldScrollPos = 0,
            scrollPos = 0,
            $htmlbody = $('html,body');

        $window
            .on('scroll', function () {

                oldScrollPos = scrollPos;
                scrollPos = $htmlbody.scrollTop();

            })
            .on('hashchange', function () {
                $window.scrollTop(oldScrollPos);
            });

    }

    // Initialize.

    // Hide main, articles.
    $main.hide();
    $main_articles.hide();

    // Initial article.
    if (location.hash != ''
        && location.hash != '#')
        $window.on('load', function () {
            $main._show(location.hash.substr(1), true);
        });

})(jQuery);


// ============== FOND SPATIAL 3D (THREE.JS) ==============
(function () {
    // Basic setup
    const scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(0x000000, 0.0008);

    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 2000);
    camera.position.z = 1000;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setClearColor(0x000000, 1);

    renderer.domElement.id = 'stars-canvas';
    renderer.domElement.style.position = 'fixed';
    renderer.domElement.style.top = '0';
    renderer.domElement.style.left = '0';
    renderer.domElement.style.width = '100%';
    renderer.domElement.style.height = '100%';
    renderer.domElement.style.zIndex = '0';
    renderer.domElement.style.pointerEvents = 'none';
    document.body.insertBefore(renderer.domElement, document.body.firstChild);

    // Create stars
    const starGeometry = new THREE.BufferGeometry();
    const starCount = 6000;
    const posArray = new Float32Array(starCount * 3);
    const colorArray = new Float32Array(starCount * 3);

    for (let i = 0; i < starCount * 3; i += 3) {
        posArray[i] = (Math.random() - 0.5) * 3000;
        posArray[i + 1] = (Math.random() - 0.5) * 3000;
        posArray[i + 2] = (Math.random() - 0.5) * 3000;

        // Initial colors (Default Cyan/Blue)
        colorArray[i] = 0.2; colorArray[i + 1] = 0.6; colorArray[i + 2] = 1.0;
    }

    starGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
    starGeometry.setAttribute('color', new THREE.BufferAttribute(colorArray, 3));

    const createCircleTexture = () => {
        const matCanvas = document.createElement('canvas');
        matCanvas.width = 32;
        matCanvas.height = 32;
        const matCtx = matCanvas.getContext('2d');
        const gradient = matCtx.createRadialGradient(16, 16, 0, 16, 16, 16);
        gradient.addColorStop(0, 'rgba(255,255,255,1)');
        gradient.addColorStop(0.2, 'rgba(255,255,255,0.8)');
        gradient.addColorStop(0.5, 'rgba(255,255,255,0.2)');
        gradient.addColorStop(1, 'rgba(0,0,0,0)');
        matCtx.fillStyle = gradient;
        matCtx.fillRect(0, 0, 32, 32);
        return new THREE.CanvasTexture(matCanvas);
    };

    const starMaterial = new THREE.PointsMaterial({
        size: 5.0,
        vertexColors: true,
        transparent: true,
        opacity: 0.9,
        map: createCircleTexture(),
        depthWrite: false,
        blending: THREE.AdditiveBlending
    });

    const starMesh = new THREE.Points(starGeometry, starMaterial);
    scene.add(starMesh);

    // Core dust/nebula particles
    const dustGeometry = new THREE.BufferGeometry();
    const dustCount = 2000;
    const dustPos = new Float32Array(dustCount * 3);
    for (let i = 0; i < dustCount * 3; i += 3) {
        dustPos[i] = (Math.random() - 0.5) * 2000;
        dustPos[i + 1] = (Math.random() - 0.5) * 2000;
        dustPos[i + 2] = (Math.random() - 0.5) * 2000;
    }
    dustGeometry.setAttribute('position', new THREE.BufferAttribute(dustPos, 3));
    const dustMaterial = new THREE.PointsMaterial({
        size: 2.0,
        color: 0x00f3ff,
        transparent: true,
        opacity: 0.4,
        blending: THREE.AdditiveBlending
    });
    const dustMesh = new THREE.Points(dustGeometry, dustMaterial);
    scene.add(dustMesh);

    // Themes Configuration
    const themes = {
        'default': { c1: [0.2, 0.6, 1.0], c2: [0.8, 0.2, 1.0], c3: [1.0, 1.0, 1.0], dust: 0x00f3ff, fog: 0x000000 },
        'apropos': { c1: [0.0, 0.8, 1.0], c2: [0.2, 0.4, 1.0], c3: [1.0, 1.0, 1.0], dust: 0x00aaff, fog: 0x000510 },
        'btssio': { c1: [0.8, 0.0, 1.0], c2: [1.0, 0.0, 0.4], c3: [1.0, 1.0, 1.0], dust: 0xff00ff, fog: 0x100015 },
        'projets': { c1: [0.0, 1.0, 0.5], c2: [0.8, 0.8, 0.0], c3: [1.0, 1.0, 1.0], dust: 0x00ff88, fog: 0x001505 },
        'stages': { c1: [1.0, 0.3, 0.0], c2: [1.0, 0.8, 0.0], c3: [1.0, 1.0, 1.0], dust: 0xff5500, fog: 0x150500 },
        'certifications': { c1: [1.0, 0.8, 0.0], c2: [1.0, 0.5, 0.0], c3: [1.0, 1.0, 1.0], dust: 0xffcc00, fog: 0x151000 },
        'veille': { c1: [0.0, 1.0, 0.0], c2: [0.2, 0.8, 0.2], c3: [0.8, 1.0, 0.8], dust: 0x00ff00, fog: 0x001500 },
        'contact': { c1: [1.0, 0.0, 0.2], c2: [0.8, 0.0, 0.0], c3: [1.0, 1.0, 1.0], dust: 0xff0033, fog: 0x150000 }
    };

    let currentSpeed = 1.2;
    let targetSpeed = 1.2;

    // Expose Theme Changer Globally
    window.setSpaceTheme = function (themeId) {
        const theme = themes[themeId] || themes['default'];

        // Update Star Colors
        const colors = starGeometry.attributes.color.array;
        for (let i = 0; i < starCount * 3; i += 3) {
            const rand = Math.random();
            const setC = rand < 0.33 ? theme.c1 : (rand < 0.66 ? theme.c2 : theme.c3);
            colors[i] = setC[0];
            colors[i + 1] = setC[1];
            colors[i + 2] = setC[2];
        }
        starGeometry.attributes.color.needsUpdate = true;

        // Update Dust & Fog
        dustMaterial.color.setHex(theme.dust);
        scene.fog.color.setHex(theme.fog);

        // Trigger Hyperspeed Jump
        currentSpeed = 40.0; // HYPERSPEED!
    };

    // Apply default theme initially
    window.setSpaceTheme('default');

    // Mouse Interaction
    let mouseX = 0;
    let mouseY = 0;
    let targetX = 0;
    let targetY = 0;
    let windowHalfX = window.innerWidth / 2;
    let windowHalfY = window.innerHeight / 2;

    document.addEventListener('mousemove', (event) => {
        mouseX = (event.clientX - windowHalfX);
        mouseY = (event.clientY - windowHalfY);
    });

    window.addEventListener('resize', () => {
        windowHalfX = window.innerWidth / 2;
        windowHalfY = window.innerHeight / 2;
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    const clock = new THREE.Clock();

    function animate() {
        requestAnimationFrame(animate);
        const time = clock.getElapsedTime();

        targetX = mouseX * 0.0005;
        targetY = mouseY * 0.0005;
        camera.rotation.y += 0.05 * (targetX - camera.rotation.y);
        camera.rotation.x += 0.05 * (targetY - camera.rotation.x);

        // Determine dynamic speed (interpolate back to normal after hyperspeed jump)
        currentSpeed += (targetSpeed - currentSpeed) * 0.02; // Smooth deceleration

        // At hyperspeed, stretch stars for motion blur effect
        if (currentSpeed > 5.0) {
            starMaterial.size = currentSpeed * 0.8;
        } else {
            starMaterial.size = 5.0;
        }

        const positions = starGeometry.attributes.position.array;
        for (let i = 2; i < starCount * 3; i += 3) {
            positions[i] += currentSpeed;
            if (positions[i] > 1000) {
                positions[i] = -2000;
            }
        }
        starGeometry.attributes.position.needsUpdate = true;

        dustMesh.rotation.y = time * 0.05;
        dustMesh.rotation.x = time * 0.02;

        renderer.render(scene, camera);
    }

    animate();
})();

// ============== ANIMATION D'INTRO TANA ==============
(function () {
    const introScreen = document.getElementById('intro-screen');
    if (!introScreen) return;

    const clickText = document.getElementById('click-text');
    const canvas = document.getElementById('pixel-canvas');

    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Particules pour former "TANAVONG CA"
    const particles = [];
    const text = 'CA TANAVONG';

    // Fonction pour créer les particules depuis le texte
    function createParticles() {
        const fontSize = window.innerWidth < 700 ? 55 : 120;
        ctx.font = 'bold ' + fontSize + 'px Arial';
        ctx.fillStyle = '#fff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text, canvas.width / 2, canvas.height / 2);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Échantillonne les pixels du texte
        for (let y = 0; y < canvas.height; y += 4) {
            for (let x = 0; x < canvas.width; x += 4) {
                const index = (y * canvas.width + x) * 4;
                const alpha = imageData.data[index + 3];

                if (alpha > 128) {
                    particles.push({
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height,
                        targetX: x,
                        targetY: y,
                        size: Math.random() * 2 + 1,
                        speedX: 0,
                        speedY: 0
                    });
                }
            }
        }
    }

    // Animation des particules
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        particles.forEach((p) => {
            // Calcule la direction vers la cible
            const dx = p.targetX - p.x;
            const dy = p.targetY - p.y;

            // Ajoute de l'inertie
            p.speedX += dx * 0.02;
            p.speedY += dy * 0.02;

            // Friction
            p.speedX *= 0.9;
            p.speedY *= 0.9;

            // Déplace la particule
            p.x += p.speedX;
            p.y += p.speedY;

            // Dessine la particule
            ctx.fillStyle = `rgba(255, 255, 255, ${Math.random() * 0.5 + 0.5})`;
            ctx.fillRect(p.x, p.y, p.size, p.size);
        });

        requestAnimationFrame(animate);
    }

    // Gestion du clic
    let animationStarted = false;
    introScreen.addEventListener('click', function () {
        if (!animationStarted) {
            animationStarted = true;
            clickText.style.display = 'none';
            canvas.style.display = 'block';

            createParticles();
            animate();

            // Après 2.5s, fade out et affiche le portfolio
            setTimeout(() => {
                introScreen.classList.add('hidden');
                document.body.classList.remove('intro-active');

                // Supprime l'écran d'intro après la transition
                setTimeout(() => {
                    introScreen.remove();
                }, 1000);
            }, 2500);
        }
    });
})();

// ============== ANIMATIONS AU SCROLL POUR LA FRISE CHRONOLOGIQUE ==============

(function () {
    'use strict';

    // Observer pour les animations au scroll
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -100px 0px'
    };

    const animateOnScroll = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observer tous les éléments de la timeline
    document.addEventListener('DOMContentLoaded', function () {
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach(item => {
            animateOnScroll.observe(item);
        });
    });

    // ============== EFFET SLIME SUR LES CERTIFICATIONS ==============

    document.querySelectorAll('.cert-bubble').forEach(bubble => {
        let isAnimating = false;

        bubble.addEventListener('mouseenter', function () {
            if (!isAnimating) {
                isAnimating = true;
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = '';
                    isAnimating = false;
                }, 50);
            }
        });

        // Effet de vibration légère au clic
        bubble.addEventListener('click', function () {
            this.style.animation = 'shake 0.5s ease';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        });
    });

    // Animation shake pour le clic sur les certifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);

    // ============== EFFET PARALLAXE LÉGER SUR LES CARDS DE LA TIMELINE ==============

    document.querySelectorAll('.timeline-card').forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
        });

        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
        });
    });

    // ============== COMPTEUR ANIMÉ POUR LES DATES ==============

    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Observer pour animer les années de la timeline
    const yearObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                const year = parseInt(entry.target.textContent);
                if (!isNaN(year)) {
                    entry.target.textContent = '0';
                    animateValue(entry.target, 2020, year, 1000);
                    entry.target.dataset.animated = 'true';
                }
            }
        });
    }, { threshold: 0.5 });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.timeline-date').forEach(date => {
            yearObserver.observe(date);
        });
    });

    // Tool badges effect
    document.querySelectorAll('.tool-badge').forEach(badge => {
        badge.addEventListener('mouseenter', function () {
            this.style.boxShadow = '0 0 20px rgba(0, 243, 255, 0.6)';
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'all 0.3s ease';
        });

        badge.addEventListener('mouseleave', function () {
            this.style.boxShadow = '';
            this.style.transform = '';
        });
    });

    // Timeline link effect
    document.querySelectorAll('.timeline-link').forEach(link => {
        link.addEventListener('click', function (e) {
            this.style.transform = 'translateX(10px)';
            setTimeout(() => {
                this.style.transform = '';
            }, 300);
        });
    });

    // Typewriter effect
    function typeWriter(element, text, speed = 50) {
        let i = 0;
        element.textContent = '';

        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }

        type();
    }

    // Threat cards pulse effect
    document.querySelectorAll('.threat-card').forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.animation = 'pulse 1.5s ease-in-out infinite';
        });

        card.addEventListener('mouseleave', function () {
            this.style.animation = '';
        });
    });

    // Ajouter l'animation pulse
    const pulseStyle = document.createElement('style');
    pulseStyle.textContent = `
        @keyframes pulse {
            0%, 100% { box-shadow: 0 10px 30px rgba(157, 0, 255, 0.3); }
            50% { box-shadow: 0 10px 40px rgba(157, 0, 255, 0.6); }
        }
    `;
    document.head.appendChild(pulseStyle);

    // Console log stylé
    console.log('%c🔥 Portfolio Tanavong CA - BTS SIO SLAM',
        'color: #00f3ff; font-size: 20px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,243,255,0.5);');
    console.log('%c✨ Animations & interactions chargées avec succès !',
        'color: #9d00ff; font-size: 14px;');

})();