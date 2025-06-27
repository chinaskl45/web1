<?php
/**
 * Sidebar template
 * 
 * @package Telegram_Groups
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<div id="secondary" class="widget-area">
    <?php if (is_active_sidebar('sidebar-1')) : ?>
        <?php dynamic_sidebar('sidebar-1'); ?>
    <?php else : ?>
        
        <!-- Widget de búsqueda -->
        <div class="widget widget-search">
            <h3 class="widget-title">
                <i class="fas fa-search"></i>
                <?php _e('Buscar Grupos', 'telegram-groups'); ?>
            </h3>
            <div class="widget-content">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-widget-group">
                        <input type="search" 
                               name="s" 
                               placeholder="<?php _e('Buscar...', 'telegram-groups'); ?>"
                               value="<?php echo get_search_query(); ?>"
                               class="search-widget-input">
                        <button type="submit" class="search-widget-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Widget de grupos populares -->
        <div class="widget widget-popular-groups">
            <h3 class="widget-title">
                <i class="fas fa-fire"></i>
                <?php _e('Grupos Populares', 'telegram-groups'); ?>
            </h3>
            <div class="widget-content">
                <?php
                $grupos_populares = new WP_Query(array(
                    'post_type' => 'grupo',
                    'posts_per_page' => 5,
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'numero_miembros',
                    'order' => 'DESC',
                    'meta_query' => array(
                        array(
                            'key' => 'estado_grupo',
                            'value' => 'Activo',
                            'compare' => '='
                        )
                    )
                ));
                
                if ($grupos_populares->have_posts()) : ?>
                    <div class="popular-groups-list">
                        <?php while ($grupos_populares->have_posts()) : $grupos_populares->the_post();
                            $numero_miembros = get_field('numero_miembros') ?: 0;
                            $categoria = wp_get_post_terms(get_the_ID(), 'categoria_grupo', array('fields' => 'all'));
                            $categoria_principal = !empty($categoria) ? $categoria[0] : null;
                            $categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
                        ?>
                            <div class="popular-group-item">
                                <div class="group-icon" style="background: <?php echo $categoria_style['color']; ?>">
                                    <i class="<?php echo $categoria_style['icon']; ?>"></i>
                                </div>
                                <div class="group-info">
                                    <h4 class="group-name">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    <span class="group-members">
                                        <i class="fas fa-users"></i>
                                        <?php echo format_member_count($numero_miembros); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>

        <!-- Widget de categorías -->
        <div class="widget widget-categories">
            <h3 class="widget-title">
                <i class="fas fa-th-large"></i>
                <?php _e('Categorías', 'telegram-groups'); ?>
            </h3>
            <div class="widget-content">
                <?php
                $categorias = get_terms(array(
                    'taxonomy' => 'categoria_grupo',
                    'hide_empty' => true,
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'number' => 8
                ));
                
                if ($categorias && !is_wp_error($categorias)) : ?>
                    <div class="categories-list">
                        <?php foreach ($categorias as $categoria) :
                            $categoria_style = get_categoria_style($categoria->slug);
                        ?>
                            <a href="<?php echo get_term_link($categoria); ?>" class="category-item">
                                <i class="<?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                                <span class="category-name"><?php echo $categoria->name; ?></span>
                                <span class="category-count"><?php echo $categoria->count; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Widget de publicidad -->
        <div class="widget widget-ad">
            <div class="widget-content">
                <div class="ad-banner">
                    <h4><?php _e('Promociona tu grupo', 'telegram-groups'); ?></h4>
                    <p><?php _e('¿Tienes un grupo de Telegram? ¡Añádelo gratis!', 'telegram-groups'); ?></p>
                    <a href="<?php echo home_url('/añadir-grupo'); ?>" class="btn-ad">
                        <i class="fas fa-plus"></i>
                        <?php _e('Añadir Grupo', 'telegram-groups'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Widget de estadísticas -->
        <div class="widget widget-stats">
            <h3 class="widget-title">
                <i class="fas fa-chart-bar"></i>
                <?php _e('Estadísticas', 'telegram-groups'); ?>
            </h3>
            <div class="widget-content">
                <?php
                $total_grupos = wp_count_posts('grupo')->publish;
                $total_categorias = wp_count_terms('categoria_grupo');
                $total_ciudades = wp_count_terms('ciudad_grupo');
                ?>
                <div class="stats-list">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($total_grupos); ?></span>
                        <span class="stat-label"><?php _e('Grupos', 'telegram-groups'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_categorias; ?></span>
                        <span class="stat-label"><?php _e('Categorías', 'telegram-groups'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_ciudades; ?></span>
                        <span class="stat-label"><?php _e('Ciudades', 'telegram-groups'); ?></span>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<style>
/* Sidebar Styles */
.widget-area {
    padding: 0;
}

.widget {
    background: var(--telegram-white);
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0, 136, 204, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.widget-title {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: var(--telegram-dark);
    padding: 1rem 1.25rem;
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-bottom: 1px solid #e9ecef;
}

.widget-title i {
    color: var(--telegram-blue);
}

.widget-content {
    padding: 1.25rem;
}

/* Widget de búsqueda */
.search-widget-group {
    display: flex;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.3s ease;
}

.search-widget-group:focus-within {
    border-color: var(--telegram-blue);
}

.search-widget-input {
    flex: 1;
    padding: 0.75rem;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.9rem;
}

.search-widget-btn {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: none;
    padding: 0.75rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-widget-btn:hover {
    background: var(--telegram-light-blue);
}

/* Grupos populares */
.popular-groups-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.popular-group-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.popular-group-item:hover {
    background: #f8f9fa;
}

.group-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 0.9rem;
    flex-shrink: 0;
}

.group-info {
    flex: 1;
    min-width: 0;
}

.group-name {
    margin: 0 0 0.25rem 0;
    font-size: 0.85rem;
    font-weight: 600;
}

.group-name a {
    color: var(--telegram-dark);
    text-decoration: none;
}

.group-name a:hover {
    color: var(--telegram-blue);
}

.group-members {
    font-size: 0.75rem;
    color: var(--telegram-gray);
    display: flex;
    align-items: center;
    gap: 3px;
}

/* Categorías */
.categories-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.category-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    text-decoration: none;
    color: var(--telegram-dark);
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.category-item:hover {
    background: #f8f9fa;
    border-color: var(--telegram-blue);
    transform: translateX(3px);
}

.category-name {
    flex: 1;
    font-size: 0.85rem;
    font-weight: 500;
}

.category-count {
    font-size: 0.75rem;
    color: var(--telegram-gray);
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 10px;
}

/* Publicidad */
.ad-banner {
    text-align: center;
    padding: 1rem;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-radius: 8px;
}

.ad-banner h4 {
    margin: 0 0 0.5rem 0;
    color: var(--telegram-blue);
    font-size: 1rem;
}

.ad-banner p {
    margin: 0 0 1rem 0;
    font-size: 0.85rem;
    color: var(--telegram-gray);
}

.btn-ad {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-ad:hover {
    background: var(--telegram-light-blue);
    transform: translateY(-1px);
    color: var(--telegram-white);
}

/* Estadísticas */
.stats-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-number {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--telegram-blue);
    line-height: 1;
}

.stat-label {
    display: block;
    font-size: 0.7rem;
    color: var(--telegram-gray);
    margin-top: 0.25rem;
}

/* Responsive */
@media (max-width: 768px) {
    .widget {
        margin-bottom: 1rem;
    }
    
    .widget-content {
        padding: 1rem;
    }
    
    .stats-list {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
}
</style>
