/**
 * Mimos Korea Design - Main JavaScript
 * Modern, clean, and secure JavaScript for e-commerce
 */

(function () {
    'use strict';

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function () {
        initializeTheme();
    });

    /**
     * Initialize theme functionality
     */
    function initializeTheme() {
        initMobileMenu();
        initScrollToTop();
        initSmoothScroll();
        initFormValidation();
        initHeroCarousel();
    }

    /**
     * Mobile menu functionality
     */
    function initMobileMenu() {
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', function () {
                toggleMobileMenu();
            });
        }
    }

    function toggleMobileMenu() {
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');

        if (mobileMenu && mobileMenuToggle) {
            mobileMenu.classList.toggle('hidden');

            // Update aria-expanded for accessibility
            const isExpanded = !mobileMenu.classList.contains('hidden');
            mobileMenuToggle.classList.toggle('active', isExpanded);
            mobileMenuToggle.setAttribute('aria-expanded', isExpanded);
        }
    }

    window.toggleMobileMenu = toggleMobileMenu;

    /**
     * Scroll to top functionality
     */
    function initScrollToTop() {
        const scrollToTopBtn = document.querySelector('.scroll-to-top');

        if (scrollToTopBtn) {
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.add('visible');
                } else {
                    scrollToTopBtn.classList.remove('visible');
                }
            });

            scrollToTopBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');

        anchorLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /**
     * Basic form validation
     */
    function initFormValidation() {
        const forms = document.querySelectorAll('form');

        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(function (field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                    } else {
                        field.classList.remove('error');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Hero Carousel functionality with drag and scroll
     */
    function initHeroCarousel() {
        const carousel = document.getElementById('heroCarouselContainer');
        
        if (!carousel) return;

        let isDown = false;
        let startX;
        let scrollLeft;
        let isDragging = false;

        // Mouse events
        carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            isDragging = false;
            carousel.classList.add('active');
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
            e.preventDefault();
        });

        carousel.addEventListener('mouseleave', () => {
            isDown = false;
            carousel.classList.remove('active');
        });

        carousel.addEventListener('mouseup', () => {
            isDown = false;
            carousel.classList.remove('active');
            
            // Pequeno delay para distinguir entre click e drag
            setTimeout(() => {
                isDragging = false;
            }, 100);
        });

        carousel.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            isDragging = true;
            
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2; // Velocidade do scroll
            carousel.scrollLeft = scrollLeft - walk;
        });

        // Touch events para mobile
        carousel.addEventListener('touchstart', (e) => {
            isDown = true;
            isDragging = false;
            startX = e.touches[0].pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });

        carousel.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            isDragging = true;
            
            const x = e.touches[0].pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2;
            carousel.scrollLeft = scrollLeft - walk;
        });

        carousel.addEventListener('touchend', () => {
            isDown = false;
            
            setTimeout(() => {
                isDragging = false;
            }, 100);
        });

        // Prevenir clique nos links durante o drag
        const carouselLinks = carousel.querySelectorAll('.hero-carousel-link');
        carouselLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (isDragging) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });

        // Scroll horizontal apenas com Shift+wheel ou quando o carrossel já está sendo usado
        carousel.addEventListener('wheel', (e) => {
            // Só intercepta o scroll se:
            // 1. Shift está pressionado (scroll horizontal intencional)
            // 2. Ou se o scroll é horizontal (deltaX)
            // 3. Ou se o carrossel já não pode mais rolar verticalmente (está no topo/bottom da página)
            if (e.shiftKey || Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                e.preventDefault();
                const scrollAmount = e.deltaX !== 0 ? e.deltaX : e.deltaY;
                carousel.scrollLeft += scrollAmount;
            }
            // Caso contrário, deixa o scroll vertical normal da página acontecer
        });
    }

})();
