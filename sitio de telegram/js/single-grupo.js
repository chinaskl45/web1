/**
 * JavaScript para la página individual de grupos
 */
(function () {
    'use strict';

    // Configuración y variables globales
    const config = window.telegramGroupConfig || {};
    let modal, reportForm, reportButton;

    // Inicialización cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function () {
        initializeElements();
        bindEvents();
        initializeAccessibility();
    });

    /**
     * Inicializar elementos del DOM
     */
    function initializeElements() {
        modal = document.getElementById('report-modal');
        reportForm = document.getElementById('report-form');
        reportButton = document.querySelector('.btn-report');
    }

    /**
     * Vincular eventos
     */
    function bindEvents() {
        // Evento para abrir modal de denuncia
        if (reportButton) {
            reportButton.addEventListener('click', openReportModal);
        }

        // Eventos para cerrar modal
        const closeButtons = document.querySelectorAll('.modal-close');
        closeButtons.forEach(button => {
            button.addEventListener('click', closeReportModal);
        });

        // Cerrar modal al hacer clic fuera
        if (modal) {
            modal.addEventListener('click', handleModalBackdropClick);
        }

        // Enviar formulario de denuncia
        if (reportForm) {
            reportForm.addEventListener('submit', handleReportSubmit);
        }

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', handleKeyPress);

        // Eventos para mejorar la experiencia de usuario
        initializeImageLazyLoading();
        initializeSmoothScrolling();
    }

    /**
     * Abrir modal de denuncia
     */
    function openReportModal(event) {
        event.preventDefault();

        if (!modal || !reportForm) return;

        const grupoId = this.getAttribute('data-grupo-id');
        const grupoIdInput = document.getElementById('report-grupo-id');

        if (grupoIdInput) {
            grupoIdInput.value = grupoId;
        }

        // Mostrar modal con animación
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Agregar clase para animación
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);

        // Focus en el primer campo
        const firstInput = modal.querySelector('select, input, textarea');
        if (firstInput) {
            firstInput.focus();
        }

        // Trackear evento (si tienes analytics)
        trackEvent('modal_opened', 'report_group', grupoId);
    }

    /**
     * Cerrar modal de denuncia
     */
    function closeReportModal() {
        if (!modal || !reportForm) return;

        // Animación de cierre
        modal.classList.remove('show');

        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';

            // Limpiar formulario
            reportForm.reset();

            // Remover estados de error
            clearFormErrors();

            // Devolver focus al botón que abrió el modal
            if (reportButton) {
                reportButton.focus();
            }
        }, 300);
    }

    /**
     * Manejar clic en el fondo del modal
     */
    function handleModalBackdropClick(event) {
        if (event.target === modal) {
            closeReportModal();
        }
    }

    /**
     * Manejar teclas del teclado
     */
    function handleKeyPress(event) {
        // Cerrar modal con Escape
        if (event.key === 'Escape' && modal && modal.classList.contains('show')) {
            closeReportModal();
        }

        // Navegación por teclado en el modal
        if (modal && modal.classList.contains('show')) {
            handleModalKeyNavigation(event);
        }
    }

    /**
     * Navegación por teclado en el modal
     */
    function handleModalKeyNavigation(event) {
        if (event.key !== 'Tab') return;

        const focusableElements = modal.querySelectorAll(
            'select, input, textarea, button, [tabindex]:not([tabindex="-1"])'
        );

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey) {
            // Shift + Tab
            if (document.activeElement === firstElement) {
                lastElement.focus();
                event.preventDefault();
            }
        } else {
            // Tab
            if (document.activeElement === lastElement) {
                firstElement.focus();
                event.preventDefault();
            }
        }
    }

    /**
     * Manejar envío del formulario de denuncia
     */
    async function handleReportSubmit(event) {
        event.preventDefault();

        if (!validateForm()) {
            return;
        }

        const formData = new FormData(reportForm);
        const submitButton = reportForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        // Estado de carga
        setSubmitButtonLoading(submitButton, true);

        try {
            const response = await fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success) {
                showSuccessMessage(config.messages?.success || 'Denuncia enviada correctamente.');
                closeReportModal();

                // Trackear evento exitoso
                trackEvent('form_submitted', 'report_group', formData.get('grupo_id'));
            } else {
                throw new Error(data.data || 'Error en el servidor');
            }

        } catch (error) {
            console.error('Error al enviar denuncia:', error);
            showErrorMessage(config.messages?.error || 'Error al enviar la denuncia. Inténtalo de nuevo.');
        } finally {
            setSubmitButtonLoading(submitButton, false, originalText);
        }
    }

    /**
     * Validar formulario antes del envío
     */
    function validateForm() {
        clearFormErrors();

        const motivo = document.getElementById('report-reason');
        let isValid = true;

        // Validar motivo requerido
        if (!motivo || !motivo.value.trim()) {
            showFieldError(motivo, 'Por favor selecciona un motivo.');
            isValid = false;
        }

        // Validar email si se proporciona
        const email = document.getElementById('report-email');
        if (email && email.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                showFieldError(email, 'Por favor ingresa un email válido.');
                isValid = false;
            }
        }

        return isValid;
    }

    /**
     * Mostrar error en un campo específico
     */
    function showFieldError(field, message) {
        field.classList.add('error');

        // Crear o actualizar mensaje de error
        let errorElement = field.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            field.parentNode.appendChild(errorElement);
        }

        errorElement.textContent = message;
        errorElement.style.color = '#e74c3c';
        errorElement.style.fontSize = '0.8rem';
        errorElement.style.marginTop = '0.25rem';
    }

    /**
     * Limpiar errores del formulario
     */
    function clearFormErrors() {
        const errorFields = reportForm.querySelectorAll('.error');
        const errorMessages = reportForm.querySelectorAll('.field-error');

        errorFields.forEach(field => field.classList.remove('error'));
        errorMessages.forEach(message => message.remove());
    }

    /**
     * Establecer estado de carga del botón de envío
     */
    function setSubmitButtonLoading(button, isLoading, originalText = '') {
        if (isLoading) {
            button.disabled = true;
            button.classList.add('loading');
            button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${config.messages?.sending || 'Enviando...'}`;
        } else {
            button.disabled = false;
            button.classList.remove('loading');
            button.innerHTML = originalText;
        }
    }

    /**
     * Mostrar mensaje de éxito
     */
    function showSuccessMessage(message) {
        showNotification(message, 'success');
    }

    /**
     * Mostrar mensaje de error
     */
    function showErrorMessage(message) {
        showNotification(message, 'error');
    }

    /**
     * Mostrar notificación
     */
    function showNotification(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
                <button class="notification-close" aria-label="Cerrar notificación">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Estilos inline para la notificación
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            backgroundColor: type === 'success' ? '#27ae60' : '#e74c3c',
            color: 'white',
            padding: '1rem',
            borderRadius: '8px',
            boxShadow: '0 4px 20px rgba(0,0,0,0.3)',
            zIndex: '10000',
            maxWidth: '400px',
            transform: 'translateX(100%)',
            transition: 'transform 0.3s ease'
        });

        document.body.appendChild(notification);

        // Animación de entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Evento para cerrar
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => removeNotification(notification));

        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            removeNotification(notification);
        }, 5000);
    }

    /**
     * Remover notificación
     */
    function removeNotification(notification) {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    /**
     * Inicializar carga perezosa de imágenes
     */
    function initializeImageLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Inicializar desplazamiento suave
     */
    function initializeSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

/**
 * Inicializar características de accesibilidad
 */



**
    function initializeAccessibility() {
        // Anunciar cambios dinámicos a lectores de pantalla
        createAriaLiveRegion();
        
        // Mejorar navegación por teclado
        enhanceKeyboardNavigation();
        
        // Añadir tooltips informativos
        addTooltips();
    }

    /**
     * Crear región ARIA live para anuncios
     */
    function createAriaLiveRegion() {
        const ariaLive = document.createElement('div');
        ariaLive.setAttribute('aria-live', 'polite');
        ariaLive.setAttribute('aria-atomic', 'true');
        ariaLive.className = 'sr-only';
        ariaLive.id = 'aria-live-region';
        
        Object.assign(ariaLive.style, {
            position: 'absolute',
            left: '-10000px',
            width: '1px',
            height: '1px',
            overflow: 'hidden'
        });
        
        document.body.appendChild(ariaLive);
    }

    /**
     * Anunciar mensaje a lectores de pantalla
     */
    function announceToScreenReader(message) {
        const ariaLive = document.getElementById('aria-live-region');
        if (ariaLive) {
            ariaLive.textContent = message;
        }
    }

    /**
     * Mejorar navegación por teclado
     */
    function enhanceKeyboardNavigation() {
        // Skip links para navegación rápida
        addSkipLinks();
        
        // Mejorar focus visible
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });
        
        document.addEventListener('mousedown', function() {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    /**
     * Añadir enlaces de salto para accesibilidad
     */
    function addSkipLinks() {
        const skipLinks = document.createElement('div');
        skipLinks.className = 'skip-links';
        skipLinks.innerHTML = `
            <a href="#primary" class="skip-link">Saltar al contenido principal</a>
            <a href="#grupo-descripcion-title" class="skip-link">Saltar a la descripción</a>
            <a href="#grupos-relacionados-title" class="skip-link">Saltar a grupos relacionados</a>
        `;
        
        // Estilos para skip links
        const style = document.createElement('style');
        style.textContent = `
            .skip-links {
                position: absolute;
                top: -100px;
                left: 0;
                z-index: 10001;
            }
            .skip-link {
                position: absolute;
                top: 0;
                left: -100px;
                background: var(--telegram-blue);
                color: white;
                padding: 0.5rem 1rem;
                text-decoration: none;
                border-radius: 0 0 4px 0;
                font-weight: 600;
                transition: left 0.3s ease;
            }
            .skip-link:focus {
                left: 0;
            }
        `;
        
        document.head.appendChild(style);
        document.body.insertBefore(skipLinks, document.body.firstChild);
    }

    /**
     * Añadir tooltips informativos
     */
    function addTooltips() {
        const elementsWithTooltips = [
            { selector: '.badge-destacado', text: 'Este grupo está destacado por su calidad' },
            { selector: '.btn-report', text: 'Reportar problemas con este grupo' },
            { selector: '.meta-categoria a', text: 'Ver todos los grupos de esta categoría' }
        ];

        elementsWithTooltips.forEach(item => {
            document.querySelectorAll(item.selector).forEach(element => {
                if (!element.getAttribute('title')) {
                    element.setAttribute('title', item.text);
                    element.setAttribute('aria-label', item.text);
                }
            });
        });
    }

    /**
     * Trackear eventos (si hay analytics configurado)
     */
    function trackEvent(action, category, label) {
        // Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                event_category: category,
                event_label: label
            });
        }
        
        // Google Analytics Universal
        if (typeof ga !== 'undefined') {
            ga('send', 'event', category, action, label);
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('track', 'CustomEvent', {
                action: action,
                category: category,
                label: label
            });
        }
    }

    /**
     * Funciones utilitarias
     */
    const utils = {
        // Debounce para optimizar eventos
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Throttle para eventos de scroll
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

        // Verificar si un elemento está visible
        isElementVisible: function(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        },

        // Obtener posición de scroll suave
        getScrollPosition: function() {
            return window.pageYOffset || document.documentElement.scrollTop;
        }
    };

    /**
     * Características adicionales
     */
    function initializeEnhancements() {
        // Scroll spy para navegación
        initializeScrollSpy();
        
        // Compartir en redes sociales
        initializeSocialSharing();
        
        // Copiar enlace al portapapeles
        initializeCopyLink();
        
        // Animaciones al hacer scroll
        initializeScrollAnimations();
    }

    /**
     * Scroll spy para resaltar secciones activas
     */
    function initializeScrollSpy() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.breadcrumb-item a[href^="#"]');
        
        if (sections.length === 0) return;

        const scrollHandler = utils.throttle(() => {
            const scrollPosition = utils.getScrollPosition() + 100;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    // Actualizar navegación activa
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, 100);

        window.addEventListener('scroll', scrollHandler);
    }

    /**
     * Funcionalidad de compartir en redes sociales
     */
    function initializeSocialSharing() {
        const shareButton = document.createElement('button');
        shareButton.className = 'btn btn-share';
        shareButton.innerHTML = '<i class="fas fa-share-alt"></i> Compartir';
        shareButton.setAttribute('aria-label', 'Compartir este grupo');
        
        // Insertar botón de compartir
        const accionesMain = document.querySelector('.grupo-acciones-main');
        if (accionesMain) {
            accionesMain.appendChild(shareButton);
        }

        shareButton.addEventListener('click', function() {
            if (navigator.share) {
                // API Web Share nativa
                navigator.share({
                    title: document.title,
                    text: document.querySelector('meta[name="description"]')?.content || '',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback: mostrar opciones de compartir
                showShareOptions();
            }
        });
    }

    /**
     * Mostrar opciones de compartir
     */
    function showShareOptions() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        const text = encodeURIComponent(document.querySelector('.descripcion-corta p')?.textContent || '');

        const shareOptions = [
            {
                name: 'WhatsApp',
                url: `https://wa.me/?text=${title}%20${url}`,
                icon: 'fab fa-whatsapp',
                color: '#25D366'
            },
            {
                name: 'Telegram',
                url: `https://t.me/share/url?url=${url}&text=${title}`,
                icon: 'fab fa-telegram',
                color: '#0088cc'
            },
            {
                name: 'Twitter',
                url: `https://twitter.com/intent/tweet?text=${title}&url=${url}`,
                icon: 'fab fa-twitter',
                color: '#1DA1F2'
            },
            {
                name: 'Facebook',
                url: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
                icon: 'fab fa-facebook',
                color: '#1877F2'
            }
        ];

        const shareModal = createShareModal(shareOptions);
        document.body.appendChild(shareModal);
        
        // Mostrar modal
        setTimeout(() => shareModal.classList.add('show'), 10);
    }

    /**
     * Crear modal de compartir
     */
    function createShareModal(options) {
        const modal = document.createElement('div');
        modal.className = 'modal share-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Compartir Grupo</h3>
                    <button class="modal-close" type="button">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="share-options">
                        ${options.map(option => `
                            <a href="${option.url}" 
                               target="_blank" 
                               rel="noopener" 
                               class="share-option"
                               style="--share-color: ${option.color}">
                                <i class="${option.icon}"></i>
                                <span>${option.name}</span>
                            </a>
                        `).join('')}
                    </div>
                    <div class="copy-link-section">
                        <label for="share-url">O copia el enlace:</label>
                        <div class="copy-link-group">
                            <input type="text" id="share-url" value="${window.location.href}" readonly>
                            <button class="btn btn-copy" data-clipboard-target="#share-url">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Eventos
        modal.querySelector('.modal-close').addEventListener('click', () => {
            modal.classList.remove('show');
            setTimeout(() => document.body.removeChild(modal), 300);
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
                setTimeout(() => document.body.removeChild(modal), 300);
            }
        });

        // Funcionalidad de copiar
        const copyBtn = modal.querySelector('.btn-copy');
        copyBtn.addEventListener('click', () => {
            const input = modal.querySelector('#share-url');
            input.select();
            document.execCommand('copy');
            
            copyBtn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                copyBtn.innerHTML = '<i class="fas fa-copy"></i>';
            }, 2000);
            
            announceToScreenReader('Enlace copiado al portapapeles');
        });

        return modal;
    }

    /**
     * Inicializar funcionalidad de copiar enlace
     */
    function initializeCopyLink() {
        document.querySelectorAll('[data-clipboard-target]').forEach(button => {
            button.addEventListener('click', function() {
                const targetSelector = this.getAttribute('data-clipboard-target');
                const target = document.querySelector(targetSelector);
                
                if (target) {
                    target.select();
                    try {
                        document.execCommand('copy');
                        showSuccessMessage('Enlace copiado al portapapeles');
                    } catch (err) {
                        showErrorMessage('No se pudo copiar el enlace');
                    }
                }
            });
        });
    }

    /**
     * Inicializar animaciones al hacer scroll
     */
    function initializeScrollAnimations() {
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '50px'
            });

            // Observar elementos que deben animarse
            document.querySelectorAll('.grupo-card-relacionado, .detalle-item').forEach(el => {
                el.classList.add('animate-on-scroll');
                animationObserver.observe(el);
            });
        }
    }

    /**
     * Inicializar todas las mejoras cuando el DOM esté listo
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Pequeño delay para asegurar que todo esté cargado
        setTimeout(() => {
            initializeEnhancements();
        }, 100);
    });

    // Exponer algunas funciones públicamente si es necesario
    window.TelegramGroup = {
        openReportModal: openReportModal,
        closeReportModal: closeReportModal,
        showNotification: showNotification,
        trackEvent: trackEvent
    };

})();

// CSS adicional para animaciones y mejoras (se puede mover al archivo CSS)
const additionalStyles = `
<style>
/* Estilos para animaciones de scroll */
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.animate-on-scroll.animate-in {
    opacity: 1;
    transform: translateY(0);
}

/* Estilos para navegación por teclado */
.keyboard-navigation *:focus {
    outline: 2px solid var(--telegram-blue) !important;
    outline-offset: 2px !important;
}

/* Estilos para modal de compartir */
.share-modal .share-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.share-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: var(--share-color, #0088cc);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: transform 0.2s ease;
}

.share-option:hover {
    transform: scale(1.05);
    color: white;
}

.copy-link-section {
    border-top: 1px solid #eee;
    padding-top: 1rem;
}

.copy-link-group {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.copy-link-group input {
    flex: 1;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-copy {
    padding: 0.5rem 1rem;
    background: var(--telegram-blue);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-share {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
}

.btn-share:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
    color: white;
}

/* Mejoras responsive para el botón de compartir */
@media (max-width: 768px) {
    .share-modal .share-options {
        grid-template-columns: 1fr;
    }
}

/* Estados de carga mejorados */
.btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Notificaciones mejoradas */
.notification {
    font-family: inherit;
    line-height: 1.5;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.notification-close {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    padding: 0.25rem;
    margin-left: auto;
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

.notification-close:hover {
    opacity: 1;
}

/* Estilos para campos con error */
.form-group input.error,
.form-group select.error,
.form-group textarea.error {
    border-color: #e74c3c;
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

/* Mejoras de accesibilidad */
@media (prefers-reduced-motion: reduce) {
    .animate-on-scroll,
    .btn,
    .modal,
    .notification {
        transition: none !important;
        animation: none !important;
    }
}

/* Soporte para modo oscuro */
@media (prefers-color-scheme: dark) {
    :root {
        --telegram-white: #1a1a1a;
        --telegram-dark: #ffffff;
        --telegram-gray: #cccccc;
    }
    
    .modal-content,
    .notification {
        background: #2d2d2d;
        color: #ffffff;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        background: #3d3d3d;
        border-color: #555;
        color: #ffffff;
    }
}
</style>
`;

// Insertar estilos adicionales
document.head.insertAdjacentHTML('beforeend', additionalStyles);