<?php
/**
 * Template part: search-form.php
 * Formulario de búsqueda reutilizable para Grupos de Telegram
 * 
 * @package GruposTelegramTheme
 * @version 1.0.0
 */

// Obtener parámetros pasados al template part
$search_context = isset($args['context']) ? $args['context'] : 'default';
$show_filters = isset($args['show_filters']) ? $args['show_filters'] : true;
$placeholder = isset($args['placeholder']) ? $args['placeholder'] : 'Buscar grupos de Telegram...';
$search_value = get_search_query();
$form_id = isset($args['form_id']) ? $args['form_id'] : 'search-form-' . uniqid();

// Obtener categorías para el filtro
$categorias = get_terms(array(
    'taxonomy' => 'categoria_grupo',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC'
));

// Configuración según el contexto
$context_config = array(
    'hero' => array(
        'size' => 'large',
        'show_suggestions' => true,
        'show_recent' => true
    ),
    'sidebar' => array(
        'size' => 'small',
        'show_suggestions' => false,
        'show_recent' => false
    ),
    'archive' => array(
        'size' => 'medium',
        'show_suggestions' => true,
        'show_recent' => false
    ),
    'default' => array(
        'size' => 'medium',
        'show_suggestions' => true,
        'show_recent' => true
    )
);

$config = isset($context_config[$search_context]) ? $context_config[$search_context] : $context_config['default'];
?>

<div class="search-form-component search-context-<?php echo esc_attr($search_context); ?>" 
     data-context="<?php echo esc_attr($search_context); ?>">
     
    <form class="search-form" 
          id="<?php echo esc_attr($form_id); ?>" 
          role="search" 
          method="get" 
          action="<?php echo esc_url(home_url('/')); ?>">
          
        <div class="search-form-wrapper">
            <!-- Búsqueda Principal -->
            <div class="search-input-container relative">
                <div class="search-input-group flex">
                    <div class="search-input-wrapper relative flex-1">
                        <input type="search" 
                               name="s" 
                               id="search-input-<?php echo esc_attr($form_id); ?>"
                               class="search-input w-full px-4 py-3 pl-12 pr-16 text-lg border-2 border-gray-300 rounded-l-full focus:border-blue-500 focus:outline-none transition-colors"
                               placeholder="<?php echo esc_attr($placeholder); ?>"
                               value="<?php echo esc_attr($search_value); ?>"
                               autocomplete="off"
                               data-min-length="2">
                               
                        <input type="hidden" name="post_type" value="grupo">
                        
                        <div class="search-icon absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        
                        <div class="search-loading absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                        </div>
                        
                        <button type="button" 
                                class="search-clear absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                                aria-label="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <button type="submit" 
                            class="search-button telegram-gradient text-white px-8 py-3 rounded-r-full hover:opacity-90 transition-opacity">
                        <?php if ($config['size'] === 'small'): ?>
                            <i class="fas fa-search"></i>
                        <?php else: ?>
                            <i class="fas fa-search mr-2"></i>
                            Buscar
                        <?php endif; ?>
                    </button>
                </div>
                
                <!-- Sugerencias de Autocompletado -->
                <?php if ($config['show_suggestions']): ?>
                <div class="search-suggestions hidden absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg mt-1 z-50 shadow-lg">
                    <div class="suggestions-content">
                        <!-- Las sugerencias se cargan dinámicamente via AJAX -->
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Filtros Avanzados -->
            <?php if ($show_filters && $search_context !== 'sidebar'): ?>
            <div class="search-filters mt-4">
                <div class="filters-row flex flex-wrap gap-4 mb-4">
                    <!-- Filtro por Categoría -->
                    <div class="filter-category">
                        <select name="categoria_filter" 
                                class="filter-select px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo esc_attr($categoria->slug); ?>" 
                                        <?php selected(get_query_var('categoria_filter'), $categoria->slug); ?>>
                                    <?php echo esc_html($categoria->name); ?> (<?php echo $categoria->count; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filtro por Estado -->
                    <div class="filter-status">
                        <select name="estado_filter" 
                                class="filter-select px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            <option value="">Todos los estados</option>
                            <option value="activo" <?php selected(get_query_var('estado_filter'), 'activo'); ?>>
                                ✅ Solo activos
                            </option>
                            <option value="inactivo" <?php selected(get_query_var('estado_filter'), 'inactivo'); ?>>
                                ❌ Inactivos
                            </option>
                        </select>
                    </div>

                    <!-- Filtro por Número de Miembros -->
                    <div class="filter-members">
                        <select name="miembros_filter" 
                                class="filter-select px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            <option value="">Cualquier tamaño</option>
                            <option value="small" <?php selected(get_query_var('miembros_filter'), 'small'); ?>>
                                👥 Menos de 1,000
                            </option>
                            <option value="medium" <?php selected(get_query_var('miembros_filter'), 'medium'); ?>>
                                👨‍👩‍👧‍👦 1,000 - 10,000
                            </option>
                            <option value="large" <?php selected(get_query_var('miembros_filter'), 'large'); ?>>
                                👥👥 Más de 10,000
                            </option>
                        </select>
                    </div>

                    <!-- Ordenamiento -->
                    <div class="filter-sort">
                        <select name="orderby" 
                                class="filter-select px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            <option value="relevance" <?php selected(get_query_var('orderby'), 'relevance'); ?>>
                                🎯 Más relevantes
                            </option>
                            <option value="members" <?php selected(get_query_var('orderby'), 'members'); ?>>
                                👥 Más miembros
                            </option>
                            <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>>
                                📅 Más recientes
                            </option>
                            <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>>
                                🔤 Alfabético
                            </option>
                        </select>
                    </div>

                    <!-- Botón Limpiar Filtros -->
                    <button type="button" 
                            class="clear-filters px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Limpiar
                    </button>
                </div>

                <!-- Tags de Filtros Activos -->
                <div class="active-filters-container">
                    <div class="active-filters flex flex-wrap gap-2">
                        <!-- Los filtros activos se generan dinámicamente -->
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Búsquedas Recientes -->
            <?php if ($config['show_recent'] && $search_context === 'hero'): ?>
            <div class="recent-searches mt-4">
                <div class="text-sm text-gray-500 mb-2">Búsquedas populares:</div>
                <div class="recent-terms flex flex-wrap gap-2">
                    <?php
                    $popular_searches = array(
                        'gaming' => 'grupos gaming',
                        'musica' => 'música electrónica',
                        'tecnologia' => 'programación',
                        'deportes' => 'fútbol España',
                        'educacion' => 'idiomas',
                        'entretenimiento' => 'series netflix'
                    );
                    
                    foreach ($popular_searches as $key => $term):
                    ?>
                        <button type="button" 
                                class="recent-term px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors"
                                data-search="<?php echo esc_attr($term); ?>">
                            <?php echo esc_html($term); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- JavaScript del Componente -->
<script>
jQuery(document).ready(function($) {
    // Inicializar funcionalidad de búsqueda
    const searchForm = $('#<?php echo esc_js($form_id); ?>');
    const searchInput = searchForm.find('.search-input');
    const suggestionsContainer = searchForm.find('.search-suggestions');
    const loadingIndicator = searchForm.find('.search-loading');
    const clearButton = searchForm.find('.search-clear');
    
    // Configuración
    const config = {
        minLength: parseInt(searchInput.data('min-length')) || 2,
        debounceDelay: 300,
        maxSuggestions: 8
    };
    
    let searchTimeout;
    let currentRequest;
    
    // Autocompletado y sugerencias
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= config.minLength) {
            showLoading(true);
            showClearButton(true);
            
            searchTimeout = setTimeout(function() {
                fetchSuggestions(query);
            }, config.debounceDelay);
        } else {
            hideSuggestions();
            showLoading(false);
            showClearButton(false);
        }
    });
    
    // Fetch suggestions via AJAX
    function fetchSuggestions(query) {
        // Cancelar petición anterior si existe
        if (currentRequest && currentRequest.readyState !== 4) {
            currentRequest.abort();
        }
        
        currentRequest = $.ajax({
            url: gruposTelegramAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_search_suggestions',
                query: query,
                context: '<?php echo esc_js($search_context); ?>',
                nonce: gruposTelegramAjax.nonce
            },
            success: function(response) {
                showLoading(false);
                if (response.success && response.data.suggestions.length > 0) {
                    displaySuggestions(response.data.suggestions);
                } else {
                    hideSuggestions();
                }
            },
            error: function() {
                showLoading(false);
                hideSuggestions();
            }
        });
    }
    
    // Mostrar sugerencias
    function displaySuggestions(suggestions) {
        let html = '<div class="suggestions-content p-2">';
        html += '<div class="text-xs text-gray-500 uppercase tracking-wide px-3 py-2">Sugerencias</div>';
        
        suggestions.forEach(function(suggestion) {
            html += '<div class="suggestion-item px-3 py-2 cursor-pointer rounded transition-colors hover:bg-gray-50" data-value="' + suggestion.value + '">';
            html += '<i class="fas fa-' + (suggestion.type === 'group' ? 'users' : 'search') + ' text-gray-400 mr-2"></i>';
            html += suggestion.label;
            if (suggestion.count) {
                html += '<span class="text-gray-500 text-sm ml-2">(' + suggestion.count + ')</span>';
            }
            html += '</div>';
        });
        
        html += '</div>';
        
        suggestionsContainer.html(html).removeClass('hidden').addClass('fade-in');
    }
    
    // Ocultar sugerencias
    function hideSuggestions() {
        suggestionsContainer.addClass('hidden').removeClass('fade-in');
    }
    
    // Mostrar/ocultar loading
    function showLoading(show) {
        if (show) {
            loadingIndicator.removeClass('hidden');
        } else {
            loadingIndicator.addClass('hidden');
        }
    }
    
    // Mostrar/ocultar botón limpiar
    function showClearButton(show) {
        if (show && searchInput.val().length > 0) {
            clearButton.removeClass('hidden');
        } else {
            clearButton.addClass('hidden');
        }
    }
    
    // Click en sugerencia
    $(document).on('click', '.suggestion-item', function() {
        const value = $(this).data('value');
        searchInput.val(value);
        hideSuggestions();
        searchForm.submit();
    });
    
    // Botón limpiar
    clearButton.on('click', function() {
        searchInput.val('').focus();
        hideSuggestions();
        showClearButton(false);
    });
    
    // Términos populares
    $('.recent-term').on('click', function() {
        const searchTerm = $(this).data('search');
        searchInput.val(searchTerm);
        searchForm.submit();
    });
    
    // Limpiar filtros
    $('.clear-filters').on('click', function() {
        searchForm.find('.filter-select').val('');
        $('.active-filters').empty();
        updateActiveFilters();
    });
    
    // Actualizar filtros activos
    function updateActiveFilters() {
        const activeFiltersContainer = $('.active-filters');
        activeFiltersContainer.empty();
        
        searchForm.find('.filter-select').each(function() {
            const select = $(this);
            const value = select.val();
            const text = select.find('option:selected').text();
            
            if (value && value !== '') {
                const filterTag = $('<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">' + 
                    text + 
                    '<button class="ml-1 text-blue-600 hover:text-blue-800 filter-remove" data-filter="' + select.attr('name') + '">' +
                    '<i class="fas fa-times text-xs"></i>' +
                    '</button></span>');
                    
                activeFiltersContainer.append(filterTag);
            }
        });
    }
    
    // Remover filtro individual
    $(document).on('click', '.filter-remove', function() {
        const filterName = $(this).data('filter');
        $('[name="' + filterName + '"]').val('');
        updateActiveFilters();
    });
    
    // Actualizar filtros al cambiar
    $('.filter-select').on('change', function() {
        updateActiveFilters();
        
        // Auto-submit si está configurado
        if (searchForm.hasClass('auto-submit')) {
            searchForm.submit();
        }
    });
    
    // Ocultar sugerencias al hacer click fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-input-container').length) {
            hideSuggestions();
        }
    });
    
    // Navegación con teclado en sugerencias
    searchInput.on('keydown', function(e) {
        const suggestions = suggestionsContainer.find('.suggestion-item');
        const activeSuggestion = suggestions.filter('.active');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (activeSuggestion.length === 0) {
                suggestions.first().addClass('active bg-gray-100');
            } else {
                activeSuggestion.removeClass('active bg-gray-100');
                const next = activeSuggestion.next('.suggestion-item');
                if (next.length) {
                    next.addClass('active bg-gray-100');
                } else {
                    suggestions.first().addClass('active bg-gray-100');
                }
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeSuggestion.length === 0) {
                suggestions.last().addClass('active bg-gray-100');
            } else {
                activeSuggestion.removeClass('active bg-gray-100');
                const prev = activeSuggestion.prev('.suggestion-item');
                if (prev.length) {
                    prev.addClass('active bg-gray-100');
                } else {
                    suggestions.last().addClass('active bg-gray-100');
                }
            }
        } else if (e.key === 'Enter' && activeSuggestion.length) {
            e.preventDefault();
            activeSuggestion.click();
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });
    
    // Inicializar filtros activos
    updateActiveFilters();
});
</script>

<?php
// Agregar estilos específicos del contexto
$custom_styles = '';
switch ($search_context) {
    case 'hero':
        $custom_styles = '
            .search-context-hero .search-input { font-size: 1.25rem; padding: 1rem 3rem 1rem 3rem; }
            .search-context-hero .search-button { padding: 1rem 2rem; font-size: 1.1rem; }
        ';
        break;
    case 'sidebar':
        $custom_styles = '
            .search-context-sidebar .search-input { font-size: 0.9rem; padding: 0.75rem 2.5rem 0.75rem 2.5rem; }
            .search-context-sidebar .search-button { padding: 0.75rem 1rem; }
        ';
        break;
}

if ($custom_styles):
?>
<style>
<?php echo $custom_styles; ?>
</style>
<?php endif; ?>