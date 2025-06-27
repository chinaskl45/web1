<?php
/**
 * The template for displaying archive pages for grupos
 *
 * @package Telegram_Groups
 */

get_header();

// Obtener parámetros de filtros
$current_categoria = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
$current_ciudad = isset($_GET['ciudad']) ? sanitize_text_field($_GET['ciudad']) : '';
$current_estado = isset($_GET['estado']) ? sanitize_text_field($_GET['estado']) : '';
$current_orden = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : 'fecha';
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Configurar grupos por página desde Customizer
$grupos_per_page = get_theme_mod('groups_per_page', 16);
?>

<main id="primary" class="site-main">
    <div class="container">
        
        <!-- Header del archivo -->
        <header class="archive-header">
            <div class="archive-header-content">
                <h1 class="archive-title">
                    <i class="fas fa-users"></i>
                    <?php _e('Todos los Grupos de Telegram', 'telegram-groups'); ?>
                </h1>
                <p class="archive-description">
                    <?php _e('Explora nuestra colección completa de grupos de Telegram organizados por categorías y ciudades.', 'telegram-groups'); ?>
                </p>
                
                <!-- Estadísticas rápidas -->
                <div class="archive-stats">
                    <?php
                    $total_grupos = wp_count_posts('grupo')->publish;
                    $total_categorias = wp_count_terms('categoria_grupo');
                    $total_ciudades = wp_count_terms('ciudad_grupo');
                    ?>
                    <div class="archive-stat">
                        <span class="stat-number"><?php echo number_format($total_grupos); ?></span>
                        <span class="stat-label"><?php _e('Grupos', 'telegram-groups'); ?></span>
                    </div>
                    <div class="archive-stat">
                        <span class="stat-number"><?php echo $total_categorias; ?></span>
                        <span class="stat-label"><?php _e('Categorías', 'telegram-groups'); ?></span>
                    </div>
                    <div class="archive-stat">
                        <span class="stat-number"><?php echo $total_ciudades; ?></span>
                        <span class="stat-label"><?php _e('Ciudades', 'telegram-groups'); ?></span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Filtros horizontales arriba -->
        <div class="archive-filters-top">
            <form class="filters-form-horizontal" method="get" id="grupos-filters">
                <div class="filters-row">
                    <!-- Buscador -->
                    <div class="filter-item">
                        <label for="search-input">
                            <i class="fas fa-search"></i>
                            <?php _e('Buscar', 'telegram-groups'); ?>
                        </label>
                        <input type="text" 
                               id="search-input"
                               name="s" 
                               value="<?php echo esc_attr($search_query); ?>" 
                               placeholder="<?php _e('Nombre del grupo...', 'telegram-groups'); ?>">
                    </div>
                    
                    <!-- Filtro por categoría -->
                    <div class="filter-item">
                        <label for="categoria-filter">
                            <i class="fas fa-th-large"></i>
                            <?php _e('Categoría', 'telegram-groups'); ?>
                        </label>
                        <select name="categoria" id="categoria-filter">
                            <option value=""><?php _e('Todas las categorías', 'telegram-groups'); ?></option>
                            <?php
                            $categorias = get_terms(array(
                                'taxonomy' => 'categoria_grupo',
                                'hide_empty' => true,
                                'orderby' => 'name',
                                'order' => 'ASC'
                            ));
                            
                            if ($categorias && !is_wp_error($categorias)) :
                                foreach ($categorias as $categoria) :
                            ?>
                                <option value="<?php echo esc_attr($categoria->slug); ?>" 
                                        <?php selected($current_categoria, $categoria->slug); ?>>
                                    <?php echo esc_html($categoria->name); ?> (<?php echo $categoria->count; ?>)
                                </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                    <!-- Filtro por ciudad -->
                    <div class="filter-item">
                        <label for="ciudad-filter">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php _e('Ciudad', 'telegram-groups'); ?>
                        </label>
                        <select name="ciudad" id="ciudad-filter">
                            <option value=""><?php _e('Todas las ciudades', 'telegram-groups'); ?></option>
                            <?php
                            $ciudades = get_terms(array(
                                'taxonomy' => 'ciudad_grupo',
                                'hide_empty' => true,
                                'orderby' => 'name',
                                'order' => 'ASC'
                            ));
                            
                            if ($ciudades && !is_wp_error($ciudades)) :
                                foreach ($ciudades as $ciudad) :
                            ?>
                                <option value="<?php echo esc_attr($ciudad->slug); ?>" 
                                        <?php selected($current_ciudad, $ciudad->slug); ?>>
                                    <?php echo esc_html($ciudad->name); ?> (<?php echo $ciudad->count; ?>)
                                </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                    <!-- Ordenar por -->
                    <div class="filter-item">
                        <label for="orden-filter">
                            <i class="fas fa-sort"></i>
                            <?php _e('Ordenar por', 'telegram-groups'); ?>
                        </label>
                        <select name="orden" id="orden-filter">
                            <option value="fecha" <?php selected($current_orden, 'fecha'); ?>>
                                <?php _e('Más recientes', 'telegram-groups'); ?>
                            </option>
                            <option value="miembros" <?php selected($current_orden, 'miembros'); ?>>
                                <?php _e('Más miembros', 'telegram-groups'); ?>
                            </option>
                            <option value="alfabetico" <?php selected($current_orden, 'alfabetico'); ?>>
                                <?php _e('A-Z', 'telegram-groups'); ?>
                            </option>
                            <option value="actualizado" <?php selected($current_orden, 'actualizado'); ?>>
                                <?php _e('Recientemente actualizados', 'telegram-groups'); ?>
                            </option>
                        </select>
                    </div>
                    
                    <!-- Botón aplicar -->
                    <div class="filter-item">
                        <button type="submit" class="btn-filter-apply-horizontal">
                            <i class="fas fa-search"></i>
                            <?php _e('Filtrar', 'telegram-groups'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Contenido principal sin sidebar -->
        <div class="archive-main-content-full">
            
            <!-- Filtros activos -->
            <?php if ($current_categoria || $current_ciudad || $current_estado || $search_query) : ?>
                <div class="active-filters">
                    <h4><?php _e('Filtros activos:', 'telegram-groups'); ?></h4>
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
                                <i class="fas fa-th-large"></i>
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
                        
                        <?php if ($current_estado) : ?>
                            <span class="active-filter">
                                <i class="fas fa-circle"></i>
                                <?php echo ucfirst($current_estado); ?>
                                <a href="<?php echo remove_query_arg('estado'); ?>" class="remove-filter">&times;</a>
                            </span>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Barra de resultados -->
            <div class="archive-toolbar">
                <div class="results-info">
                    <?php
                    global $wp_query;
                    $total_found = $wp_query->found_posts;
                    
                    if ($total_found > 0) :
                        printf(
                            _n(
                                'Mostrando %d grupo',
                                'Mostrando %d grupos',
                                $total_found,
                                'telegram-groups'
                            ),
                            $total_found
                        );
                    else :
                        _e('No se encontraron grupos', 'telegram-groups');
                    endif;
                    ?>
                </div>
            </div>
            
            <!-- Contenedor de grupos -->
            <div class="grupos-container" id="grupos-container">
                
                <?php if (have_posts()) : ?>
                    
                    <div class="grupos-grid" id="grupos-grid">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php get_template_part('template-parts/grupo-card', 'archive'); ?>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="archive-pagination">
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
                    <div class="no-grupos-found">
                        <div class="no-results-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3><?php _e('No se encontraron grupos', 'telegram-groups'); ?></h3>
                        <p><?php _e('No hay grupos que coincidan con los filtros seleccionados. Intenta cambiar los criterios de búsqueda.', 'telegram-groups'); ?></p>
                        
                        <div class="no-results-suggestions">
                            <h4><?php _e('Sugerencias:', 'telegram-groups'); ?></h4>
                            <ul>
                                <li><?php _e('Verifica la ortografía de las palabras clave', 'telegram-groups'); ?></li>
                                <li><?php _e('Prueba con términos más generales', 'telegram-groups'); ?></li>
                                <li><?php _e('Reduce el número de filtros aplicados', 'telegram-groups'); ?></li>
                                <li><?php _e('Explora diferentes categorías', 'telegram-groups'); ?></li>
                            </ul>
                            
                            <div class="no-results-actions">
                                <a href="<?php echo get_post_type_archive_link('grupo'); ?>" class="btn btn-primary">
                                    <i class="fas fa-list"></i>
                                    <?php _e('Ver todos los grupos', 'telegram-groups'); ?>
                                </a>
                                <a href="<?php echo home_url('/añadir-grupo'); ?>" class="btn btn-secondary">
                                    <i class="fas fa-plus"></i>
                                    <?php _e('Añadir un grupo', 'telegram-groups'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
            </div>
            
        </div>
        
        <!-- Grupos destacados al final (solo si no hay filtros) -->
        <?php if (!$current_categoria && !$current_ciudad && !$current_estado && !$search_query) : ?>
            <section class="archive-featured-section">
                <h2 class="featured-section-title">
                    <i class="fas fa-star"></i>
                    <?php _e('Grupos Destacados', 'telegram-groups'); ?>
                </h2>
                
                <div class="featured-grupos-grid">
                    <?php
                    $grupos_destacados = new WP_Query(array(
                        'post_type' => 'grupo',
                        'posts_per_page' => 8,
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
                    
                    if ($grupos_destacados->have_posts()) :
                        while ($grupos_destacados->have_posts()) : $grupos_destacados->the_post();
                            get_template_part('template-parts/grupo-card', 'featured');
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </section>
        <?php endif; ?>
        
    </div>
</main>

<style>
/* Variables CSS base */
:root {
    --telegram-blue: #0088cc;
    --telegram-light-blue: #229ED9;
    --telegram-bg: #f4f4f5;
    --telegram-white: #ffffff;
    --telegram-dark: #2c3e50;
    --telegram-gray: #95a5a6;
    --telegram-success: #27ae60;
    --telegram-warning: #f39c12;
    --telegram-danger: #e74c3c;
}

/* Archive Page Styles */
.archive-header {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    padding: 3rem 0;
    margin-bottom: 2rem;
    text-align: center;
    border-radius: 0 0 20px 20px;
}

.archive-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.archive-description {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto 2rem;
    line-height: 1.6;
}

.archive-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
}

.archive-stat {
    text-align: center;
}

.archive-stat .stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.archive-stat .stat-label {
    display: block;
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 0.25rem;
}

/* Filtros horizontales arriba */
.archive-filters-top {
    background: var(--telegram-white);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
}

.filters-form-horizontal {
    width: 100%;
}

.filters-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.5fr 1.5fr auto;
    gap: 1.5rem;
    align-items: end;
}

.filter-item {
    display: flex;
    flex-direction: column;
}

.filter-item label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--telegram-dark);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-item label i {
    color: var(--telegram-blue);
    width: 15px;
}

.filter-item input,
.filter-item select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
}

.filter-item input:focus,
.filter-item select:focus {
    outline: none;
    border-color: var(--telegram-blue);
}

.btn-filter-apply-horizontal {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: background 0.3s ease;
    font-size: 0.9rem;
    white-space: nowrap;
}

.btn-filter-apply-horizontal:hover {
    background: var(--telegram-light-blue);
}

/* Contenido principal ancho completo */
.archive-main-content-full {
    width: 100%;
}

/* Filtros activos */
.active-filters {
    background: var(--telegram-white);
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 136, 204, 0.05);
}

.active-filters h4 {
    margin: 0 0 1rem 0;
    color: var(--telegram-dark);
    font-size: 1rem;
}

.active-filters-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.active-filter {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remove-filter {
    color: var(--telegram-white);
    text-decoration: none;
    font-weight: bold;
    margin-left: 0.5rem;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.remove-filter:hover {
    opacity: 1;
    color: var(--telegram-white);
}

/* Toolbar */
.archive-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem 1.5rem;
    background: var(--telegram-white);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 136, 204, 0.05);
}

.results-info {
    font-weight: 600;
    color: var(--telegram-dark);
}

/* Grid más ancho para aprovechar todo el espacio */
.grupos-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 2rem !important;
}

/* Sin resultados */
.no-grupos-found {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--telegram-white);
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
}

.no-results-icon {
    font-size: 4rem;
    color: var(--telegram-gray);
    opacity: 0.5;
    margin-bottom: 1.5rem;
}

.no-grupos-found h3 {
    color: var(--telegram-dark);
    font-size: 1.8rem;
    margin: 0 0 1rem 0;
}

.no-grupos-found p {
    color: var(--telegram-gray);
    font-size: 1.1rem;
    margin: 0 0 2rem 0;
}

.no-results-suggestions {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
    text-align: left;
    max-width: 500px;
    margin: 0 auto;
}

.no-results-suggestions h4 {
    margin: 0 0 1rem 0;
    color: var(--telegram-blue);
}

.no-results-suggestions ul {
    margin: 0 0 2rem 0;
    padding-left: 1.5rem;
    color: var(--telegram-gray);
}

.no-results-suggestions li {
    margin-bottom: 0.5rem;
}

.no-results-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.no-results-actions .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
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
.archive-pagination {
    margin-top: 3rem;
    text-align: center;
}

.archive-pagination .nav-links {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.archive-pagination a,
.archive-pagination span {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.archive-pagination a {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    border: 2px solid #e9ecef;
}

.archive-pagination a:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border-color: var(--telegram-blue);
}

.archive-pagination .current {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: 2px solid var(--telegram-blue);
}

/* Sección destacados */
.archive-featured-section {
    margin-top: 4rem;
    padding-top: 3rem;
    border-top: 1px solid #e9ecef;
}

.featured-section-title {
    text-align: center;
    color: var(--telegram-dark);
    font-size: 2rem;
    margin: 0 0 2rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.featured-section-title i {
    color: #ffd700;
}

.featured-grupos-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
}

/* Responsive */
@media (max-width: 1400px) {
    .grupos-grid,
    .featured-grupos-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 1024px) {
    .filters-row {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }
    
    .grupos-grid,
    .featured-grupos-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .archive-header {
        padding: 2rem 0;
    }
    
    .archive-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .archive-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .filters-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .grupos-grid,
    .featured-grupos-grid {
        grid-template-columns: 1fr !important;
    }
    
    .archive-filters-top {
        padding: 1.5rem;
    }
    
    .archive-pagination .nav-links {
        flex-direction: column;
        align-items: center;
    }
    
    .no-results-actions {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .archive-description {
        font-size: 1rem;
    }
    
    .no-grupos-found {
        padding: 2rem 1rem;
    }
    
    .no-results-suggestions {
        padding: 1.5rem;
    }
    
    .featured-section-title {
        font-size: 1.5rem;
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<script>
// JavaScript para funcionalidades del archivo
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-submit de filtros cuando cambian
    const filterForm = document.querySelector('.filters-form-horizontal');
    if (filterForm) {
        const filterSelects = filterForm.querySelectorAll('select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }
    
    // Mejorar UX del buscador
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            // Opcional: búsqueda en tiempo real con AJAX
            searchTimeout = setTimeout(() => {
                console.log('Buscando:', this.value);
            }, 500);
        });
    }
    
    // Smooth scroll para navegación interna
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php
// Modificar la query principal para incluir filtros
function telegram_groups_modify_archive_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('grupo')) {
        
        // Grupos por página desde Customizer
        $grupos_per_page = get_theme_mod('groups_per_page', 16);
        $query->set('posts_per_page', $grupos_per_page);
        
        // Meta query base para grupos activos
        $meta_query = array(
            array(
                'key' => 'estado_grupo',
                'value' => 'Activo',
                'compare' => '='
            )
        );
        
        // Filtro por estado si se especifica
        if (isset($_GET['estado']) && !empty($_GET['estado'])) {
            $estado_filter = sanitize_text_field($_GET['estado']);
            $meta_query[0]['value'] = ucfirst($estado_filter);
        }
        
        $query->set('meta_query', $meta_query);
        
        // Tax query para filtros de taxonomías
        $tax_query = array();
        
        if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
            $categoria_filter = sanitize_text_field($_GET['categoria']);
            $tax_query[] = array(
                'taxonomy' => 'categoria_grupo',
                'field' => 'slug',
      'terms' => $categoria_filter
            );
        }
        


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
        
        if (!empty($tax_query)) {
            $tax_query['relation'] = 'AND';
            $query->set('tax_query', $tax_query);
        }
        
        // Ordenamiento
        if (isset($_GET['orden']) && !empty($_GET['orden'])) {
            $orden_filter = sanitize_text_field($_GET['orden']);
            
            switch ($orden_filter) {
                case 'miembros':
                    $query->set('orderby', 'meta_value_num');
                    $query->set('meta_key', 'numero_miembros');
                    $query->set('order', 'DESC');
                    break;
                    
                case 'alfabetico':
                    $query->set('orderby', 'title');
                    $query->set('order', 'ASC');
                    break;
                    
                case 'actualizado':
                    $query->set('orderby', 'modified');
                    $query->set('order', 'DESC');
                    break;
                    
                case 'fecha_asc':
                    $query->set('orderby', 'date');
                    $query->set('order', 'ASC');
                    break;
                    
                default: // 'fecha'
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
        } else {
            // Ordenamiento por defecto
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
    }
}
add_action('pre_get_posts', 'telegram_groups_modify_archive_query');

get_footer();
?>        