<?php
/**
 * The template for displaying category archive pages
 *
 * @package Telegram_Groups
 */

get_header();

// Obtener término actual
$current_term = get_queried_object();
$categoria_style = get_categoria_style($current_term->slug);

// Obtener filtros
$current_ciudad = isset($_GET['ciudad']) ? sanitize_text_field($_GET['ciudad']) : '';
$current_orden = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : 'fecha';
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Configurar grupos por página
$grupos_per_page = get_theme_mod('groups_per_page', 16);
?>

<main id="primary" class="site-main taxonomy-page">
    <div class="container">
        
        <!-- Header de categoría -->
        <header class="taxonomy-header" style="background: linear-gradient(135deg, <?php echo $categoria_style['color']; ?>, <?php echo $categoria_style['color']; ?>dd);">
            <div class="taxonomy-header-content">
                <div class="taxonomy-icon">
                    <i class="<?php echo $categoria_style['icon']; ?>"></i>
                </div>
                <div class="taxonomy-info">
                    <h1 class="taxonomy-title"><?php echo $current_term->name; ?></h1>
                    <p class="taxonomy-description">
                        <?php 
                        if ($current_term->description) {
                            echo esc_html($current_term->description);
                        } else {
                            printf(__('Explora todos los grupos de %s en nuestra comunidad de Telegram.', 'telegram-groups'), $current_term->name);
                        }
                        ?>
                    </p>
                    <div class="taxonomy-stats">
                        <span class="stat-item">
                            <i class="fas fa-users"></i>
                            <strong><?php echo $current_term->count; ?></strong>
                            <?php _e('grupos', 'telegram-groups'); ?>
                        </span>
                        <?php
                        // Calcular total de miembros en esta categoría
                        $grupos_categoria = get_posts(array(
                            'post_type' => 'grupo',
                            'numberposts' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'categoria_grupo',
                                    'field' => 'term_id',
                                    'terms' => $current_term->term_id
                                )
                            ),
                            'fields' => 'ids'
                        ));
                        
                        $total_miembros = 0;
                        foreach ($grupos_categoria as $grupo_id) {
                            $miembros = get_field('numero_miembros', $grupo_id);
                            $total_miembros += intval($miembros);
                        }
                        ?>
                        <span class="stat-item">
                            <i class="fas fa-user-friends"></i>
                            <strong><?php echo format_member_count($total_miembros); ?></strong>
                            <?php _e('miembros', 'telegram-groups'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Breadcrumbs -->
        <?php if (get_theme_mod('enable_breadcrumbs', true)) : ?>
            <nav class="taxonomy-breadcrumbs" aria-label="<?php _e('Navegación', 'telegram-groups'); ?>">
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
                    <li class="breadcrumb-item active">
                        <?php echo $current_term->name; ?>
                    </li>
                </ol>
            </nav>
        <?php endif; ?>

        <!-- Filtros horizontales arriba -->
        <div class="taxonomy-filters-top">
            <form class="filters-form-horizontal" method="get">
                <!-- Mantener categoría actual -->
                <input type="hidden" name="categoria" value="<?php echo $current_term->slug; ?>">
                
                <div class="filters-row">
                    <!-- Buscador -->
                    <div class="filter-item">
                        <label for="search-input">
                            <i class="fas fa-search"></i>
                            <?php _e('Buscar en esta categoría', 'telegram-groups'); ?>
                        </label>
                        <input type="text" 
                               id="search-input"
                               name="s" 
                               value="<?php echo esc_attr($search_query); ?>" 
                               placeholder="<?php _e('Nombre del grupo...', 'telegram-groups'); ?>">
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
                            // Obtener ciudades que tienen grupos en esta categoría
                            global $wpdb;
                            $ciudades_en_categoria = $wpdb->get_results($wpdb->prepare("
                                SELECT DISTINCT t.term_id, t.name, t.slug, tt.count
                                FROM {$wpdb->terms} t
                                INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                                INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
                                INNER JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id
                                INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
                                WHERE tt.taxonomy = 'ciudad_grupo'
                                AND tt2.taxonomy = 'categoria_grupo'
                                AND tt2.term_id = %d
                                AND p.post_status = 'publish'
                                ORDER BY t.name ASC
                            ", $current_term->term_id));
                            
                            foreach ($ciudades_en_categoria as $ciudad) :
                            ?>
                                <option value="<?php echo esc_attr($ciudad->slug); ?>" 
                                        <?php selected($current_ciudad, $ciudad->slug); ?>>
                                    <?php echo esc_html($ciudad->name); ?>
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
        <div class="taxonomy-main-content-full">
            
            <!-- Filtros activos -->
            <?php if ($search_query || $current_ciudad) : ?>
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
                        
                        <?php if ($current_ciudad) : ?>
                            <?php $city_term = get_term_by('slug', $current_ciudad, 'ciudad_grupo'); ?>
                            <span class="active-filter">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo $city_term ? $city_term->name : $current_ciudad; ?>
                                <a href="<?php echo remove_query_arg('ciudad'); ?>" class="remove-filter">&times;</a>
                            </span>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Toolbar -->
            <div class="taxonomy-toolbar">
                <div class="results-info">
                    <?php
                    global $wp_query;
                    $total_found = $wp_query->found_posts;
                    
                    if ($total_found > 0) :
                        printf(
                            _n(
                                'Mostrando %d grupo de %s',
                                'Mostrando %d grupos de %s',
                                $total_found,
                                'telegram-groups'
                            ),
                            $total_found,
                            $current_term->name
                        );
                    else :
                        printf(__('No se encontraron grupos de %s', 'telegram-groups'), $current_term->name);
                    endif;
                    ?>
                </div>
            </div>
            
            <!-- Resultados -->
            <div class="taxonomy-results">
                
                <?php if (have_posts()) : ?>
                    
                    <div class="grupos-grid">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php get_template_part('template-parts/grupo-card', 'taxonomy'); ?>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="taxonomy-pagination">
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
                    <div class="no-taxonomy-results">
                        <div class="no-results-icon" style="color: <?php echo $categoria_style['color']; ?>">
                            <i class="<?php echo $categoria_style['icon']; ?>"></i>
                        </div>
                        <h3><?php printf(__('No hay grupos de %s', 'telegram-groups'), $current_term->name); ?></h3>
                        <p><?php _e('Aún no se han añadido grupos en esta categoría, pero pronto habrá contenido disponible.', 'telegram-groups'); ?></p>
                        
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

/* Taxonomy Page Styles */
.taxonomy-page {
    padding: 2rem 0;
}

/* Header de taxonomía */
.taxonomy-header {
    color: var(--telegram-white);
    padding: 3rem 0;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border-radius: 0 0 20px 20px;
}

.taxonomy-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.taxonomy-header-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.taxonomy-icon {
    font-size: 4rem;
    opacity: 0.9;
}

.taxonomy-info {
    text-align: left;
}

.taxonomy-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    line-height: 1.2;
}

.taxonomy-description {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0 0 1.5rem 0;
    line-height: 1.5;
}

.taxonomy-stats {
    display: flex;
    gap: 2rem;
}

.taxonomy-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}

.taxonomy-stats .stat-item i {
    opacity: 0.8;
}

.taxonomy-stats .stat-item strong {
    font-size: 1.2rem;
}

/* Breadcrumbs */
.taxonomy-breadcrumbs {
    margin-bottom: 2rem;
}

.breadcrumb-list {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.5rem;
    align-items: center;
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
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: var(--telegram-gray);
    font-size: 0.9rem;
}

/* Filtros horizontales arriba */
.taxonomy-filters-top {
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
.taxonomy-main-content-full {
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
.taxonomy-toolbar {
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
.no-taxonomy-results {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--telegram-white);
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
}

.no-results-icon {
    font-size: 5rem;
    opacity: 0.3;
    margin-bottom: 2rem;
}

.no-taxonomy-results h3 {
    color: var(--telegram-dark);
    font-size: 2rem;
    margin: 0 0 1rem 0;
}

.no-taxonomy-results p {
    color: var(--telegram-gray);
    font-size: 1.1rem;
    margin: 0 0 3rem 0;
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
.taxonomy-pagination {
    margin-top: 3rem;
    text-align: center;
}

.taxonomy-pagination .nav-links {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.taxonomy-pagination a,
.taxonomy-pagination span {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.taxonomy-pagination a {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    border: 2px solid #e9ecef;
}

.taxonomy-pagination a:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border-color: var(--telegram-blue);
}

.taxonomy-pagination .current {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: 2px solid var(--telegram-blue);
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
    .taxonomy-header {
        padding: 2rem 0;
    }
    
    .taxonomy-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .taxonomy-title {
        font-size: 2rem;
    }
    
    .taxonomy-stats {
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
    
    .taxonomy-filters-top {
        padding: 1.5rem;
    }
    
    .breadcrumb-list {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .taxonomy-page {
        padding: 1rem 0;
    }
    
    .taxonomy-title {
        font-size: 1.5rem;
    }
    
    .taxonomy-icon {
        font-size: 3rem;
    }
    
    .no-taxonomy-results {
        padding: 2rem 1rem;
    }
    
    .no-results-actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
// JavaScript para taxonomías
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