/**
 * search.js - Funcionalidades de Búsqueda Avanzada
 * Tema WordPress: Grupos de Telegram
 * Versión: 1.0.0
 * 
 * Funcionalidades:
 * - Búsqueda en tiempo real con AJAX
 * - Autocompletado inteligente
 * - Filtros dinámicos
 * - Historial de búsquedas
 * - Resultados sin recargar página
 */

(function($) {
    'use strict';

    // Configuración global
    const GruposTelegramSearch = {
        // Configuración
        config: {
            ajaxUrl: gruposTelegramAjax.ajaxUrl,
            nonce: gruposTelegramAjax.nonce,
            debounceDelay: 300,
            minSearchLength: 2,
            maxResults: 20,
            cacheExpiry: 300000, // 5 minutos
            historyLimit: 10
        },

        // Cache de resultados
        cache: new Map(),
        
        // Elementos del DOM
        elements: {
            searchInput: null,
            searchButton: null,
            searchResults: null,
            searchSuggestions: null,
            filterCategory: null,
            filterSort: null,
            filterStatus: null,
            searchHistory: null,
            loadingSpinner: null,
            noResults: null
        },

        // Estado actual
        state: {
            currentQuery: '',
            currentFilters: {},
            isSearching: false,
            lastSearchTime: 0,
            selectedSuggestion: -1,
            resultsPage: 1
        },

        // Inicialización
        init: function() {
            this.bindElements();
            this.bindEvents();
            this.loadSearchHistory();
            this.initializeFilters();
            this.checkUrlParams();
            
            console.log('🔍 Search module initialized');
        },

        // Vincular elementos del DOM
        bindElements: function() {
            this.elements.searchInput = $('.search-input, #hero-search, #grupo-search, #categoria-search');
            this.elements.searchButton = $('.search-button, .search-btn');
            this.elements.searchResults = $('#search-results, .search-results');
            this.elements.searchSuggestions = $('#search-suggestions, .search-suggestions');
            this.elements.filterCategory = $('#categoria-filter, .filter-category select');
            this.elements.filterSort = $('#grupos-sort, .filter-sort select');
            this.elements.filterStatus = $('#status-filter, .filter-status select');
            this.elements.searchHistory = $('.search-history');
            this.elements.loadingSpinner = $('.search-loading, .loading-spinner');
            this.elements.noResults = $('.no-results, .no-grupos-found');
        },

        // Vincular eventos
        bindEvents: function() {
            const self = this;

            // Búsqueda en tiempo real
            this.elements.searchInput.on('input', this.debounce(function() {
                self.performSearch($(this).val());
            }, this.config.debounceDelay));

            // Búsqueda al enviar formulario
            $('form[role="search"], .search-form').on('submit', function(e) {
                e.preventDefault();
                const query = $(this).find('input[type="search"]').val();
                self.performSearch(query, true);
            });

            // Navegación con teclado en sugerencias
            this.elements.searchInput.on('keydown', function(e) {
                self.handleKeyboardNavigation(e);
            });

            // Click en sugerencias
            $(document).on('click', '.search-suggestion-item', function() {
                const query = $(this).data('query');
                self.selectSuggestion(query);
            });

            // Filtros dinámicos
            this.elements.filterCategory.add(this.elements.filterSort).add(this.elements.filterStatus)
                .on('change', function() {
                    self.updateFilters();
                    self.performSearch(self.state.currentQuery, false);
                });

            // Limpiar búsqueda
            $(document).on('click', '.clear-search', function() {
                self.clearSearch();
            });

            // Historial de búsquedas
            $(document).on('click', '.search-history-item', function() {
                const query = $(this).data('query');
                self.elements.searchInput.val(query);
                self.performSearch(query, true);
            });

            // Scroll infinito (si está habilitado)
            if (this.elements.searchResults.length) {
                $(window).on('scroll', this.debounce(function() {
                    self.handleInfiniteScroll();
                }, 100));
            }

            // Click fuera para cerrar sugerencias
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-container, .search-suggestions').length) {
                    self.hideSuggestions();
                }
            });
        },

        // Realizar búsqueda
        performSearch: function(query, updateUrl = false) {
            query = query ? query.trim() : '';
            
            // Validar longitud mínima
            if (query.length > 0 && query.length < this.config.minSearchLength) {
                this.showSuggestions([]);
                return;
            }

            // Actualizar estado
            this.state.currentQuery = query;
            this.state.lastSearchTime = Date.now();

            // Si la consulta está vacía, limpiar resultados
            if (!query) {
                this.clearResults();
                this.hideSuggestions();
                return;
            }

            // Buscar en cache primero
            const cacheKey = this.generateCacheKey(query, this.state.currentFilters);
            const cachedResult = this.getCachedResult(cacheKey);
            
            if (cachedResult) {
                this.displayResults(cachedResult);
                this.showSuggestions(cachedResult.suggestions || []);
                return;
            }

            // Mostrar loading
            this.showLoading();

            // Realizar búsqueda AJAX
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'grupos_search',
                    nonce: this.config.nonce,
                    query: query,
                    filters: this.state.currentFilters,
                    page: this.state.resultsPage,
                    per_page: this.config.maxResults
                },
                success: function(response) {
                    if (response.success) {
                        // Guardar en cache
                        this.setCachedResult(cacheKey, response.data);
                        
                        // Mostrar resultados
                        this.displayResults(response.data);
                        this.showSuggestions(response.data.suggestions || []);
                        
                        // Actualizar historial
                        this.addToSearchHistory(query);
                        
                        // Actualizar URL si es necesario
                        if (updateUrl) {
                            this.updateUrl(query, this.state.currentFilters);
                        }
                    } else {
                        this.showError(response.data || 'Error en la búsqueda');
                    }
                }.bind(this),
                error: function() {
                    this.showError('Error de conexión. Inténtalo de nuevo.');
                }.bind(this),
                complete: function() {
                    this.hideLoading();
                }.bind(this)
            });
        },

        // Mostrar resultados
        displayResults: function(data) {
            const resultsContainer = this.elements.searchResults;
            
            if (!resultsContainer.length) return;

            let html = '';

            if (data.grupos && data.grupos.length > 0) {
                // Header de resultados
                html += `
                    
                        
                            
                            ${data.total} resultados para "${this.state.currentQuery}"
                        
                        
                            ${data.grupos.length} de ${data.total}
                            ${data.search_time}ms
                        
                    
                `;

                // Grid de resultados
                html += '';
                
                data.grupos.forEach(grupo => {
                    html += this.generateGrupoCard(grupo);
                });
                
                html += '';

                // Paginación si hay más resultados
                if (data.total > data.grupos.length) {
                    html += this.generatePagination(data);
                }
            } else {
                html = this.generateNoResults();
            }

            resultsContainer.html(html);
            this.animateResults();
        },

        // Generar card de grupo
        generateGrupoCard: function(grupo) {
            const categoryIcon = this.getCategoryIcon(grupo.categoria);
            const memberCount = this.formatNumber(grupo.miembros);
            
            return `
                
                    
                        ${grupo.imagen ? 
                            `` :
                            `
                                
                            `
                        }
                        
                            
                        
                    
                    
                    
                        
                            
                                ${grupo.titulo}
                            
                            ${grupo.categoria}
                        
                        
                        
                            ${grupo.descripcion}
                        
                        
                        
                            
                                
                                ${memberCount} miembros
                            
                            
                                
                                ${grupo.estado}
                            
                            
                                
                                ${grupo.fecha}
                            
                        
                        
                        ${grupo.etiquetas ? `
                            
                                ${grupo.etiquetas.map(tag => `${tag}`).join('')}
                            
                        ` : ''}
                        
                        
                            
                                
                                Ver Detalles
                            
                            ${grupo.enlace_telegram ? `
                                
                                    
                                    Unirse
                                
                            ` : ''}
                        
                    
                
            `;
        },

        // Mostrar sugerencias de autocompletado
        showSuggestions: function(suggestions) {
            const suggestionsContainer = this.elements.searchSuggestions;
            
            if (!suggestionsContainer.length || suggestions.length === 0) {
                this.hideSuggestions();
                return;
            }

            let html = '';
            
            suggestions.forEach((suggestion, index) => {
                html += `
                    
                        
                            
                        
                        
                            ${suggestion.title}
                            ${suggestion.meta}
                        
                        
                            
                        
                    
                `;
            });
            
            html += '';
            
            suggestionsContainer.html(html).show();
        },

        // Ocultar sugerencias
        hideSuggestions: function() {
            this.elements.searchSuggestions.hide();
            this.state.selectedSuggestion = -1;
        },

        // Navegación con teclado
        handleKeyboardNavigation: function(e) {
            const suggestions = $('.search-suggestion-item');
            
            if (suggestions.length === 0) return;

            switch(e.which) {
                case 38: // Flecha arriba
                    e.preventDefault();
                    this.state.selectedSuggestion = Math.max(-1, this.state.selectedSuggestion - 1);
                    this.updateSuggestionSelection();
                    break;
                    
                case 40: // Flecha abajo
                    e.preventDefault();
                    this.state.selectedSuggestion = Math.min(suggestions.length - 1, this.state.selectedSuggestion + 1);
                    this.updateSuggestionSelection();
                    break;
                    
                case 13: // Enter
                    e.preventDefault();
                    if (this.state.selectedSuggestion >= 0) {
                        const selectedQuery = suggestions.eq(this.state.selectedSuggestion).data('query');
                        this.selectSuggestion(selectedQuery);
                    } else {
                        this.performSearch(this.elements.searchInput.val(), true);
                    }
                    break;
                    
                case 27: // Escape
                    this.hideSuggestions();
                    break;
            }
        },

        // Actualizar selección de sugerencias
        updateSuggestionSelection: function() {
            $('.search-suggestion-item').removeClass('selected');
            if (this.state.selectedSuggestion >= 0) {
                $('.search-suggestion-item').eq(this.state.selectedSuggestion).addClass('selected');
            }
        },

        // Seleccionar sugerencia
        selectSuggestion: function(query) {
            this.elements.searchInput.val(query);
            this.hideSuggestions();
            this.performSearch(query, true);
        },

        // Actualizar filtros
        updateFilters: function() {
            this.state.currentFilters = {
                category: this.elements.filterCategory.val() || '',
                sort: this.elements.filterSort.val() || 'relevance',
                status: this.elements.filterStatus.val() || ''
            };
        },

        // Gestión de historial de búsquedas
        addToSearchHistory: function(query) {
            if (!query || query.length < this.config.minSearchLength) return;

            let history = this.getSearchHistory();
            
            // Remover si ya existe
            history = history.filter(item => item.query !== query);
            
            // Añadir al principio
            history.unshift({
                query: query,
                timestamp: Date.now(),
                filters: {...this.state.currentFilters}
            });
            
            // Limitar tamaño
            history = history.slice(0, this.config.historyLimit);
            
            // Guardar
            localStorage.setItem('grupos_search_history', JSON.stringify(history));
            this.updateSearchHistoryDisplay();
        },

        // Obtener historial de búsquedas
        getSearchHistory: function() {
            try {
                const history = localStorage.getItem('grupos_search_history');
                return history ? JSON.parse(history) : [];
            } catch(e) {
                return [];
            }
        },

        // Cargar historial de búsquedas
        loadSearchHistory: function() {
            this.updateSearchHistoryDisplay();
        },

        // Actualizar display del historial
        updateSearchHistoryDisplay: function() {
            const historyContainer = this.elements.searchHistory;
            if (!historyContainer.length) return;

            const history = this.getSearchHistory();
            
            if (history.length === 0) {
                historyContainer.hide();
                return;
            }

            let html = '';
            html += ' Búsquedas recientes';
            
            history.forEach(item => {
                html += `
                    
                        
                            
                        
                        
                            ${item.query}
                            ${this.formatTimeAgo(item.timestamp)}
                        
                        
                            
                        
                    
                `;
            });
            
            html += '';
            historyContainer.html(html).show();
        },

        // Gestión de cache
        generateCacheKey: function(query, filters) {
            return `search_${query}_${JSON.stringify(filters)}`;
        },

        getCachedResult: function(key) {
            const cached = this.cache.get(key);
            if (!cached) return null;
            
            // Verificar expiración
            if (Date.now() - cached.timestamp > this.config.cacheExpiry) {
                this.cache.delete(key);
                return null;
            }
            
            return cached.data;
        },

        setCachedResult: function(key, data) {
            this.cache.set(key, {
                data: data,
                timestamp: Date.now()
            });
        },

        // Estados de UI
        showLoading: function() {
            this.state.isSearching = true;
            this.elements.loadingSpinner.show();
            this.elements.searchButton.addClass('loading').prop('disabled', true);
        },

        hideLoading: function() {
            this.state.isSearching = false;
            this.elements.loadingSpinner.hide();
            this.elements.searchButton.removeClass('loading').prop('disabled', false);
        },

        showError: function(message) {
            const errorHtml = `
                
                    
                        
                    
                    
                        Error en la búsqueda
                        ${message}
                    
                    
                        
                        Reintentar
                    
                
            `;
            this.elements.searchResults.html(errorHtml);
        },

        clearResults: function() {
            this.elements.searchResults.empty();
        },

        clearSearch: function() {
            this.elements.searchInput.val('');
            this.state.currentQuery = '';
            this.clearResults();
            this.hideSuggestions();
            this.updateUrl('', {});
        },

        // Animaciones
        animateResults: function() {
            const cards = $('.search-result-card');
            cards.each(function(index) {
                $(this).css({
                    opacity: 0,
                    transform: 'translateY(20px)'
                }).delay(index * 50).animate({
                    opacity: 1,
                    transform: 'translateY(0)'
                }, 300);
            });
        },

        // Utilidades
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

        formatNumber: function(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        },

        formatTimeAgo: function(timestamp) {
            const now = Date.now();
            const diff = now - timestamp;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (days > 0) return `hace ${days}d`;
            if (hours > 0) return `hace ${hours}h`;
            if (minutes > 0) return `hace ${minutes}m`;
            return 'ahora';
        },

        getCategoryIcon: function(category) {
            const icons = {
                'gaming': 'fas fa-gamepad',
                'musica': 'fas fa-music',
                'tecnologia': 'fas fa-laptop-code',
                'deportes': 'fas fa-futbol',
                'educacion': 'fas fa-graduation-cap',
                'entretenimiento': 'fas fa-film'
            };
            return icons[category.toLowerCase()] || 'fas fa-users';
        },

        // Manejo de URL
        updateUrl: function(query, filters) {
            if (!window.history || !window.history.pushState) return;

            const url = new URL(window.location);
            
            if (query) {
                url.searchParams.set('s', query);
            } else {
                url.searchParams.delete('s');
            }

            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    url.searchParams.set(key, filters[key]);
                } else {
                    url.searchParams.delete(key);
                }
            });

            window.history.pushState({}, '', url);
        },

        checkUrlParams: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get('s');
            
            if (query) {
                this.elements.searchInput.val(query);
                
                // Establecer filtros desde URL
                const category = urlParams.get('category');
                const sort = urlParams.get('sort');
                const status = urlParams.get('status');
                
                if (category) this.elements.filterCategory.val(category);
                if (sort) this.elements.filterSort.val(sort);
                if (status) this.elements.filterStatus.val(status);
                
                this.updateFilters();
                this.performSearch(query, false);
            }
        },

        // Scroll infinito
        handleInfiniteScroll: function() {
            if (this.state.isSearching) return;

            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();

            if (scrollTop + windowHeight >= documentHeight - 1000) {
                this.loadMoreResults();
            }
        },

        loadMoreResults: function() {
            this.state.resultsPage++;
            this.performSearch(this.state.currentQuery, false);
        },

        // Inicialización de filtros
        initializeFilters: function() {
            this.updateFilters();
        },

        // Generar mensaje de no resultados
        generateNoResults: function() {
            return `
                
                    
                        
                    
                    No se encontraron grupos
                    No hay grupos que coincidan con "${this.state.currentQuery}"
                    
                        Sugerencias:
                        
                            Verifica la ortografía de las palabras
                            Intenta con términos más generales
                            Usa palabras clave diferentes
                            Revisa los filtros aplicados
                        
                    
                    
                        
                            
                            Limpiar Filtros
                        
                        
                            
                            Añadir Mi Grupo
                        
                    
                
            `;
        },

        // Limpiar filtros
        clearFilters: function() {
            this.elements.filterCategory.val('');
            this.elements.filterSort.val('relevance');
            this.elements.filterStatus.val('');
            this.updateFilters();
            this.performSearch(this.state.currentQuery, true);
        },

        // Generar paginación
        generatePagination: function(data) {
            const totalPages = Math.ceil(data.total / this.config.maxResults);
            const currentPage = this.state.resultsPage;
            
            if (totalPages <= 1) return '';

            let html = '';
            
            // Botón anterior
            if (currentPage > 1) {
                html += `
                     Anterior
                `;
            }
            
            // Números de página
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                html += `
                    ${i}
                `;
            }
            
            // Botón siguiente
            if (currentPage < totalPages) {
                html += `
                    Siguiente 
                `;
            }
            
            html += '';
            
            // Vincular eventos de paginación
            $(document).on('click', '.pagination-btn', function() {
                const page = $(this).data('page');
                GruposTelegramSearch.state.resultsPage = page;
                GruposTelegramSearch.performSearch(GruposTelegramSearch.state.currentQuery, true);
            });
            
            return html;
        }
    };

    // Función para copiar al portapapeles
    window.copyToClipboard = function(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent;
        
        navigator.clipboard.writeText(text).then(function() {
            // Mostrar feedback visual
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = 'Copiado';
            button.classList.add('bg-green-500');
            
            setTimeout(function() {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-500');
            }, 2000);
        });
    };

    // Exponer globalmente para debugging
    window.GruposTelegramSearch = GruposTelegramSearch;

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        GruposTelegramSearch.init();
    });

})(jQuery);

/**
 * Funciones auxiliares para el backend (PHP)
 * Estas funciones deben implementarse en functions.php
 */

/*
// AJAX Handler para búsqueda
add_action('wp_ajax_grupos_search', 'handle_grupos_search');
add_action('wp_ajax_nopriv_grupos_search', 'handle_grupos_search');

function handle_grupos_search() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'grupos_telegram_nonce')) {
        wp_die('Error de seguridad');
    }

    $query = sanitize_text_field($_POST['query']);
    $filters = $_POST['filters'];
    $page = intval($_POST['page']);
    $per_page = intval($_POST['per_page']);

    // Realizar búsqueda
    $results = perform_grupos_search($query, $filters, $page, $per_page);

    wp_send_json_success($results);
}

function perform_grupos_search($query, $filters, $page, $per_page) {
    // Lógica de búsqueda aquí
    // Retornar array con resultados, sugerencias, etc.
}
*/