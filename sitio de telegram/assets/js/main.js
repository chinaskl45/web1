/**
 * main.js - JavaScript Principal del Tema Grupos de Telegram
 * Archivo principal con todas las funcionalidades interactivas del tema
 * 
 * @package GruposTelegramTheme
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Variables globales del tema
    const GruposTelegramTheme = {
        // Configuración
        config: {
            breakpoints: {
                mobile: 480,
                tablet: 768,
                desktop: 1024,
                large: 1200
            },
            animation: {
                duration: 300,
                easing: 'ease-in-out'
            },
            ajaxUrl: gruposTelegramAjax.ajaxUrl,
            nonce: gruposTelegramAjax.nonce
        },

        // Estado de la aplicación
        state: {
            mobileMenuOpen: false,
            currentBreakpoint: null,
            scrollDirection: null,
            lastScrollTop: 0
        },

        // Inicialización
        init: function() {
            console.log('🚀 Inicializando Grupos Telegram Theme');
            this.bindEvents();
            this.initComponents();
            this.checkBreakpoint();
            this.initLazyLoading();
            this.initScrollEffects();
            this.initCounters();
            console.log('✅ Tema inicializado correctamente');
        },

        // Eventos principales
        bindEvents: function() {
            $(document).ready(this.onDocumentReady.bind(this));
            $(window).on('load', this.onWindowLoad.bind(this));
            $(window).on('resize', this.onWindowResize.bind(this));
            $(window).on('scroll', this.onWindowScroll.bind(this));
            
            // Eventos de navegación
            $(document).on('click', '.mobile-menu-toggle', this.toggleMobileMenu.bind(this));
            $(document).on('click', '.menu-overlay', this.closeMobileMenu.bind(this));
            
            // Eventos de formularios
            $(document).on('submit', '.newsletter-form', this.handleNewsletterSubmit.bind(this));
            $(document).on('submit', '.contact-form', this.handleContactSubmit.bind(this));
            
            // Eventos de botones
            $(document).on('click', '.scroll-to-top', this.scrollToTop.bind(this));
            $(document).on('click', '.btn-back-to-top', this.scrollToTop.bind(this));
            
            // Enlaces suaves
            $(document).on('click', 'a[href^="#"]', this.handleSmoothScroll.bind(this));
            
            // Eventos de modales
            $(document).on('click', '[data-modal]', this.openModal.bind(this));
            $(document).on('click', '.modal-close, .modal-overlay', this.closeModal.bind(this));
            
            // Tecla ESC para cerrar modales
            $(document).on('keyup', this.handleKeyUp.bind(this));
        },

        // Cuando el documento está listo
        onDocumentReady: function() {
            this.initMobileMenu();
            this.initTooltips();
            this.initSmoothScrolling();
            this.highlightActiveMenu();
        },

        // Cuando la ventana se carga completamente
        onWindowLoad: function() {
            this.hideLoader();
            this.initAnimations();
            this.checkVisibleElements();
        },

        // Al redimensionar la ventana
        onWindowResize: function() {
            clearTimeout(this.resizeTimer);
            this.resizeTimer = setTimeout(() => {
                this.checkBreakpoint();
                this.adjustLayout();
                this.recalculateElements();
            }, 250);
        },

        // Al hacer scroll
        onWindowScroll: function() {
            this.handleScrollDirection();
            this.updateScrollProgress();
            this.checkVisibleElements();
            this.updateActiveSection();
            
            // Throttle para mejor rendimiento
            if (!this.scrollTimer) {
                this.scrollTimer = setTimeout(() => {
                    this.handleStickyHeader();
                    this.scrollTimer = null;
                }, 10);
            }
        },

        // Inicializar componentes
        initComponents: function() {
            this.initSearchFunctionality();
            this.initFilterComponents();
            this.initCarousels();
            this.initAccordions();
            this.initTabs();
            this.initProgressBars();
        },

        // Menú móvil
        initMobileMenu: function() {
            const $menu = $('.main-navigation');
            const $toggle = $('.mobile-menu-toggle');
            const $overlay = $('');
            
            if ($menu.length && $toggle.length) {
                $('body').append($overlay);
                
                // Agregar iconos a submenu items
                $menu.find('.menu-item-has-children > a').append(
                    ''
                );
                
                // Manejar submenús
                $(document).on('click', '.submenu-toggle', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const $parent = $(this).closest('.menu-item-has-children');
                    const $submenu = $parent.find('.sub-menu').first();
                    
                    $parent.toggleClass('submenu-open');
                    $submenu.slideToggle(300);
                    $(this).toggleClass('fa-chevron-down fa-chevron-up');
                });
            }
        },

        toggleMobileMenu: function(e) {
            e.preventDefault();
            
            const $body = $('body');
            const $menu = $('.main-navigation');
            const $overlay = $('.menu-overlay');
            const $toggle = $('.mobile-menu-toggle');
            
            if (this.state.mobileMenuOpen) {
                this.closeMobileMenu();
            } else {
                this.state.mobileMenuOpen = true;
                $body.addClass('mobile-menu-open');
                $menu.addClass('menu-open');
                $overlay.addClass('active');
                $toggle.addClass('active');
                
                // Animar entrada del menú
                $menu.css('transform', 'translateX(-100%)').animate({
                    transform: 'translateX(0)'
                }, this.config.animation.duration);
            }
        },

        closeMobileMenu: function() {
            const $body = $('body');
            const $menu = $('.main-navigation');
            const $overlay = $('.menu-overlay');
            const $toggle = $('.mobile-menu-toggle');
            
            this.state.mobileMenuOpen = false;
            $body.removeClass('mobile-menu-open');
            $menu.removeClass('menu-open');
            $overlay.removeClass('active');
            $toggle.removeClass('active');
            
            // Cerrar todos los submenús
            $('.menu-item-has-children').removeClass('submenu-open');
            $('.sub-menu').slideUp(200);
            $('.submenu-toggle').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        },

        // Smooth scrolling
        initSmoothScrolling: function() {
            // Configurar scroll suave para toda la página
            if (CSS.supports('scroll-behavior', 'smooth')) {
                $('html').css('scroll-behavior', 'smooth');
            }
        },

        handleSmoothScroll: function(e) {
            const href = $(e.currentTarget).attr('href');
            
            if (href.startsWith('#') && href.length > 1) {
                const target = $(href);
                
                if (target.length) {
                    e.preventDefault();
                    
                    const headerHeight = $('.site-header').outerHeight() || 0;
                    const offset = target.offset().top - headerHeight - 20;
                    
                    $('html, body').animate({
                        scrollTop: offset
                    }, 800, 'easeInOutCubic');
                    
                    // Cerrar menú móvil si está abierto
                    if (this.state.mobileMenuOpen) {
                        this.closeMobileMenu();
                    }
                }
            }
        },

        scrollToTop: function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 800, 'easeInOutCubic');
        },

        // Header sticky
        handleStickyHeader: function() {
            const $header = $('.site-header');
            const scrollTop = $(window).scrollTop();
            
            if (scrollTop > 100) {
                $header.addClass('header-scrolled');
            } else {
                $header.removeClass('header-scrolled');
            }
        },

        // Dirección del scroll
        handleScrollDirection: function() {
            const scrollTop = $(window).scrollTop();
            
            if (scrollTop > this.state.lastScrollTop && scrollTop > 100) {
                // Scrolling down
                if (this.state.scrollDirection !== 'down') {
                    this.state.scrollDirection = 'down';
                    $('body').addClass('scroll-down').removeClass('scroll-up');
                }
            } else {
                // Scrolling up
                if (this.state.scrollDirection !== 'up') {
                    this.state.scrollDirection = 'up';
                    $('body').addClass('scroll-up').removeClass('scroll-down');
                }
            }
            
            this.state.lastScrollTop = scrollTop;
        },

        // Progress del scroll
        updateScrollProgress: function() {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height() - $(window).height();
            const progress = (scrollTop / docHeight) * 100;
            
            $('.scroll-progress-bar').css('width', progress + '%');
        },

        // Sección activa
        updateActiveSection: function() {
            const scrollTop = $(window).scrollTop();
            const headerHeight = $('.site-header').outerHeight() || 0;
            
            $('section[id], .section[id]').each(function() {
                const $section = $(this);
                const sectionTop = $section.offset().top - headerHeight - 50;
                const sectionBottom = sectionTop + $section.outerHeight();
                
                if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                    const sectionId = $section.attr('id');
                    $('.main-navigation a[href="#' + sectionId + '"]')
                        .parent().addClass('current-menu-item')
                        .siblings().removeClass('current-menu-item');
                }
            });
        },

        // Destacar menú activo
        highlightActiveMenu: function() {
            const currentUrl = window.location.pathname;
            
            $('.main-navigation a').each(function() {
                const linkUrl = $(this).attr('href');
                
                if (linkUrl === currentUrl || (currentUrl.includes(linkUrl) && linkUrl !== '/')) {
                    $(this).parent().addClass('current-menu-item');
                }
            });
        },

        // Contadores animados
        initCounters: function() {
            $('.counter, .stat-number').each(function() {
                const $counter = $(this);
                const target = parseInt($counter.data('count') || $counter.text().replace(/,/g, ''));
                
                if (!isNaN(target)) {
                    $counter.data('target', target);
                    $counter.text('0');
                }
            });
        },

        animateCounter: function($counter) {
            const target = $counter.data('target');
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(function() {
                current += increment;
                
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                $counter.text(Math.floor(current).toLocaleString());
            }, 16);
        },

        // Elementos visibles
        checkVisibleElements: function() {
            $('.animate-on-scroll, .counter').each((index, element) => {
                const $element = $(element);
                
                if (this.isElementInViewport($element) && !$element.hasClass('animated')) {
                    $element.addClass('animated');
                    
                    // Si es un contador, animarlo
                    if ($element.hasClass('counter') || $element.hasClass('stat-number')) {
                        this.animateCounter($element);
                    }
                    
                    // Agregar animación con delay basado en el índice
                    setTimeout(() => {
                        $element.addClass('fade-in-up');
                    }, index * 100);
                }
            });
        },

        isElementInViewport: function($element) {
            const elementTop = $element.offset().top;
            const elementBottom = elementTop + $element.outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            return elementBottom > viewportTop && elementTop < viewportBottom;
        },

        // Lazy loading
        initLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback para navegadores sin soporte
                $('img[data-src]').each(function() {
                    $(this).attr('src', $(this).data('src')).removeClass('lazy').addClass('loaded');
                });
            }
        },

        // Búsqueda
        initSearchFunctionality: function() {
            let searchTimer;
            
            $(document).on('input', '.search-input, #grupo-search', function() {
                const $input = $(this);
                const query = $input.val();
                
                clearTimeout(searchTimer);
                
                if (query.length >= 3) {
                    searchTimer = setTimeout(() => {
                        GruposTelegramTheme.performSearch(query, $input);
                    }, 500);
                } else {
                    $('.search-results').hide();
                }
            });
            
            // Cerrar resultados al hacer clic fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-form, .search-results').length) {
                    $('.search-results').hide();
                }
            });
        },

        performSearch: function(query, $input) {
            const $form = $input.closest('form');
            const $results = $form.find('.search-results') || $('.search-results');
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'grupos_ajax_search',
                    query: query,
                    nonce: this.config.nonce
                },
                beforeSend: function() {
                    $input.addClass('searching');
                },
                success: function(response) {
                    if (response.success) {
                        GruposTelegramTheme.displaySearchResults(response.data, $results);
                    }
                },
                complete: function() {
                    $input.removeClass('searching');
                }
            });
        },

        displaySearchResults: function(results, $container) {
            if (!$container.length) {
                $container = $('');
                $('.search-form').append($container);
            }
            
            let html = '';
            
            if (results.length > 0) {
                html += '';
                results.forEach(result => {
                    html += `
                        
                            
                                
                            
                            
                                ${result.title}
                                ${result.category}
                                ${result.members} miembros
                            
                        
                    `;
                });
                html += '';
            } else {
                html = 'No se encontraron grupos';
            }
            
            $container.html(html).show();
        },

        // Formularios
        handleNewsletterSubmit: function(e) {
            e.preventDefault();
            
            const $form = $(e.target);
            const email = $form.find('input[type="email"]').val();
            
            this.submitForm($form, {
                action: 'newsletter_subscribe',
                email: email
            });
        },

        handleContactSubmit: function(e) {
            e.preventDefault();
            
            const $form = $(e.target);
            const formData = $form.serializeArray();
            
            this.submitForm($form, {
                action: 'contact_form_submit',
                form_data: formData
            });
        },

        submitForm: function($form, data) {
            const $submit = $form.find('button[type="submit"]');
            const originalText = $submit.text();
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    ...data,
                    nonce: this.config.nonce
                },
                beforeSend: function() {
                    $submit.prop('disabled', true).text('Enviando...');
                    $form.find('.form-message').remove();
                },
                success: function(response) {
                    const messageClass = response.success ? 'success' : 'error';
                    const message = `${response.data}`;
                    
                    $form.prepend(message);
                    
                    if (response.success) {
                        $form[0].reset();
                    }
                },
                error: function() {
                    const message = 'Error al enviar el formulario';
                    $form.prepend(message);
                },
                complete: function() {
                    $submit.prop('disabled', false).text(originalText);
                }
            });
        },

        // Modales
        openModal: function(e) {
            e.preventDefault();
            
            const modalId = $(e.currentTarget).data('modal');
            const $modal = $('#' + modalId);
            
            if ($modal.length) {
                $('body').addClass('modal-open');
                $modal.addClass('active');
                
                // Focus en el primer elemento focuseable
                $modal.find('input, button, a').first().focus();
            }
        },

        closeModal: function(e) {
            if ($(e.target).hasClass('modal-close') || $(e.target).hasClass('modal-overlay')) {
                $('body').removeClass('modal-open');
                $('.modal').removeClass('active');
            }
        },

        // Tooltips
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                const $element = $(this);
                const tooltipText = $element.data('tooltip');
                const $tooltip = $('' + tooltipText + '');
                
                $element.on('mouseenter', function() {
                    $('body').append($tooltip);
                    
                    const offset = $element.offset();
                    $tooltip.css({
                        top: offset.top - $tooltip.outerHeight() - 10,
                        left: offset.left + ($element.outerWidth() / 2) - ($tooltip.outerWidth() / 2)
                    }).addClass('visible');
                });
                
                $element.on('mouseleave', function() {
                    $tooltip.remove();
                });
            });
        },

        // Filtros
        initFilterComponents: function() {
            $(document).on('change', '.filter-select, .filter-checkbox', this.handleFilterChange.bind(this));
            $(document).on('click', '.filter-tag', this.toggleFilterTag.bind(this));
            $(document).on('click', '.clear-filters', this.clearAllFilters.bind(this));
        },

        handleFilterChange: function(e) {
            const $filter = $(e.target);
            const filterType = $filter.data('filter');
            const filterValue = $filter.val();
            
            this.applyFilters();
        },

        applyFilters: function() {
            const filters = {};
            
            $('.filter-select, .filter-checkbox:checked').each(function() {
                const $filter = $(this);
                const filterType = $filter.data('filter');
                const filterValue = $filter.val();
                
                if (filterValue) {
                    if (!filters[filterType]) {
                        filters[filterType] = [];
                    }
                    filters[filterType].push(filterValue);
                }
            });
            
            // Aplicar filtros a los elementos
            $('.filterable-item').each(function() {
                const $item = $(this);
                let shouldShow = true;
                
                Object.keys(filters).forEach(filterType => {
                    const itemValue = $item.data(filterType);
                    if (itemValue && !filters[filterType].includes(itemValue.toString())) {
                        shouldShow = false;
                    }
                });
                
                if (shouldShow) {
                    $item.removeClass('filtered-out').addClass('filtered-in');
                } else {
                    $item.removeClass('filtered-in').addClass('filtered-out');
                }
            });
        },

        // Utilitarios
        checkBreakpoint: function() {
            const width = $(window).width();
            let currentBreakpoint;
            
            if (width < this.config.breakpoints.mobile) {
                currentBreakpoint = 'mobile-small';
            } else if (width < this.config.breakpoints.tablet) {
                currentBreakpoint = 'mobile';
            } else if (width < this.config.breakpoints.desktop) {
                currentBreakpoint = 'tablet';
            } else if (width < this.config.breakpoints.large) {
                currentBreakpoint = 'desktop';
            } else {
                currentBreakpoint = 'large';
            }
            
            if (currentBreakpoint !== this.state.currentBreakpoint) {
                $('body').removeClass(this.state.currentBreakpoint).addClass(currentBreakpoint);
                this.state.currentBreakpoint = currentBreakpoint;
                this.onBreakpointChange(currentBreakpoint);
            }
        },

        onBreakpointChange: function(breakpoint) {
            // Cerrar menú móvil al cambiar a desktop
            if (breakpoint === 'desktop' || breakpoint === 'large') {
                this.closeMobileMenu();
            }
            
            // Reajustar layout
            this.adjustLayout();
        },

        adjustLayout: function() {
            // Ajustar altura de elementos
            this.recalculateElements();
            
            // Reposicionar elementos fijos
            this.repositionFixedElements();
        },

        recalculateElements: function() {
            // Recalcular alturas de grids
            $('.grupos-grid, .categorias-grid').each(function() {
                const $grid = $(this);
                const $items = $grid.find('.grupo-card, .categoria-card');
                
                // Reset heights
                $items.css('height', 'auto');
                
                // Calculate equal heights for current row
                if ($(window).width() > 768) {
                    const heights = [];
                    $items.each(function() {
                        heights.push($(this).outerHeight());
                    });
                    const maxHeight = Math.max(...heights);
                    $items.css('height', maxHeight + 'px');
                }
            });
        },

        repositionFixedElements: function() {
            // Reposicionar botones flotantes
            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();
            
            if (scrollTop + windowHeight > documentHeight - 100) {
                $('.floating-buttons').addClass('near-bottom');
            } else {
                $('.floating-buttons').removeClass('near-bottom');
            }
        },

        hideLoader: function() {
            $('.page-loader').fadeOut(500, function() {
                $(this).remove();
            });
        },

        initAnimations: function() {
            // Inicializar animaciones CSS
            $('.animate-on-load').each(function(index) {
                const $element = $(this);
                setTimeout(() => {
                    $element.addClass('animation-loaded');
                }, index * 100);
            });
        },

        initCarousels: function() {
            // Carruseles simples sin dependencias
            $('.simple-carousel').each(function() {
                const $carousel = $(this);
                const $items = $carousel.find('.carousel-item');
                const itemCount = $items.length;
                let currentIndex = 0;
                
                if (itemCount > 1) {
                    // Crear controles
                    const controls = `
                        
                        
                        
                    `;
                    $carousel.append(controls);
                    
                    // Crear dots
                    const $dots = $carousel.find('.carousel-dots');
                    for (let i = 0; i < itemCount; i++) {
                        $dots.append(``);
                    }
                    
                    // Eventos
                    $carousel.on('click', '.carousel-next', function() {
                        currentIndex = (currentIndex + 1) % itemCount;
                        updateCarousel();
                    });
                    
                    $carousel.on('click', '.carousel-prev', function() {
                        currentIndex = (currentIndex - 1 + itemCount) % itemCount;
                        updateCarousel();
                    });
                    
                    $carousel.on('click', '.carousel-dot', function() {
                        currentIndex = parseInt($(this).data('index'));
                        updateCarousel();
                    });
                    
                    function updateCarousel() {
                        $items.removeClass('active').eq(currentIndex).addClass('active');
                        $carousel.find('.carousel-dot').removeClass('active').eq(currentIndex).addClass('active');
                    }
                }
            });
        },

        initAccordions: function() {
            $(document).on('click', '.accordion-header', function() {
                const $header = $(this);
                const $accordion = $header.closest('.accordion-item');
                const $content = $accordion.find('.accordion-content');
                
                if ($accordion.hasClass('active')) {
                    $accordion.removeClass('active');
                    $content.slideUp(300);
                } else {
                    // Cerrar otros acordeones en el mismo grupo
                    $accordion.siblings('.accordion-item').removeClass('active')
                        .find('.accordion-content').slideUp(300);
                    
                    $accordion.addClass('active');
                    $content.slideDown(300);
                }
            });
        },

        initTabs: function() {
            $(document).on('click', '.tab-button', function() {
                const $button = $(this);
                const $tabGroup = $button.closest('.tabs');
                const target = $button.data('tab');
                
                // Activar botón
                $button.addClass('active').siblings().removeClass('active');
                
                // Mostrar contenido correspondiente
                $tabGroup.find('.tab-content').removeClass('active');
                $tabGroup.find('#' + target).addClass('active');
            });
        },

        initProgressBars: function() {
            $('.progress-bar').each(function() {
                const $bar = $(this);
                const percentage = $bar.data('percentage') || 0;
                
                $bar.find('.progress-fill').css('width', '0%').animate({
                    width: percentage + '%'
                }, 1500);
            });
        },

        handleKeyUp: function(e) {
            // ESC para cerrar modales
            if (e.keyCode === 27) {
                this.closeModal({ target: $('.modal-overlay') });
                this.closeMobileMenu();
            }
        },

        // Utilidades públicas
        utils: {
            debounce: function(func, wait, immediate) {
                let timeout;
                return function executedFunction() {
                    const context = this;
                    const args = arguments;
                    const later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    const callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            },

            throttle: function(func, limit) {
                let inThrottle;
                return function() {
                    const args = arguments;
                    const context = this;
                    if (!inThrottle) {
                        func.apply(context, args);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                };
            },

            formatNumber: function(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },

            getParameterByName: function(name, url) {
                if (!url) url = window.location.href;
                name = name.replace(/[\[\]]/g, '\\$&');
                const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
                const results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, ' '));
            }
        }
    };

    // Inicializar cuando jQuery esté listo
    $(function() {
        GruposTelegramTheme.init();
    });

    // Exponer objeto global
    window.GruposTelegramTheme = GruposTelegramTheme;

    // Agregar easing personalizado para jQuery
    $.easing.easeInOutCubic = function(x, t, b, c, d) {
        if ((t /= d / 2) < 1) return c / 2 * t * t * t + b;
        return c / 2 * ((t -= 2) * t * t + 2) + b;
    };

})(jQuery);

// Funciones de utilidad fuera del scope de jQuery
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Código copiado al portapapeles', 'success');
        });
    } else {
        // Fallback para navegadores sin soporte
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Código copiado al portapapeles', 'success');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4caf50' : '#2196f3'};
        color: white;
        padding: 12px 24px;
        border-radius: 4px;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Estilos para las animaciones de notificación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);