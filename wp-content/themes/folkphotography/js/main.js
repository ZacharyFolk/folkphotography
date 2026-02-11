/**
 * FolkPhotography Theme JavaScript
 * Handles parallax scrolling, header behavior, and mobile menu
 */

(function() {
    'use strict';

    // DOM elements
    const header = document.getElementById('site-header');
    const heroSection = document.getElementById('hero-section');
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.querySelector('.menu');

    let lastScrollTop = 0;
    let ticking = false;

    /**
     * Parallax effect for hero image
     */
    function parallaxScroll() {
        if (!heroSection) return;

        const scrolled = window.pageYOffset;
        const heroHeight = heroSection.offsetHeight;
        const parallaxSpeed = parseFloat(heroSection.dataset.parallaxSpeed) || 0.5;

        if (scrolled <= heroHeight) {
            const heroImage = heroSection.querySelector('.hero-image');
            const translateY = scrolled * parallaxSpeed;
            heroImage.style.transform = `translateY(${translateY}px)`;

            // Darken overlay as we scroll
            const overlay = heroSection.querySelector('.hero-overlay');
            const opacity = Math.min(scrolled / heroHeight, 0.8);
            overlay.style.background = `linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,${opacity}) 100%)`;

            // Add scrolled class to hero
            if (scrolled > 100) {
                heroSection.classList.add('scrolled');
            } else {
                heroSection.classList.remove('scrolled');
            }
        }
    }

    /**
     * Header behavior on scroll
     */
    function handleHeaderScroll() {
        if (!header) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Add scrolled class when scrolling down
        if (scrollTop > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // Hide header when scrolling down, show when scrolling up
        if (scrollTop > lastScrollTop && scrollTop > 200) {
            // Scrolling down
            header.classList.add('hide-header');
        } else {
            // Scrolling up
            header.classList.remove('hide-header');
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }

    /**
     * Request animation frame for smooth scrolling
     */
    function requestTick() {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    }

    /**
     * Update animations
     */
    function update() {
        parallaxScroll();
        handleHeaderScroll();
        ticking = false;
    }

    /**
     * Mobile menu toggle
     */
    function toggleMobileMenu() {
        // Guard against null elements
        if (!mainNav || !menuToggle) return;
        
        mainNav.classList.toggle('active');
        menuToggle.classList.toggle('active');
    }

    /**
     * Close mobile menu when clicking outside
     */
    function closeMobileMenu(event) {
        // Guard against null menuToggle or mainNav
        if (!mainNav || !menuToggle) return;
        
        if (mainNav.classList.contains('active')) {
            if (!mainNav.contains(event.target) && !menuToggle.contains(event.target)) {
                mainNav.classList.remove('active');
                menuToggle.classList.remove('active');
            }
        }
    }

    /**
     * Close mobile menu when clicking a menu item
     */
    function handleMenuItemClick() {
        // Guard against null elements
        if (!mainNav || !menuToggle) return;
        
        if (window.innerWidth <= 768) {
            mainNav.classList.remove('active');
            menuToggle.classList.remove('active');
        }
    }

    /**
     * Initialize
     */
    function init() {
        // Scroll event listener
        window.addEventListener('scroll', requestTick, { passive: true });

        // Mobile menu toggle
        if (menuToggle) {
            menuToggle.addEventListener('click', toggleMobileMenu);
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', closeMobileMenu);

        // Close mobile menu when clicking menu items
        if (mainNav) {
            const menuItems = mainNav.querySelectorAll('a');
            menuItems.forEach(item => {
                item.addEventListener('click', handleMenuItemClick);
            });
        }

        // Initial update
        update();

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        const headerHeight = header ? header.offsetHeight : 0;
                        const targetPosition = target.offsetTop - headerHeight;
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    }

    /**
     * Initialize GLightbox for images
     */
    function initLightbox() {
        if (typeof GLightbox !== 'undefined') {
            const lightbox = GLightbox({
                touchNavigation: true,
                loop: true,
                autoplayVideos: true,
                zoomable: true,
                draggable: true,
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            init();
            initLightbox();
        });
    } else {
        init();
        initLightbox();
    }

})();
