<?php
/**
 * The template for displaying city archive pages
 *
 * @package Telegram_Groups
 */

get_header();

// Obtener término actual
$current_term = get_queried_object();

// Obtener filtros
$current_categoria = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
$current_orden = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : 'fecha';
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
?>

<main id="primary" class="site-main taxonomy-ciudad-page">
    <div class="container">
        
        <!-- Header de ciudad -->
        <header class="ciudad-header">
            <div class="ciudad-header-content">
                <div class="ciudad-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="ciudad-info">
                    <h1 class="ciudad-title"><?php echo $current_term->name; ?></h1>
                    <p class="ciudad-description">
                        <?php 
                        if ($current_term->description) {
                            echo esc_html($current_term->description);
                        } else {
                            printf(__('Descubre los mejores grupos de Telegram en %s. Conéctate con tu comunidad local.', 'telegram-groups'), $current_term->name);
                        }
                        ?>
                    </p>
                    <div class="ciudad-stats">
                        <span class="stat-item">
                            <i class="fas fa-users"></i>
                            <strong><?php echo $current_term->count; ?></strong>
                            <?php _e('grupos', 'telegram-groups'); ?>
                        </span>
                        <?php
                        // Calcular categorías disponibles en esta ciudad
                        global $wpdb;
                        $categorias_count = $wpdb->get_var($wpdb->prepare("
                            SELECT COUNT(DISTINCT tt2.term_id)
                            FROM {$wpdb->term_relationships} tr
                            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                            INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
                            INNER JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id
                            INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
                            WHERE tt.taxonomy = 'ciudad_grupo'
                            AND tt.term_id = %d
                            AND tt2.taxonomy = 'categoria_grupo'
                            AND p.post_status = 'publish'
                        ", $current_term->term_id));
                        ?>
                        <span class="stat-item">
                            <i class="fas fa-th-large"></i>
                            <strong><?php echo $categorias_count; ?></strong>
                            <?php _e('categorías', 'telegram-groups'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Breadcrumbs -->
        <?php if (get_theme_mod('enable_breadcrumbs', true)) : ?>
            <nav class="ciudad-breadcrumbs" aria-label="<?php _e('Navegación', 'telegram-groups'); ?>">
                <ol class="breadcrumb-list">
                    <li class="breadcrumb-item">
                        <a href="<?php echo home_url(); ?>">
                            <i class="fas fa-home"></i>
                            <?php _e('Inicio', 'telegram-groups'); ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo get_post_type_archive_link('grupo'); ?>">
                            <?php _e('Grupos', 'telegram-groups'); ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo home_url('/ciudades/'); ?>">
                            <?php _e('Ciudades', 'telegram-groups'); ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?php echo $current_term->name; ?>
                    </li>
                </ol>
            </nav>
        <?php endif; ?>

        <!-- Filtros horizontales arriba -->
        <div class="ciudad-filters-top">
            <form class="filters-form-horizontal" method="get">
                <!-- Mantener ciudad actual -->
                <input type="hidden" name="ciudad" value="<?php echo $current_term->slug; ?>">
                
                <div class="filters-row">
                    <!-- Buscador -->
                    <div class="filter-item">
                        <label for="search-input">
                            <i class="fas fa-search"></i>
                            <?php printf(__('Buscar en %s', 'telegram-groups'), $current_term->name); ?>
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
                            // Obtener categorías que tienen grupos en esta ciudad
                            $categorias_en_ciudad = $wpdb->get_results($wpdb->prepare("
                                SELECT DISTINCT t.term_id, t.name, t.slug, COUNT(p.ID) as count
                                FROM {$wpdb->terms} t
                                INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                                INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
                                INNER JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id
                                INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
                                WHERE tt.taxonomy = 'categoria_grupo'
                                AND tt2.taxonomy = 'ciudad_grupo'
                                AND tt2.term_id = %d
                                AND p.post_status = 'publish'
                                GROUP BY t.term_id
                                ORDER BY t.name ASC
                            ", $current_term->term_id));
                            
                            foreach ($categorias_en_ciudad as $categoria) :
                            ?>
                                <option value="<?php echo esc_attr($categoria->slug); ?>" 
                                        <?php selected($current_categoria, $categoria->slug); ?>>
                                    <?php echo esc_html($categoria->name); ?> (<?php echo $categoria->count; ?>)
                                </option>
                            <?php endforeach; ?>
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
        <div class="ciudad-main-content-full">
            
            <!-- Filtros activos -->
            <?php if ($search_query || $current_categoria) : ?>
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
                        
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Toolbar -->
            <div class="ciudad-toolbar">
                <div class="results-info">
                    <?php
                    global $wp_query;
                    $total_found = $wp_query->found_posts;
                    
                    if ($total_found > 0) :
                        printf(
                            _n(
                                'Mostrando %d grupo en %s',
                                'Mostrando %d grupos en %s',
                                $total_found,
                                'telegram-groups'
                            ),
                            $total_found,
                            $current_term->name
                        );
                    else :
                        printf(__('No se encontraron grupos en %s', 'telegram-groups'), $current_term->name);
                    endif;
                    ?>
                </div>
            </div>
            
            <!-- Resultados -->
            <div class="ciudad-results">
                
                <?php if (have_posts()) : ?>
                    
                    <div class="grupos-grid">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php get_template_part('template-parts/grupo-card', 'ciudad'); ?>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="ciudad-pagination">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Anterior', 'telegram-groups'),
                            'next_text' => __('Siguiente', 'telegram-groups') . ' <i class="fas fa-chevron-right"></i>',
                        ));
                        ?>
                    </div>
                    
                <?php else : ?>
                    
                    <!-- Sin resultados -->
                    <div class="no-ciudad-results">
                        <div class="no-results-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3><?php printf(__('No hay grupos en %s', 'telegram-groups'), $current_term->name); ?></h3>
                        <p><?php _e('Aún no se han añadido grupos en esta ciudad, pero pronto habrá contenido disponible.', 'telegram-groups'); ?></p>
                        
                        <div class="no-results-actions">
                            <a href="<?php echo get_post_type_archive_link('grupo'); ?>" class="btn btn-primary">
                                <i class="fas fa-list"></i>
                                <?php _e('Ver todos los grupos', 'telegram-groups'); ?>
                            </a>
                            <a href="<?php echo home_url('/añadir-grupo'); ?>" class="btn btn-secondary">
                                <i class="fas fa-plus"></i>
                                <?php _e('Añadir grupo', 'telegram-groups'); ?>
                            </a>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
            </div>
            
        </div>
        
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

/* Reset específico para la página */
.taxonomy-ciudad-page * {
    box-sizing: border-box;
}

.taxonomy-ciudad-page {
    padding: 2rem 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: var(--telegram-dark);
}

/* Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Header de ciudad */
.ciudad-header {
    background: linear-gradient(135deg, #34495e, #2c3e50);
    color: var(--telegram-white);
    padding: 3rem 0;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    border-radius: 0 0 20px 20px;
    overflow: hidden;
}

.ciudad-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 10px,
        rgba(255,255,255,0.05) 10px,
        rgba(255,255,255,0.05) 20px
    );
    z-index: 1;
}

.ciudad-header-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.ciudad-icon {
    font-size: 4rem;
    opacity: 0.9;
    color: var(--telegram-white);
}

.ciudad-info {
    text-align: left;
}

.ciudad-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    line-height: 1.2;
    color: var(--telegram-white);
}

.ciudad-description {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0 0 1.5rem 0;
    line-height: 1.5;
    color: var(--telegram-white);
}

.ciudad-stats {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.ciudad-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    color: var(--telegram-white);
}

.ciudad-stats .stat-item i {
    opacity: 0.8;
}

.ciudad-stats .stat-item strong {
    font-size: 1.2rem;
    font-weight: 700;
}

/* Breadcrumbs */
.ciudad-breadcrumbs {
    margin-bottom: 2rem;
}

.breadcrumb-list {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item:not(:last-child)::after {
    content: '/';
    margin-left: 0.5rem;
    color: var(--telegram-gray);
}

.breadcrumb-item a {
    color: var(--telegram-blue);
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: background 0.3s ease;
}

.breadcrumb-item a:hover {
    background: rgba(0, 136, 204, 0.1);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: var(--telegram-gray);
    font-size: 0.9rem;
    font-weight: 600;
}

/* Filtros horizontales arriba */
.ciudad-filters-top {
    background: var(--telegram-white);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
    border: 1px solid #e9ecef;
}

.filters-form-horizontal {
    width: 100%;
}

.filters-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.5fr auto;
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
    text-align: center;
}

.filter-item input,
.filter-item select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
    font-family: inherit;
    transition: border-color 0.3s ease;
    background: var(--telegram-white);
}

.filter-item input:focus,
.filter-item select:focus {
    outline: none;
    border-color: var(--telegram-blue);
    box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1);
}

.btn-filter-apply-horizontal {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-family: inherit;
    white-space: nowrap;
}

.btn-filter-apply-horizontal:hover {
    background: linear-gradient(135deg, var(--telegram-light-blue), var(--telegram-blue));
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 136, 204, 0.3);
}

/* Contenido principal ancho completo */
.ciudad-main-content-full {
    width: 100%;
}

/* Filtros activos */
.active-filters {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
}

.active-filters h4 {
    margin: 0 0 1rem 0;
    color: var(--telegram-dark);
    font-size: 1rem;
    font-weight: 600;
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
    font-weight: 500;
}

.remove-filter {
    color: var(--telegram-white);
    text-decoration: none;
    font-weight: bold;
    margin-left: 0.5rem;
    opacity: 0.8;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.remove-filter:hover {
    opacity: 1;
    color: var(--telegram-white);
}

/* Toolbar */
.ciudad-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.results-info {
    font-weight: 600;
    color: var(--telegram-dark);
    font-size: 1rem;
}

/* Grid más ancho para aprovechar todo el espacio */
.grupos-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 2rem !important;
}

/* Paginación */
.ciudad-pagination {
    margin-top: 3rem;
    text-align: center;
}

.ciudad-pagination .nav-links {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.ciudad-pagination a,
.ciudad-pagination span {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ciudad-pagination a {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    border: 2px solid #e9ecef;
}

.ciudad-pagination a:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border-color: var(--telegram-blue);
}

.ciudad-pagination .current {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: 2px solid var(--telegram-blue);
}

/* Sin resultados */
.no-ciudad-results {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--telegram-white);
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
    border: 1px solid #e9ecef;
}

.no-ciudad-results .no-results-icon {
    font-size: 5rem;
    color: var(--telegram-gray);
    opacity: 0.3;
    margin-bottom: 2rem;
}

.no-ciudad-results h3 {
    color: var(--telegram-dark);
    font-size: 2rem;
    margin: 0 0 1rem 0;
    font-weight: 700;
}

.no-ciudad-results p {
    color: var(--telegram-gray);
    font-size: 1.1rem;
    margin: 0 0 3rem 0;
    line-height: 1.6;
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
    font-family: inherit;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--telegram-light-blue), var(--telegram-blue));
    transform: translateY(-2px);
    color: var(--telegram-white);
    text-decoration: none;
}

.btn-secondary {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    border: 2px solid var(--telegram-blue);
}

.btn-secondary:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    text-decoration: none;
}

/* Responsive */
@media (max-width: 1400px) {
    .grupos-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 1024px) {
    .filters-row {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .grupos-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .taxonomy-ciudad-page {
        padding: 1rem 0;
    }
    
    .container {
        padding: 0 15px;
    }
    
    .ciudad-header {
        padding: 2rem 0;
        margin-bottom: 1rem;
    }
    
    .ciudad-header-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .ciudad-title {
        font-size: 2rem;
    }
    
    .ciudad-icon {
        font-size: 3rem;
    }
    
    .ciudad-stats {
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .filters-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .grupos-grid {
        grid-template-columns: 1fr !important;
    }
    
    .ciudad-filters-top {
        padding: 1.5rem;
    }
    
    .breadcrumb-list {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .ciudad-title {
        font-size: 1.5rem;
    }
    
    .ciudad-description {
        font-size: 1rem;
    }
    
    .no-ciudad-results {
        padding: 2rem 1rem;
    }
    
    .no-results-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .no-results-actions .btn {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}
</style>

<script>
// JavaScript para ciudades
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filtros
    const filterForm = document.querySelector('.filters-form-horizontal');
    if (filterForm) {
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }
});
</script>

<?php get_footer(); ?>