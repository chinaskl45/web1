/**
 * animations.js - Animaciones para Tema WordPress Grupos de Telegram
 * Versión: 1.0.0
 * Autor: Tu Nombre
 * Descripción: Animaciones JavaScript avanzadas para mejorar la UX
 */

(function($) {
    'use strict';

    // Objeto principal para las animaciones
    const GruposTelegramAnimations = {

        // Inicialización
        init: function() {
            this.setupScrollAnimations();
            this.setupCounterAnimations();
            this.setupParallaxEffects();
            this.setupLoadingAnimations();
            this.setupHoverEffects();
            this.setupStaggerAnimations();
            this.setupModalAnimations();
            this.initializeOnLoad();
        },

        // Animaciones al hacer scroll
        setupScrollAnimations: function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        
                        // Fade in up
                        if (element.classList.contains('fade-in-up')) {
                            element.classList.add('animated');
                        }
                        
                        // Slide in left
                        if (element.classList.contains('slide-in-left')) {
                            element.classList.add('animated');
                        }
                        
                        // Slide in right
                        if (element.classList.contains('slide-in-right')) {
                            element.classList.add('animated');
                        }
                        
                        // Bounce in
                        if (element.classList.contains('bounce-in-element')) {
                            element.classList.add('bounce-in');
                        }
                        
                        // Scale in
                        if (element.classList.contains('scale-in')) {
                            element.style.transform = 'scale(1)';
                            element.style.opacity = '1';
                        }
                    }
                });
            }, observerOptions);

            // Observar todos los elementos con clases de animación
            const animatedElements = document.querySelectorAll(
                '.fade-in-up, .slide-in-left, .slide-in-right, .bounce-in-element, .scale-in'
            );
            
            animatedElements.forEach(el => observer.observe(el));
        },

        // Animaciones de contadores
        setupCounterAnimations: function() {
            const counters = document.querySelectorAll('.counter-animation');
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateCounter(entry.target);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            counters.forEach(counter => counterObserver.observe(counter));
        },

        // Animar contador individual
        animateCounter: function(element) {
            const target = parseInt(element.getAttribute('data-count')) || 0;
            const duration = parseInt(element.getAttribute('data-duration')) || 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    element.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target.toLocaleString();
                }
            };

            updateCounter();
        },

        // Efectos parallax
        setupParallaxEffects: function() {
            const parallaxElements = document.querySelectorAll('.parallax-element');
            
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.5;
                
                parallaxElements.forEach(element => {
                    const speed = element.getAttribute('data-speed') || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            });
        },

        // Animaciones de carga
        setupLoadingAnimations: function() {
            // Mostrar spinner de carga
            this.showLoadingSpinner = function(container) {
                const spinner = document.createElement('div');
                spinner.className = 'loading-spinner mx-auto';
                spinner.innerHTML = '';
                container.appendChild(spinner);
                return spinner;
            };

            // Ocultar spinner de carga
            this.hideLoadingSpinner = function(spinner) {
                if (spinner && spinner.parentNode) {
                    spinner.parentNode.removeChild(spinner);
                }
            };

            // Animación de carga de páginas
            this.setupPageLoader = function() {
                const pageLoader = document.querySelector('.page-loader');
                if (pageLoader) {
                    window.addEventListener('load', () => {
                        pageLoader.style.opacity = '0';
                        setTimeout(() => {
                            pageLoader.style.display = 'none';
                        }, 500);
                    });
                }
            };
        },

        // Efectos hover dinámicos
        setupHoverEffects: function() {
            // Efecto hover para cards de grupos
            $('.grupo-card').hover(
                function() {
                    $(this).addClass('hover-scale');
                    $(this).find('.grupo-imagen img').css('transform', 'scale(1.1)');
                },
                function() {
                    $(this).removeClass('hover-scale');
                    $(this).find('.grupo-imagen img').css('transform', 'scale(1)');
                }
            );

            // Efecto hover para botones
            $('.btn-telegram, .telegram-gradient').hover(
                function() {
                    $(this).css('transform', 'translateY(-2px)');
                    $(this).css('box-shadow', '0 8px 25px rgba(0, 136, 204, 0.3)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                    $(this).css('box-shadow', '0 4px 15px rgba(0, 136, 204, 0.1)');
                }
            );

            // Efecto hover para categorías
            $('.categoria-card').hover(
                function() {
                    $(this).find('.categoria-icon').addClass('telegram-pulse');
                },
                function() {
                    $(this).find('.categoria-icon').removeClass('telegram-pulse');
                }
            );
        },

        // Animaciones escalonadas
        setupStaggerAnimations: function() {
            const staggerElements = document.querySelectorAll('.stagger-container .stagger-animation');
            
            const staggerObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const elements = entry.target.querySelectorAll('.stagger-animation');
                        elements.forEach((el, index) => {
                            setTimeout(() => {
                                el.classList.add('animated');
                            }, index * 100);
                        });
                        staggerObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            document.querySelectorAll('.stagger-container').forEach(container => {
                staggerObserver.observe(container);
            });
        },

        // Animaciones de modales
        setupModalAnimations: function() {
            // Abrir modal con animación
            this.openModal = function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                    modal.style.opacity = '0';
                    modal.style.transform = 'scale(0.8)';
                    
                    requestAnimationFrame(() => {
                        modal.style.transition = 'all 0.3s ease';
                        modal.style.opacity = '1';
                        modal.style.transform = 'scale(1)';
                    });
                }
            };

            // Cerrar modal con animación
            this.closeModal = function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.transition = 'all 0.3s ease';
                    modal.style.opacity = '0';
                    modal.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }
            };
        },

        // Animación de typewriter
        typewriterEffect: function(element, text, speed = 100) {
            element.textContent = '';
            let i = 0;
            
            const typeInterval = setInterval(() => {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                } else {
                    clearInterval(typeInterval);
                }
            }, speed);
        },

        // Animación de números con efecto wave
        waveNumbers: function(container) {
            const numbers = container.querySelectorAll('.wave-number');
            numbers.forEach((num, index) => {
                setTimeout(() => {
                    num.style.animation = 'wave 0.6s ease';
                }, index * 100);
            });
        },

        // Shake animation para errores
        shakeElement: function(element) {
            element.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                element.style.animation = '';
            }, 500);
        },

        // Smooth scroll personalizado
        smoothScrollTo: function(target, duration = 1000) {
            const targetElement = document.querySelector(target);
            if (!targetElement) return;

            const targetPosition = targetElement.offsetTop;
            const startPosition = window.pageYOffset;
            const distance = targetPosition - startPosition;
            let startTime = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const run = easeInOutQuad(timeElapsed, startPosition, distance, duration);
                window.scrollTo(0, run);
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }

            function easeInOutQuad(t, b, c, d) {
                t /= d/2;
                if (t < 1) return c/2*t*t + b;
                t--;
                return -c/2 * (t*(t-2) - 1) + b;
            }

            requestAnimationFrame(animation);
        },

        // Inicialización cuando se carga la página
        initializeOnLoad: function() {
            // Animar elementos que ya están visibles
            $(document).ready(() => {
                $('.fade-in-on-load').each(function(index) {
                    setTimeout(() => {
                        $(this).addClass('animated');
                    }, index * 200);
                });

                // Inicializar tooltips animados
                this.setupAnimatedTooltips();

                // Configurar animaciones de formularios
                this.setupFormAnimations();
            });
        },

        // Tooltips animados
        setupAnimatedTooltips: function() {
            $('[data-tooltip]').hover(
                function() {
                    const tooltipText = $(this).attr('data-tooltip');
                    const tooltip = $('' + tooltipText + '');
                    $('body').append(tooltip);
                    
                    const offset = $(this).offset();
                    tooltip.css({
                        'top': offset.top - tooltip.outerHeight() - 10,
                        'left': offset.left + ($(this).outerWidth() / 2) - (tooltip.outerWidth() / 2)
                    });
                    
                    tooltip.addClass('fade-in-up animated');
                },
                function() {
                    $('.animated-tooltip').remove();
                }
            );
        },

        // Animaciones de formularios
        setupFormAnimations: function() {
            // Animación en inputs al hacer focus
            $('input, textarea, select').focus(function() {
                $(this).parent().addClass('input-focused');
            }).blur(function() {
                if ($(this).val() === '') {
                    $(this).parent().removeClass('input-focused');
                }
            });

            // Animación de éxito en formularios
            this.showFormSuccess = function(form) {
                const successMessage = $('¡Formulario enviado correctamente!');
                form.prepend(successMessage);
                
                setTimeout(() => {
                    successMessage.fadeOut(() => successMessage.remove());
                }, 3000);
            };

            // Animación de error en formularios
            this.showFormError = function(form, message) {
                const errorMessage = $('' + message + '');
                form.prepend(errorMessage);
                
                setTimeout(() => {
                    errorMessage.fadeOut(() => errorMessage.remove());
                }, 5000);
            };
        },

        // Lazy loading con animación
        setupLazyLoading: function() {
            const lazyImages = document.querySelectorAll('img[data-src]');
            
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('fade-in');
                        imageObserver.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        }
    };

    // CSS adicional para las animaciones
    const animationStyles = `
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @keyframes wave {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .fade-in-on-load {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        
        .fade-in-on-load.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        .scale-in {
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.6s ease;
        }
        
        .slide-in-right {
            opacity: 0;
            transform: translateX(50px);
            transition: all 0.8s ease;
        }
        
        .slide-in-right.animated {
            opacity: 1;
            transform: translateX(0);
        }
        
        .animated-tooltip {
            position: absolute;
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 1000;
            pointer-events: none;
        }
        
        .form-success {
            background: #4caf50;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .form-error {
            background: #f44336;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .input-focused {
            transform: scale(1.02);
            transition: transform 0.3s ease;
        }
        
    `;

    // Inyectar estilos
    $('head').append(animationStyles);

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        GruposTelegramAnimations.init();
    });

    // Exponer el objeto global para uso externo
    window.GruposTelegramAnimations = GruposTelegramAnimations;

})(jQuery);

// Funciones de utilidad adicionales

// Función para animar elementos en secuencia
function animateSequence(elements, delay = 200) {
    elements.forEach((element, index) => {
        setTimeout(() => {
            element.classList.add('animated');
        }, index * delay);
    });
}

// Función para crear efectos de partículas
function createParticleEffect(container, particleCount = 20) {
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.cssText = `
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--telegram-blue);
            border-radius: 50%;
            pointer-events: none;
            animation: particle-float ${Math.random() * 3 + 2}s linear infinite;
            left: ${Math.random() * 100}%;
            animation-delay: ${Math.random() * 2}s;
        `;
        container.appendChild(particle);
    }
}

// Keyframes para partículas
const particleKeyframes = `
    @keyframes particle-float {
        0% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100px) rotate(360deg);
            opacity: 0;
        }
    }
`;

// Inyectar keyframes de partículas
const styleSheet = document.createElement('style');
styleSheet.textContent = particleKeyframes;
document.head.appendChild(styleSheet);