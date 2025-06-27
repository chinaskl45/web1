<?php
/**
 * The template for displaying featured groups archive
 *
 * @package Telegram_Groups
 */

get_header();

// Obtener término actual
$current_term = get_queried_object();

// Obtener filtros
$current_categoria = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
$current_ciudad = isset($_GET['ciudad']) ? sanitize_text_field($_GET['ciudad']) : '';
$current_orden = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : 'miembros';
?>

<main id="primary" class="site-main taxonomy-destacados-page">
    <div class="container">
        
        <!-- Header de destacados -->
        <header class="destacados-header">
            <div class="destacados-header-content">
                <div class="destacados-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="destacados-info">
                    <h1 class="destacados-title"><?php _e('Grupos Destacados', 'telegram-groups'); ?></h1>
                    <p class="destacados-description">
                        <?php _e('Los mejores grupos de Telegram seleccionados por nuestra comunidad. Calidad garantizada y comunidades activas.', 'telegram-groups'); ?>
                    </p>
                    <div class="destacados-stats">
                        <span class="stat-item">
                            <i class="fas fa-star"></i>
                            <strong><?php echo $current_term->count; ?></strong>
                            <?php _e('grupos destacados', 'telegram-groups'); ?>
                        </span>
                        <?php
                        // Calcular total de miembros en grupos destacados
                        $grupos_destacados_ids = get_posts(array(
                            'post_type' => 'grupo',
                            'numberposts' => -1,
                            'tax_query' => array(
Copiar
Guardar en Notion
continua
Copy                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'destacados',
                                    'field' => 'term_id',
                                    'terms' => $current_term->term_id
                                )
                            ),
                            'meta_query' => array(
                                array(
                                    'key' => 'estado_grupo',
                                    'value' => 'Activo',
                                    'compare' => '='
                                )
                            ),
                            'fields' => 'ids'
                        ));
                        
                        $total_miembros_destacados = 0;
                        foreach ($grupos_destacados_ids as $grupo_id) {
                            $miembros = get_field('numero_miembros', $grupo_id);
                            $total_miembros_destacados += intval($miembros);
                        }
                        ?>
                        <span class="stat-item">
                            <i class="fas fa-user-friends"></i>
                            <strong><?php echo format_member_count($total_miembros_destacados); ?></strong>
                            <?php _e('miembros totales', 'telegram-groups'); ?>
                        </span>
                        <span class="stat-item">
                            <i class="fas fa-trophy"></i>
                            <strong><?php echo number_format(($total_miembros_destacados / max($current_term->count, 1)), 0); ?></strong>
                            <?php _e('promedio por grupo', 'telegram-groups'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Breadcrumbs -->
        <?php if (get_theme_mod('enable_breadcrumbs', true)) : ?>
            <nav class="destacados-breadcrumbs" aria-label="<?php _e('Navegación', 'telegram-groups'); ?>">
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
                        <?php _e('Destacados', 'telegram-groups'); ?>
                    </li>
                </ol>
            </nav>
        <?php endif; ?>

        <div class="destacados-content-wrapper">
            
            <!-- Filtros laterales -->
            <aside class="destacados-filters">
                <div class="filters-sticky">
                    <h3 class="filters-title">
                        <i class="fas fa-filter"></i>
                        <?php _e('Filtrar Destacados', 'telegram-groups'); ?>
                    </h3>
                    
                    <form class="filters-form" method="get">
                        
                        <!-- Filtro por categoría -->
                        <div class="filter-group">
                            <label for="categoria-filter">
                                <i class="fas fa-th-large"></i>
                                <?php _e('Categoría', 'telegram-groups'); ?>
                            </label>
                            <select name="categoria" id="categoria-filter">
                                <option value=""><?php _e('Todas las categorías', 'telegram-groups'); ?></option>
                                <?php
                                // Obtener categorías que tienen grupos destacados
                                global $wpdb;
                                $categorias_destacadas = $wpdb->get_results($wpdb->prepare("
                                    SELECT DISTINCT t.term_id, t.name, t.slug, COUNT(p.ID) as count
                                    FROM {$wpdb->terms} t
                                    INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                                    INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                    INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
                                    INNER JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id
                                    INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
                                    WHERE tt.taxonomy = 'categoria_grupo'
                                    AND tt2.taxonomy = 'destacados'
                                    AND tt2.term_id = %d
                                    AND p.post_status = 'publish'
                                    GROUP BY t.term_id
                                    ORDER BY count DESC, t.name ASC
                                ", $current_term->term_id));
                                
                                foreach ($categorias_destacadas as $categoria) :
                                ?>
                                    <option value="<?php echo esc_attr($categoria->slug); ?>" 
                                            <?php selected($current_categoria, $categoria->slug); ?>>
                                        <?php echo esc_html($categoria->name); ?> (<?php echo $categoria->count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Filtro por ciudad -->
                        <div class="filter-group">
                            <label for="ciudad-filter">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php _e('Ciudad', 'telegram-groups'); ?>
                            </label>
                            <select name="ciudad" id="ciudad-filter">
                                <option value=""><?php _e('Todas las ciudades', 'telegram-groups'); ?></option>
                                <?php
                                // Obtener ciudades que tienen grupos destacados
                                $ciudades_destacadas = $wpdb->get_results($wpdb->prepare("
                                    SELECT DISTINCT t.term_id, t.name, t.slug, COUNT(p.ID) as count
                                    FROM {$wpdb->terms} t
                                    INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                                    INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                    INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
                                    INNER JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id
                                    INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
                                    WHERE tt.taxonomy = 'ciudad_grupo'
                                    AND tt2.taxonomy = 'destacados'
                                    AND tt2.term_id = %d
                                    AND p.post_status = 'publish'
                                    GROUP BY t.term_id
                                    ORDER BY count DESC, t.name ASC
                                ", $current_term->term_id));
                                
                                foreach ($ciudades_destacadas as $ciudad) :
                                ?>
                                    <option value="<?php echo esc_attr($ciudad->slug); ?>" 
                                            <?php selected($current_ciudad, $ciudad->slug); ?>>
                                        <?php echo esc_html($ciudad->name); ?> (<?php echo $ciudad->count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Ordenar por -->
                        <div class="filter-group">
                            <label for="orden-filter">
                                <i class="fas fa-sort"></i>
                                <?php _e('Ordenar por', 'telegram-groups'); ?>
                            </label>
                            <select name="orden" id="orden-filter">
                                <option value="miembros" <?php selected($current_orden, 'miembros'); ?>>
                                    <?php _e('Más miembros', 'telegram-groups'); ?>
                                </option>
                                <option value="fecha" <?php selected($current_orden, 'fecha'); ?>>
                                    <?php _e('Más recientes', 'telegram-groups'); ?>
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
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-search"></i>
                            <?php _e('Aplicar Filtros', 'telegram-groups'); ?>
                        </button>
                        
                    </form>
                    
                    <!-- Estadísticas de destacados -->
                    <div class="estadisticas-destacados">
                        <h4>
                            <i class="fas fa-chart-line"></i>
                            <?php _e('Estadísticas Premium', 'telegram-groups'); ?>
                        </h4>
                        <div class="stats-destacados-grid">
                            <?php
                            // Grupo con más miembros
                            $top_grupo = new WP_Query(array(
                                'post_type' => 'grupo',
                                'posts_per_page' => 1,
                                'orderby' => 'meta_value_num',
                                'meta_key' => 'numero_miembros',
                                'order' => 'DESC',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'destacados',
                                        'field' => 'term_id',
                                        'terms' => $current_term->term_id
                                    )
                                )
                            ));
                            
                            if ($top_grupo->have_posts()) :
                                $top_grupo->the_post();
                                $max_miembros = get_field('numero_miembros') ?: 0;
                                wp_reset_postdata();
                            ?>
                                <div class="stat-destacado-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number"><?php echo format_member_count($max_miembros); ?></span>
                                        <span class="stat-label"><?php _e('Grupo más grande', 'telegram-groups'); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="stat-destacado-item">
                                <div class="stat-icon">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-number">100%</span>
                                    <span class="stat-label"><?php _e('Grupos activos', 'telegram-groups'); ?></span>
                                </div>
                            </div>
                            
                            <?php
                            // Calcular categoría más popular
                            $cat_popular = $wpdb->get_row($wpdb->prepare("
                                SELECT t.name, COUNT(p.ID) as count
                                FROM {$wpdb->terms} t
                                INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                                INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
                                INNER JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id
                                INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
                                WHERE tt.taxonomy = 'categoria_grupo'
                                AND tt2.taxonomy = 'destacados'
                                AND tt2.term_id = %d
                                AND p.post_status = 'publish'
                                GROUP BY t.term_id
                                ORDER BY count DESC
                                LIMIT 1
                            ", $current_term->term_id));
                            ?>
                            
                            <?php if ($cat_popular) : ?>
                                <div class="stat-destacado-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number"><?php echo $cat_popular->count; ?></span>
                                        <span class="stat-label"><?php echo $cat_popular->name; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Criterios de selección -->
                    <div class="criterios-destacados">
                        <h4>
                            <i class="fas fa-clipboard-check"></i>
                            <?php _e('Criterios de Selección', 'telegram-groups'); ?>
                        </h4>
                        <div class="criterios-list">
                            <div class="criterio-item">
                                <i class="fas fa-users"></i>
                                <span><?php _e('Comunidad activa', 'telegram-groups'); ?></span>
                            </div>
                            <div class="criterio-item">
                                <i class="fas fa-shield-check"></i>
                                <span><?php _e('Moderación de calidad', 'telegram-groups'); ?></span>
                            </div>
                            <div class="criterio-item">
                                <i class="fas fa-heart"></i>
                                <span><?php _e('Valoraciones positivas', 'telegram-groups'); ?></span>
                            </div>
                            <div class="criterio-item">
                                <i class="fas fa-clock"></i>
                                <span><?php _e('Actualizaciones frecuentes', 'telegram-groups'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </aside>
            
            <!-- Contenido principal -->
            <div class="destacados-main-content">
                
                <!-- Filtros activos -->
                <?php if ($current_categoria || $current_ciudad) : ?>
                    <div class="active-filters">
                        <h4><?php _e('Filtros activos:', 'telegram-groups'); ?></h4>
                        <div class="active-filters-list">
                            
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
                            
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Toolbar -->
                <div class="destacados-toolbar">
                    <div class="results-info">
                        <?php
                        global $wp_query;
                        $total_found = $wp_query->found_posts;
                        
                        if ($total_found > 0) :
                            printf(
                                _n(
                                    'Mostrando %d grupo destacado',
                                    'Mostrando %d grupos destacados',
                                    $total_found,
                                    'telegram-groups'
                                ),
                                $total_found
                            );
                        else :
                            _e('No se encontraron grupos destacados', 'telegram-groups');
                        endif;
                        ?>
                    </div>
                    
                    <div class="view-toggle">
                        <button class="view-toggle-btn active" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-toggle-btn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Resultados -->
                <div class="destacados-results">
                    
                    <?php if (have_posts()) : ?>
                        
                        <div class="grupos-grid grid-view active">
                            <?php while (have_posts()) : the_post(); ?>
                                <?php get_template_part('template-parts/grupo-card', 'destacado'); ?>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="grupos-list list-view">
                            <?php 
                            rewind_posts();
                            while (have_posts()) : the_post(); 
                            ?>
                                <?php get_template_part('template-parts/grupo-card', 'destacado-list'); ?>
                            <?php endwhile; ?>
                        </div>
                        
                        <!-- Paginación -->
                        <div class="destacados-pagination">
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
                        <div class="no-destacados-results">
                            <div class="no-results-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3><?php _e('No hay grupos destacados', 'telegram-groups'); ?></h3>
                            <p><?php _e('Aún no se han seleccionado grupos destacados con estos criterios. Revisa otros filtros o explora todas las categorías.', 'telegram-groups'); ?></p>
                            
                            <div class="no-results-actions">
                                <a href="<?php echo get_post_type_archive_link('grupo'); ?>" class="btn btn-primary">
                                    <i class="fas fa-list"></i>
                                    <?php _e('Ver todos los grupos', 'telegram-groups'); ?>
                                </a>
                                <a href="<?php echo home_url('/'); ?>" class="btn btn-secondary">
                                    <i class="fas fa-home"></i>
                                    <?php _e('Volver al inicio', 'telegram-groups'); ?>
                                </a>
                            </div>
                        </div>
                        
                    <?php endif; ?>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
</main>

<style>
/* Destacados Page Styles */
.taxonomy-destacados-page {
    padding: 2rem 0;
}

/* Header de destacados */
.destacados-header {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: var(--telegram-white);
    padding: 3rem 0;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.destacados-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
    z-index: 1;
}

.destacados-header-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    max-width: 900px;
    margin: 0 auto;
}

.destacados-icon {
    font-size: 4rem;
    opacity: 0.9;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.destacados-info {
    text-align: left;
}

.destacados-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    line-height: 1.2;
}

.destacados-description {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0 0 1.5rem 0;
    line-height: 1.5;
}

.destacados-stats {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.destacados-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}

.destacados-stats .stat-item i {
    opacity: 0.8;
}

.destacados-stats .stat-item strong {
    font-size: 1.2rem;
}

/* Estadísticas de destacados */
.estadisticas-destacados,
.criterios-destacados {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.estadisticas-destacados h4,
.criterios-destacados h4 {
    margin: 0 0 1rem 0;
    color: var(--telegram-dark);
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.estadisticas-destacados h4 i,
.criterios-destacados h4 i {
    color: #f39c12;
}

.stats-destacados-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-destacado-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    border-radius: 10px;
    border: 1px solid #f39c12;
}

.stat-destacado-item .stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: var(--telegram-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.stat-destacado-item .stat-content {
    flex: 1;
}

.stat-destacado-item .stat-number {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: #d35400;
    line-height: 1;
}

.stat-destacado-item .stat-label {
    display: block;
    font-size: 0.8rem;
    color: #8e44ad;
    margin-top: 0.25rem;
}

/* Criterios de selección */
.criterios-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.criterio-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #f39c12;
    transition: all 0.3s ease;
}

.criterio-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.criterio-item i {
    color: #f39c12;
    width: 20px;
    text-align: center;
}

.criterio-item span {
    font-size: 0.9rem;
    color: var(--telegram-dark);
    font-weight: 500;
}

/* Reutilizar estilos base */
.destacados-content-wrapper {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    align-items: start;
}

.destacados-filters {
    position: sticky;
    top: 100px;
}

/* Sin resultados destacados */
.no-destacados-results {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(243, 156, 18, 0.1);
    border: 2px solid #f39c12;
}

.no-destacados-results .no-results-icon {
    font-size: 5rem;
    color: #f39c12;
    opacity: 0.3;
    margin-bottom: 2rem;
    animation: pulse 2s infinite;
}

.no-destacados-results h3 {
    color: #d35400;
    font-size: 2rem;
    margin: 0 0 1rem 0;
}

.no-destacados-results p {
    color: #8e44ad;
    font-size: 1.1rem;
    margin: 0 0 3rem 0;
}

/* Responsive para destacados */
@media (max-width: 768px) {
    .destacados-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .destacados-title {
        font-size: 2rem;
    }
    
    .destacados-stats {
        justify-content: center;
        gap: 1rem;
    }
    
    .stats-destacados-grid {
        gap: 0.75rem;
    }
    
    .stat-destacado-item {
        padding: 0.75rem;
    }
}

@media (max-width: 1024px) {
    .destacados-content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .destacados-filters {
        position: static;
        order: 2;
    }
    
    .destacados-main-content {
        order: 1;
    }
}
</style>

<script>
// JavaScript específico para destacados
document.addEventListener('DOMContentLoaded', function() {
    // Animación especial para iconos de destacados
    const starIcons = document.querySelectorAll('.destacados-header .fas fa-star');
    
    starIcons.forEach((icon, index) => {
        setTimeout(() => {
            icon.style.animation = 'pulse 2s infinite';
            icon.style.animationDelay = (index * 0.2) + 's';
        }, 1000);
    });
    
    // Efecto hover especial para tarjetas destacadas
    const destacadoCards = document.querySelectorAll('.grupo-card-destacado');
    
    destacadoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.boxShadow = '0 12px 40px rgba(243, 156, 18, 0.3)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-5px) scale(1)';
            this.style.boxShadow = '0 8px 30px rgba(0, 136, 204, 0.2)';
        });
    });
    
    // Reutilizar funcionalidad de toggle vista
    const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');
    const gruposGrid = document.querySelector('.grupos-grid');
    const gruposList = document.querySelector('.grupos-list');
    
    if (viewToggleBtns.length > 0) {
        viewToggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.getAttribute('data-view');
                
                viewToggleBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                if (view === 'grid') {
                    gruposGrid.classList.add('active');
                    gruposList.classList.remove('active');
                } else {
                    gruposList.classList.add('active');
                    gruposGrid.classList.remove('active');
                }
            });
        });
    }
});
</script>

<?php get_footer(); ?>