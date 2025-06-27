<?php
/**
 * The template for displaying search results pages
 *
 * @package Telegram_Groups
 */

get_header();

// Obtener término de búsqueda
$search_query = get_search_query();
$current_categoria = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
$current_ciudad = isset($_GET['ciudad']) ? sanitize_text_field($_GET['ciudad']) : '';
$current_tipo = isset($_GET['tipo']) ? sanitize_text_field($_GET['tipo']) : '';

// Configurar resultados por página
$resultados_per_page = get_theme_mod('groups_per_page', 16);
?>

<main id="primary" class="site-main search-page">
    <div class="container">
        
        <!-- Header de búsqueda -->
        <header class="search-header">
            <div class="search-header-content">
                <h1 class="search-title">
                    <i class="fas fa-search"></i>
                    <?php if ($search_query) : ?>
                        <?php printf(__('Resultados para: "%s"', 'telegram-groups'), '<span class="search-term">' . esc_html($search_query) . '</span>'); ?>
                    <?php else : ?>
                        <?php _e('Búsqueda Avanzada', 'telegram-groups'); ?>
                    <?php endif; ?>
                </h1>
                
                <?php if ($search_query) : ?>
                    <p class="search-description">
                        <?php
                        global $wp_query;
                        $total_found = $wp_query->found_posts;
                        
                        if ($total_found > 0) :
                            printf(
                                _n(
                                    'Se encontró %d resultado',
                                    'Se encontraron %d resultados',
                                    $total_found,
                                    'telegram-groups'
                                ),
                                $total_found
                            );
                        else :
                            _e('No se encontraron resultados para tu búsqueda.', 'telegram-groups');
                        endif;
                        ?>
                    </p>
                <?php endif; ?>
                
                <!-- Buscador principal -->
                <div class="search-form-container">
                    <form role="search" method="get" class="search-form-advanced" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="search-input-group">
                            <div class="search-input-wrapper">
                                <i class="fas fa-search search-icon"></i>
                                <input type="search" 
                                       name="s" 
                                       placeholder="<?php _e('Buscar grupos, categorías, contenido...', 'telegram-groups'); ?>"
                                       value="<?php echo esc_attr($search_query); ?>"
                                       class="search-input-main"
                                       required>
                            </div>
                            <button type="submit" class="search-button-main">
                                <i class="fas fa-search"></i>
                                <span><?php _e('Buscar', 'telegram-groups'); ?></span>
                            </button>
                        </div>
                        
                        <!-- Filtros rápidos -->
                        <div class="search-quick-filters">
                            <select name="categoria" class="quick-filter-select">
                                <option value=""><?php _e('Todas las categorías', 'telegram-groups'); ?></option>
                                <?php
                                $categorias = get_terms(array(
                                    'taxonomy' => 'categoria_grupo',
                                    'hide_empty' => true,
                                    'orderby' => 'name'
                                ));
                                
                                if ($categorias && !is_wp_error($categorias)) :
                                    foreach ($categorias as $categoria) :
                                ?>
                                    <option value="<?php echo esc_attr($categoria->slug); ?>" 
                                            <?php selected($current_categoria, $categoria->slug); ?>>
                                        <?php echo esc_html($categoria->name); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                            
                            <select name="ciudad" class="quick-filter-select">
                                <option value=""><?php _e('Todas las ciudades', 'telegram-groups'); ?></option>
                                <?php
                                $ciudades = get_terms(array(
                                    'taxonomy' => 'ciudad_grupo',
                                    'hide_empty' => true,
                                    'orderby' => 'name'
                                ));
                                
                                if ($ciudades && !is_wp_error($ciudades)) :
                                    foreach ($ciudades as $ciudad) :
                                ?>
                                    <option value="<?php echo esc_attr($ciudad->slug); ?>" 
                                            <?php selected($current_ciudad, $ciudad->slug); ?>>
                                        <?php echo esc_html($ciudad->name); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                            
                            <select name="tipo" class="quick-filter-select">
                                <option value=""><?php _e('Todo el contenido', 'telegram-groups'); ?></option>
                                <option value="grupo" <?php selected($current_tipo, 'grupo'); ?>><?php _e('Solo grupos', 'telegram-groups'); ?></option>
                                <option value="destacados" <?php selected($current_tipo, 'destacados'); ?>><?php _e('Solo destacados', 'telegram-groups'); ?></option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </header>

        <div class="search-content-wrapper">
            
            <!-- Sidebar con filtros -->
            <aside class="search-sidebar">
                <div class="search-filters-sticky">
                    
                    <!-- Búsquedas sugeridas -->
                    <?php if (!$search_query) : ?>
                        <div class="suggested-searches">
                            <h3>
                                <i class="fas fa-lightbulb"></i>
                                <?php _e('Búsquedas Populares', 'telegram-groups'); ?>
                            </h3>
                            <div class="suggested-terms">
                                <?php
                                $suggested_terms = array(
                                    'gaming' => __('Gaming', 'telegram-groups'),
                                    'música' => __('Música', 'telegram-groups'),
                                    'tecnología' => __('Tecnología', 'telegram-groups'),
                                    'deportes' => __('Deportes', 'telegram-groups'),
                                    'Madrid' => __('Madrid', 'telegram-groups'),
                                    'Barcelona' => __('Barcelona', 'telegram-groups'),
                                    'programación' => __('Programación', 'telegram-groups'),
                                    'trading' => __('Trading', 'telegram-groups')
                                );
                                
                                foreach ($suggested_terms as $term => $label) :
                                ?>
                                    <a href="<?php echo esc_url(home_url('/?s=' . urlencode($term))); ?>" class="suggested-term">
                                        <?php echo esc_html($label); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Categorías relacionadas -->
                    <?php if ($search_query) : ?>
                        <div class="related-categories">
                            <h3>
                                <i class="fas fa-tags"></i>
                                <?php _e('Categorías Relacionadas', 'telegram-groups'); ?>
                            </h3>
                            <div class="related-categories-list">
                                <?php
                                // Buscar categorías que contengan el término de búsqueda
                                $related_cats = get_terms(array(
                                    'taxonomy' => 'categoria_grupo',
                                    'hide_empty' => true,
                                    'name__like' => $search_query,
                                    'number' => 6
                                ));
                                
                                // Si no hay coincidencias exactas, mostrar categorías populares
                                if (empty($related_cats)) {
                                    $related_cats = get_terms(array(
                                        'taxonomy' => 'categoria_grupo',
                                        'hide_empty' => true,
                                        'orderby' => 'count',
                                        'order' => 'DESC',
                                        'number' => 6
                                    ));
                                }
                                
                                if ($related_cats && !is_wp_error($related_cats)) :
                                    foreach ($related_cats as $categoria) :
                                        $categoria_style = get_categoria_style($categoria->slug);
                                ?>
                                    <a href="<?php echo get_term_link($categoria); ?>" class="related-category-link">
                                        <i class="<?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                                        <span><?php echo $categoria->name; ?></span>
                                        <small>(<?php echo $categoria->count; ?>)</small>
                                    </a>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Grupos destacados -->
                    <div class="search-featured-groups">
                        <h3>
                            <i class="fas fa-star"></i>
                            <?php _e('Grupos Destacados', 'telegram-groups'); ?>
                        </h3>
                        <div class="search-featured-list">
                            <?php
                            $featured_groups = new WP_Query(array(
                                'post_type' => 'grupo',
                                'posts_per_page' => 5,
                                'orderby' => 'rand',
                                'meta_query' => array(
                                    array(
                                        'key' => 'estado_grupo',
                                        'value' => 'Activo',
                                        'compare' => '='
                                    )
                                ),
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'destacados',
                                        'field' => 'slug',
                                        'terms' => 'destacado'
                                    )
                                )
                            ));
                            
                            if ($featured_groups->have_posts()) :
                                while ($featured_groups->have_posts()) : $featured_groups->the_post();
                                    $numero_miembros = get_field('numero_miembros') ?: 0;
                                    $categoria = wp_get_post_terms(get_the_ID(), 'categoria_grupo', array('fields' => 'all'));
                                    $categoria_principal = !empty($categoria) ? $categoria[0] : null;
                                    $categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
                            ?>
                                <div class="search-featured-item">
                                    <div class="featured-icon" style="background: <?php echo $categoria_style['color']; ?>">
                                        <i class="<?php echo $categoria_style['icon']; ?>"></i>
                                    </div>
                                    <div class="featured-info">
                                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <span class="featured-members">
                                            <i class="fas fa-users"></i>
                                            <?php echo format_member_count($numero_miembros); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    </div>
                    
                    <!-- Estadísticas de búsqueda -->
                    <div class="search-stats">
                        <h3>
                            <i class="fas fa-chart-bar"></i>
                            <?php _e('Estadísticas', 'telegram-groups'); ?>
                        </h3>
                        <?php
                        $total_grupos = wp_count_posts('grupo')->publish;
                        $total_categorias = wp_count_terms('categoria_grupo');
                        $total_ciudades = wp_count_terms('ciudad_grupo');
                        ?>
                        <div class="search-stats-list">
                            <div class="search-stat-item">
                                <span class="stat-number"><?php echo number_format($total_grupos); ?></span>
                                <span class="stat-label"><?php _e('Grupos', 'telegram-groups'); ?></span>
                            </div>
                            <div class="search-stat-item">
                                <span class="stat-number"><?php echo $total_categorias; ?></span>
                                <span class="stat-label"><?php _e('Categorías', 'telegram-groups'); ?></span>
                            </div>
                            <div class="search-stat-item">
                                <span class="stat-number"><?php echo $total_ciudades; ?></span>
                                <span class="stat-label"><?php _e('Ciudades', 'telegram-groups'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </aside>
            
            <!-- Contenido principal de resultados -->
            <div class="search-main-content">
                
                <!-- Filtros activos -->
                <?php if ($search_query || $current_categoria || $current_ciudad || $current_tipo) : ?>
                    <div class="search-active-filters">
                        <h4><?php _e('Filtros aplicados:', 'telegram-groups'); ?></h4>
                        <div class="active-filters-list">
                            
                            <?php if ($search_query) : ?>
                                <span class="active-filter">
                                    <i class="fas fa-search"></i>
                                    "<?php echo esc_html($search_query); ?>"
                                    <a href="<?php echo remove_query_arg('s'); ?>" class="remove-filter">&times;</a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($current_categoria) : ?>
                                <?php $cat_term = get_term_by('slug', $current_categoria, 'categoria_grupo'); ?>
                                <span class="active-filter">
                                    <i class="fas fa-tag"></i>
                                    <?php echo $cat_term ? $cat_term->name : $current_categoria; ?>
                                    <a href="<?php echo remove_query_arg('categoria'); ?>" class="remove-filter">&times;</a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($current_ciudad) : ?>
                                <?php $city_term = get_term_by('slug', $current_ciudad, 'ciudad_grupo'); ?>
                                <span class="active-filter">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $city_term ? $city_term->name : $current_ciudad; ?>
                                    <a href="<?php echo remove_query_arg('ciudad'); ?>" class="remove-filter">&times;</a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($current_tipo) : ?>
                                <span class="active-filter">
                                    <i class="fas fa-filter"></i>
                                    <?php 
                                    switch($current_tipo) {
                                        case 'grupo': _e('Solo grupos', 'telegram-groups'); break;
                                        case 'destacados': _e('Solo destacados', 'telegram-groups'); break;
                                        default: echo esc_html($current_tipo);
                                    }
                                    ?>
                                    <a href="<?php echo remove_query_arg('tipo'); ?>" class="remove-filter">&times;</a>
                                </span>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Resultados de búsqueda -->
                <div class="search-results">
                    
                    <?php if (have_posts()) : ?>
                        
                        <!-- Toolbar de resultados -->
                        <div class="search-toolbar">
                            <div class="results-summary">
                                <?php
                                global $wp_query;
                                $total_found = $wp_query->found_posts;
                                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                                $posts_per_page = get_query_var('posts_per_page');
                                $start = ($paged - 1) * $posts_per_page + 1;
                                $end = min($paged * $posts_per_page, $total_found);
                                
                                printf(
                                    __('Mostrando %d-%d de %d resultados', 'telegram-groups'),
                                    $start,
                                    $end,
                                    $total_found
                                );
                                ?>
                            </div>
                        </div>
                        
                        <!-- Contenedor de resultados -->
                        <div class="search-results-container">
                            <div class="search-results-grid">
                                <?php while (have_posts()) : the_post(); ?>
                                    <?php 
                                    if (get_post_type() === 'grupo') {
                                        get_template_part('template-parts/grupo-card', 'search');
                                    } else {
                                        get_template_part('template-parts/content', 'search');
                                    }
                                    ?>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <!-- Paginación -->
                        <div class="search-pagination">
                            <?php
                            the_posts_pagination(array(
                                'mid_size' => 2,
                                'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Anterior', 'telegram-groups'),
                                'next_text' => __('Siguiente', 'telegram-groups') . ' <i class="fas fa-chevron-right"></i>',
                                'before_page_number' => '<span class="screen-reader-text">' . __('Página', 'telegram-groups') . ' </span>',
                            ));
                            ?>
                        </div>
                        
                    <?php else : ?>
                        
                        <!-- Sin resultados -->
                        <div class="no-search-results">
                            <div class="no-results-icon">
                                <i class="fas fa-search-minus"></i>
                            </div>
                            <h3><?php _e('No se encontraron resultados', 'telegram-groups'); ?></h3>
                            <p><?php _e('Tu búsqueda no produjo ningún resultado. Prueba con términos diferentes o revisa las sugerencias.', 'telegram-groups'); ?></p>
                            
                            <div class="search-suggestions">
                                <h4><?php _e('Sugerencias para mejorar tu búsqueda:', 'telegram-groups'); ?></h4>
                                <ul>
                                    <li><?php _e('Verifica la ortografía de las palabras', 'telegram-groups'); ?></li>
                                    <li><?php _e('Usa términos más generales', 'telegram-groups'); ?></li>
                                    <li><?php _e('Prueba con sinónimos', 'telegram-groups'); ?></li>
                                    <li><?php _e('Reduce el número de palabras', 'telegram-groups'); ?></li>
                                </ul>
                                
                                <div class="alternative-searches">
                                    <h5><?php _e('¿Quizás buscabas?', 'telegram-groups'); ?></h5>
                                    <div class="alternative-terms">
                                        <a href="<?php echo esc_url(home_url('/?s=gaming')); ?>" class="alternative-term">Gaming</a>
                                        <a href="<?php echo esc_url(home_url('/?s=música')); ?>" class="alternative-term">Música</a>
                                        <a href="<?php echo esc_url(home_url('/?s=tecnología')); ?>" class="alternative-term">Tecnología</a>
                                        <a href="<?php echo esc_url(home_url('/?s=deportes')); ?>" class="alternative-term">Deportes</a>
                                    </div>
                                </div>
                                
                                <div class="no-results-actions">
                                    <a href="<?php echo get_post_type_archive_link('grupo'); ?>" class="btn btn-primary">
                                        <i class="fas fa-list"></i>
                                        <?php _e('Explorar todos los grupos', 'telegram-groups'); ?>
                                    </a>
                                    <a href="<?php echo home_url('/'); ?>" class="btn btn-secondary">
                                        <i class="fas fa-home"></i>
                                        <?php _e('Volver al inicio', 'telegram-groups'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                    <?php endif; ?>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
</main>

<style>
/* Search Page Styles */
.search-page {
    padding: 2rem 0;
}

/* Header de búsqueda */
.search-header {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    padding: 3rem 0;
    margin-bottom: 2rem;
    text-align: center;
}

.search-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.search-term {
    color: #ffd700;
    font-style: italic;
}

.search-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0 0 2rem 0;
}

/* Formulario de búsqueda avanzado */
.search-form-container {
    max-width: 800px;
    margin: 0 auto;
}

.search-input-group {
    display: flex;
    background: var(--telegram-white);
    border-radius: 50px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
}

.search-input-wrapper {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 1.5rem;
    color: var(--telegram-gray);
    z-index: 2;
}

.search-input-main {
    width: 100%;
    padding: 1.25rem 1.5rem 1.25rem 3rem;
    border: none;
    outline: none;
    font-size: 1.1rem;
    color: var(--telegram-dark);
}

.search-button-main {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: none;
    padding: 1.25rem 2rem;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.3s ease;
}

.search-button-main:hover {
    background: var(--telegram-light-blue);
}

/* Filtros rápidos */
.search-quick-filters {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.quick-filter-select {
    background: rgba(255, 255, 255, 0.2);
    color: var(--telegram-white);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 25px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quick-filter-select:focus {
    outline: none;
    background: var(--telegram-white);
    color: var(--telegram-dark);
    border-color: var(--telegram-white);
}

.quick-filter-select option {
    color: var(--telegram-dark);
    background: var(--telegram-white);
}

/* Layout principal */
.search-content-wrapper {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    align-items: start;
}

/* Sidebar */
.search-sidebar {
    position: sticky;
    top: 100px;
}

.search-filters-sticky {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.suggested-searches,
.related-categories,
.search-featured-groups,
.search-stats {
    background: var(--telegram-white);
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
}

.search-filters-sticky h3 {
    margin: 0 0 1.5rem 0;
    color: var(--telegram-dark);
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--telegram-blue);
}

.search-filters-sticky h3 i {
    color: var(--telegram-blue);
}

/* Búsquedas sugeridas */
.suggested-terms {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.suggested-term {
    background: #f8f9fa;
    color: var(--telegram-dark);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.suggested-term:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border-color: var(--telegram-blue);
}

/* Categorías relacionadas */
.related-categories-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.related-category-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    color: var(--telegram-dark);
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.related-category-link:hover {
    background: #f8f9fa;
    border-color: var(--telegram-blue);
    transform: translateX(3px);
}

.related-category-link span {
    flex: 1;
    font-weight: 500;
    font-size: 0.9rem;
}

.related-category-link small {
    color: var(--telegram-gray);
    font-size: 0.8rem;
}

/* Grupos destacados en sidebar */
.search-featured-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.search-featured-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.search-featured-item:hover {
    background: #f8f9fa;
}

.featured-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 1rem;
    flex-shrink: 0;
}

.featured-info {
    flex: 1;
    min-width: 0;
}

.featured-info h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.featured-info h4 a {
    color: var(--telegram-dark);
    text-decoration: none;
}

.featured-info h4 a:hover {
    color: var(--telegram-blue);
}

.featured-members {
    font-size: 0.8rem;
    color: var(--telegram-gray);
    display: flex;
    align-items: center;
    gap: 3px;
}

/* Estadísticas */
.search-stats-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.search-stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.search-stat-item .stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--telegram-blue);
    line-height: 1;
}

.search-stat-item .stat-label {
    display: block;
    font-size: 0.8rem;
    color: var(--telegram-gray);
    margin-top: 0.25rem;
}

/* Filtros activos */
.search-active-filters {
    background: var(--telegram-white);
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 136, 204, 0.05);
}

.search-active-filters h4 {
    margin: 0 0 1rem 0;
    color: var(--telegram-dark);
    font-size: 1rem;
}

.active-filters-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}



active-filter {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}







.active-filter i {
    opacity: 0.8;
}

.remove-filter {
    color: var(--telegram-white);
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1rem;
    line-height: 1;
    margin-left: 0.5rem;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.remove-filter:hover {
    opacity: 1;
    color: var(--telegram-white);
}

/* Toolbar de resultados */
.search-toolbar {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem 1.5rem;
    background: var(--telegram-white);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 136, 204, 0.05);
}

.results-summary {
    font-weight: 600;
    color: var(--telegram-dark);
}

/* Contenedor de resultados */
.search-results-container {
    position: relative;
}

.search-results-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

/* Sin resultados */
.no-search-results {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--telegram-white);
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
}

.no-results-icon {
    font-size: 5rem;
    color: var(--telegram-gray);
    opacity: 0.3;
    margin-bottom: 2rem;
}

.no-search-results h3 {
    color: var(--telegram-dark);
    font-size: 2rem;
    margin: 0 0 1rem 0;
}

.no-search-results p {
    color: var(--telegram-gray);
    font-size: 1.1rem;
    margin: 0 0 3rem 0;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.search-suggestions {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    text-align: left;
    max-width: 600px;
    margin: 0 auto;
}

.search-suggestions h4 {
    color: var(--telegram-blue);
    margin: 0 0 1rem 0;
    font-size: 1.2rem;
}

.search-suggestions h5 {
    color: var(--telegram-dark);
    margin: 2rem 0 1rem 0;
    font-size: 1rem;
}

.search-suggestions ul {
    margin: 0 0 2rem 0;
    padding-left: 1.5rem;
    color: var(--telegram-gray);
}

.search-suggestions li {
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

.alternative-terms {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.alternative-term {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    border: 2px solid var(--telegram-blue);
    transition: all 0.3s ease;
}

.alternative-term:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
}

.no-results-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.no-results-actions .btn {
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--telegram-blue);
    color: var(--telegram-white);
}

.btn-primary:hover {
    background: var(--telegram-light-blue);
    transform: translateY(-2px);
    color: var(--telegram-white);
}

.btn-secondary {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    border: 2px solid var(--telegram-blue);
}

.btn-secondary:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
}

/* Paginación */
.search-pagination {
    margin-top: 3rem;
    text-align: center;
}

.search-pagination .nav-links {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.search-pagination a,
.search-pagination span {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-pagination a {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    border: 2px solid #e9ecef;
}

.search-pagination a:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border-color: var(--telegram-blue);
}

.search-pagination .current {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: 2px solid var(--telegram-blue);
}

/* Responsive */
@media (max-width: 1200px) {
    .search-content-wrapper {
        grid-template-columns: 280px 1fr;
    }
    
    .search-results-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 1024px) {
    .search-content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .search-sidebar {
        position: static;
        order: 2;
    }
    
    .search-main-content {
        order: 1;
    }
    
    .search-results-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .search-header {
        padding: 2rem 0;
    }
    
    .search-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .search-input-group {
        flex-direction: column;
        border-radius: 15px;
    }
    
    .search-button-main {
        border-radius: 0 0 15px 15px;
        justify-content: center;
    }
    
    .search-quick-filters {
        flex-direction: column;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .search-toolbar {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .search-results-grid {
        grid-template-columns: 1fr;
    }
    
    .search-stats-list {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .no-results-actions {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 480px) {
    .search-page {
        padding: 1rem 0;
    }
    
    .search-title {
        font-size: 1.5rem;
    }
    
    .search-filters-sticky {
        gap: 1rem;
    }
    
    .search-filters-sticky > div {
        padding: 1rem;
    }
    
    .no-search-results {
        padding: 2rem 1rem;
    }
    
    .search-suggestions {
        padding: 1.5rem;
    }
    
    .alternative-terms {
        justify-content: center;
    }
}

/* Estilos adicionales para highlighting */
.search-highlight {
    background: #ffd700;
    color: var(--telegram-dark);
    padding: 0 2px;
    border-radius: 2px;
    font-weight: 600;
}

.mobile-search-filters-toggle {
    width: 100%;
    padding: 1rem;
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: none;
    border-radius: 10px;
    font-weight: 600;
    margin-bottom: 1rem;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: background 0.3s ease;
}

.mobile-search-filters-toggle:hover {
    background: var(--telegram-light-blue);
}

@media (max-width: 1024px) {
    .mobile-search-filters-toggle {
        display: flex;
    }
}

@media (min-width: 1025px) {
    .mobile-search-filters-toggle {
        display: none !important;
    }
}
</style>

<script>
// JavaScript para funcionalidades de búsqueda
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-submit para filtros rápidos
    const quickFilterSelects = document.querySelectorAll('.quick-filter-select');
    quickFilterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Submit automático del formulario al cambiar filtros
            this.closest('form').submit();
        });
    });
    
    // Mejorar UX del buscador principal
    const searchInputMain = document.querySelector('.search-input-main');
    if (searchInputMain) {
        // Focus automático si no hay término de búsqueda
        if (!searchInputMain.value) {
            setTimeout(() => {
                searchInputMain.focus();
            }, 500);
        }
        
        // Limpiar búsqueda con Escape
        searchInputMain.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.focus();
            }
        });
    }
    
    // Búsqueda con enter en filtros
    quickFilterSelects.forEach(select => {
        select.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    });
    
    // Highlighting de términos de búsqueda en resultados
    const searchTerm = document.querySelector('.search-term');
    if (searchTerm) {
        const termText = searchTerm.textContent.toLowerCase();
        const resultCards = document.querySelectorAll('.grupo-card-search, .content-search');
        
        resultCards.forEach(card => {
            const titleElement = card.querySelector('h3 a, h2 a');
            const descElement = card.querySelector('.grupo-descripcion-search p, .content-excerpt');
            
            if (titleElement) {
                highlightText(titleElement, termText);
            }
            if (descElement) {
                highlightText(descElement, termText);
            }
        });
    }
    
    // Función para resaltar texto
    function highlightText(element, searchTerm) {
        if (!searchTerm || searchTerm.length < 2) return;
        
        const text = element.innerHTML;
        const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
        const highlightedText = text.replace(regex, '<mark class="search-highlight">$1</mark>');
        element.innerHTML = highlightedText;
    }
    
    // Escapar caracteres especiales para regex
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    // Scroll suave a resultados después de búsqueda
    if (window.location.search.includes('s=')) {
        setTimeout(() => {
            const resultsSection = document.querySelector('.search-results');
            if (resultsSection) {
                resultsSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 300);
    }
    
    // Mostrar/ocultar filtros en móvil
    if (window.innerWidth <= 1024) {
        const sidebar = document.querySelector('.search-sidebar');
        if (sidebar) {
            // Crear botón toggle para móvil
            const toggleButton = document.createElement('button');
            toggleButton.innerHTML = '<i class="fas fa-filter"></i> <?php _e("Mostrar Filtros", "telegram-groups"); ?>';
            toggleButton.className = 'mobile-search-filters-toggle';
            
            // Insertar antes del contenido principal
            const mainContent = document.querySelector('.search-main-content');
            if (mainContent) {
                mainContent.parentNode.insertBefore(toggleButton, mainContent);
                
                // Ocultar sidebar inicialmente en móvil
                sidebar.style.display = 'none';
                
                // Toggle functionality
                toggleButton.addEventListener('click', function() {
                    if (sidebar.style.display === 'none') {
                        sidebar.style.display = 'block';
                        this.innerHTML = '<i class="fas fa-times"></i> <?php _e("Ocultar Filtros", "telegram-groups"); ?>';
                        sidebar.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        sidebar.style.display = 'none';
                        this.innerHTML = '<i class="fas fa-filter"></i> <?php _e("Mostrar Filtros", "telegram-groups"); ?>';
                    }
                });
            }
        }
    }
    
    // Analytics para búsquedas (opcional)
    if (typeof gtag !== 'undefined') {
        const searchQuery = new URLSearchParams(window.location.search).get('s');
        if (searchQuery) {
            gtag('event', 'search', {
                'search_term': searchQuery,
                'event_category': 'engagement'
            });
        }
    }
});
</script>

<?php
// Modificar query de búsqueda para incluir filtros
function telegram_groups_modify_search_query($query) {
    if (!is_admin() && $query->is_main_query() && is_search()) {
        
        // Configurar post types para búsqueda
        $post_types = array('grupo', 'post', 'page');
        
        // Si se especifica solo grupos
        if (isset($_GET['tipo']) && $_GET['tipo'] === 'grupo') {
            $post_types = array('grupo');
        }
        
        $query->set('post_type', $post_types);
        
        // Meta query para grupos activos
        if (in_array('grupo', $post_types)) {
            $meta_query = array(
                'relation' => 'OR',
                array(
                    'key' => 'estado_grupo',
                    'value' => 'Activo',
                    'compare' => '='
                ),
                array(
                    'key' => 'estado_grupo',
                    'compare' => 'NOT EXISTS'
                )
            );
            $query->set('meta_query', $meta_query);
        }
        
        // Tax query para filtros
        $tax_query = array();
        
        if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
            $categoria_filter = sanitize_text_field($_GET['categoria']);
            $tax_query[] = array(
                'taxonomy' => 'categoria_grupo',
                'field' => 'slug',
                'terms' => $categoria_filter
            );
        }
        
        if (isset($_GET['ciudad']) && !empty($_GET['ciudad'])) {
            $ciudad_filter = sanitize_text_field($_GET['ciudad']);
            $tax_query[] = array(
                'taxonomy' => 'ciudad_grupo',
                'field' => 'slug',
                'terms' => $ciudad_filter
            );
        }
        
        if (isset($_GET['tipo']) && $_GET['tipo'] === 'destacados') {
            $tax_query[] = array(
                'taxonomy' => 'destacados',
                'field' => 'slug',
                'terms' => 'destacado'
            );
        }
        
        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'AND';
            }
            $query->set('tax_query', $tax_query);
        }
        
        // Configurar paginación
        $resultados_per_page = get_theme_mod('groups_per_page', 16);
        $query->set('posts_per_page', $resultados_per_page);
        
        // Mejorar relevancia de búsqueda
        $query->set('orderby', 'relevance');
        
        // Buscar también en meta fields para grupos
        add_filter('posts_search', 'telegram_groups_extend_search', 10, 2);
    }
}
add_action('pre_get_posts', 'telegram_groups_modify_search_query');

// Extender búsqueda a campos personalizados
function telegram_groups_extend_search($search, $wp_query) {
    global $wpdb;
    
    if (empty($search) || !$wp_query->is_search()) {
        return $search;
    }
    
    $search_term = $wp_query->get('s');
    
    if (empty($search_term)) {
        return $search;
    }
    
    // Buscar en campos ACF específicos
    $meta_search = $wpdb->prepare("
        OR (
            {$wpdb->postmeta}.meta_key IN ('descripcion_corta', 'descripcion_completa') 
            AND {$wpdb->postmeta}.meta_value LIKE %s
        )
    ", '%' . $wpdb->esc_like($search_term) . '%');
    
    $search = preg_replace('/\)\s*$/', ') ' . $meta_search . ' )', $search);
    
    return $search;
}

get_footer();
?>