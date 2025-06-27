<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Telegram_Groups
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="error-404-wrapper">
            
            <!-- Contenido principal del error -->
            <div class="error-404-content">
                
                <!-- Número 404 grande -->
                <div class="error-number">
                    <h1>404</h1>
                    <div class="error-number-bg">404</div>
                </div>
                
                <!-- Mensaje de error -->
                <div class="error-message">
                    <h2><?php _e('¡Oops! Página no encontrada', 'telegram-groups'); ?></h2>
                    <p><?php _e('La página que estás buscando no existe o ha sido movida. Pero no te preocupes, ¡tenemos muchas otras cosas interesantes!', 'telegram-groups'); ?></p>
                </div>
                
                <!-- Buscador en la página 404 -->
                <div class="error-search">
                    <h3><?php _e('¿Qué estabas buscando?', 'telegram-groups'); ?></h3>
                    <form role="search" method="get" class="error-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="error-search-group">
                            <input type="search" 
                                   name="s" 
                                   placeholder="<?php _e('Buscar grupos, categorías...', 'telegram-groups'); ?>"
                                   class="error-search-input"
                                   required>
                            <button type="submit" class="error-search-btn">
                                <i class="fas fa-search"></i>
                                <?php _e('Buscar', 'telegram-groups'); ?>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Acciones rápidas -->
                <div class="error-actions">
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        <?php _e('Ir al Inicio', 'telegram-groups'); ?>
                    </a>
                    <a href="<?php echo get_post_type_archive_link('grupo'); ?>" class="btn btn-secondary">
                        <i class="fas fa-users"></i>
                        <?php _e('Ver Grupos', 'telegram-groups'); ?>
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        <?php _e('Volver Atrás', 'telegram-groups'); ?>
                    </a>
                </div>
                
            </div>
            
            <!-- Sidebar con sugerencias -->
            <div class="error-404-sidebar">
                
                <!-- Grupos populares -->
                <div class="error-suggestion-box">
                    <h3>
                        <i class="fas fa-fire"></i>
                        <?php _e('Grupos Populares', 'telegram-groups'); ?>
                    </h3>
                    <div class="error-grupos-list">
                        <?php
                        $grupos_populares = new WP_Query(array(
                            'post_type' => 'grupo',
                            'posts_per_page' => 6,
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
                        
                        if ($grupos_populares->have_posts()) :
                            while ($grupos_populares->have_posts()) : $grupos_populares->the_post();
                                $numero_miembros = get_field('numero_miembros') ?: 0;
                                $categoria = wp_get_post_terms(get_the_ID(), 'categoria_grupo', array('fields' => 'all'));
                                $categoria_principal = !empty($categoria) ? $categoria[0] : null;
                                $categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
                        ?>
                            <div class="error-grupo-item">
                                <div class="error-grupo-icon" style="background: <?php echo $categoria_style['color']; ?>">
                                    <i class="<?php echo $categoria_style['icon']; ?>"></i>
                                </div>
                                <div class="error-grupo-info">
                                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <span class="error-grupo-members">
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
                
                <!-- Categorías principales -->
                <div class="error-suggestion-box">
                    <h3>
                        <i class="fas fa-th-large"></i>
                        <?php _e('Categorías Principales', 'telegram-groups'); ?>
                    </h3>
                    <div class="error-categories-list">
                        <?php
                        $categorias_principales = get_terms(array(
                            'taxonomy' => 'categoria_grupo',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 8
                        ));
                        
                        if ($categorias_principales && !is_wp_error($categorias_principales)) :
                            foreach ($categorias_principales as $categoria) :
                                $categoria_style = get_categoria_style($categoria->slug);
                        ?>
                            <a href="<?php echo get_term_link($categoria); ?>" class="error-category-link">
                                <i class="<?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                                <span><?php echo $categoria->name; ?></span>
                                <small>(<?php echo $categoria->count; ?>)</small>
                            </a>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
                
                <!-- Información de contacto -->
                <div class="error-suggestion-box error-help-box">
                    <h3>
                        <i class="fas fa-question-circle"></i>
                        <?php _e('¿Necesitas Ayuda?', 'telegram-groups'); ?>
                    </h3>
                    <p><?php _e('Si crees que esto es un error o necesitas ayuda, no dudes en contactarnos.', 'telegram-groups'); ?></p>
                    <div class="error-help-actions">
                        <a href="<?php echo home_url('/contacto'); ?>" class="btn-help">
                            <i class="fas fa-envelope"></i>
                            <?php _e('Contactar', 'telegram-groups'); ?>
                        </a>
                        <a href="<?php echo home_url('/añadir-grupo'); ?>" class="btn-help">
                            <i class="fas fa-plus"></i>
                            <?php _e('Añadir Grupo', 'telegram-groups'); ?>
                        </a>
                    </div>
                </div>
                
            </div>
            
        </div>
        
        <!-- Estadísticas del sitio -->
        <div class="error-stats-section">
            <h3><?php _e('Mientras tanto, mira estas estadísticas', 'telegram-groups'); ?></h3>
            <div class="error-stats-grid">
                <?php
                $total_grupos = wp_count_posts('grupo')->publish;
                $total_categorias = wp_count_terms('categoria_grupo');
                $total_ciudades = wp_count_terms('ciudad_grupo');
                
                // Calcular total aproximado de miembros
                $grupos_muestra = get_posts(array(
                    'post_type' => 'grupo',
                    'numberposts' => 50,
                    'meta_key' => 'numero_miembros',
                    'fields' => 'ids'
                ));
                
                $total_miembros_muestra = 0;
                foreach ($grupos_muestra as $grupo_id) {
                    $miembros = get_field('numero_miembros', $grupo_id);
                    $total_miembros_muestra += intval($miembros);
                }
                
                $promedio_miembros = count($grupos_muestra) > 0 ? $total_miembros_muestra / count($grupos_muestra) : 0;
                $total_miembros_estimado = $total_grupos * $promedio_miembros;
                ?>
                
                <div class="error-stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo number_format($total_grupos); ?></span>
                        <span class="stat-label"><?php _e('Grupos Activos', 'telegram-groups'); ?></span>
                    </div>
                </div>
                
                <div class="error-stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo format_member_count($total_miembros_estimado); ?></span>
                        <span class="stat-label"><?php _e('Miembros Totales', 'telegram-groups'); ?></span>
                    </div>
                </div>
                
                <div class="error-stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $total_categorias; ?></span>
                        <span class="stat-label"><?php _e('Categorías', 'telegram-groups'); ?></span>
                    </div>
                </div>
                
                <div class="error-stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $total_ciudades; ?></span>
                        <span class="stat-label"><?php _e('Ciudades', 'telegram-groups'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<style>
/* 404 Page Styles */
.error-404-wrapper {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 3rem;
    margin: 2rem 0;
    min-height: 60vh;
}

/* Contenido principal */
.error-404-content {
    text-align: center;
    padding: 2rem;
}

/* Número 404 grande */
.error-number {
    position: relative;
    margin-bottom: 2rem;
}

.error-number h1 {
    font-size: 8rem;
    font-weight: 900;
    color: var(--telegram-blue);
    margin: 0;
    position: relative;
    z-index: 2;
    text-shadow: 0 4px 15px rgba(0, 136, 204, 0.3);
}

.error-number-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12rem;
    font-weight: 900;
    color: rgba(0, 136, 204, 0.1);
    z-index: 1;
    pointer-events: none;
}

/* Mensaje de error */
.error-message {
    margin-bottom: 3rem;
}

.error-message h2 {
    font-size: 2.5rem;
    color: var(--telegram-dark);
    margin: 0 0 1rem 0;
    font-weight: 700;
}

.error-message p {
    font-size: 1.1rem;
    color: var(--telegram-gray);
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

/* Buscador en 404 */
.error-search {
    background: var(--telegram-white);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
    margin-bottom: 3rem;
}

.error-search h3 {
    margin: 0 0 1.5rem 0;
    color: var(--telegram-dark);
    font-size: 1.3rem;
}

.error-search-group {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
    background: #f8f9fa;
    border-radius: 50px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.error-search-input {
    flex: 1;
    padding: 1rem 1.5rem;
    border: none;
    outline: none;
    background: transparent;
    font-size: 1rem;
    color: var(--telegram-dark);
}

.error-search-btn {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    border: none;
    padding: 1rem 2rem;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.error-search-btn:hover {
    transform: translateX(-2px);
    box-shadow: -2px 0 10px rgba(0, 136, 204, 0.3);
}

/* Acciones de error */
.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.error-actions .btn {
    padding: 1rem 2rem;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 136, 204, 0.3);
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

.btn-outline {
    background: transparent;
    color: var(--telegram-gray);
    border: 2px solid var(--telegram-gray);
}

.btn-outline:hover {
    background: var(--telegram-gray);
    color: var(--telegram-white);
}

/* Sidebar de sugerencias */
.error-404-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.error-suggestion-box {
    background: var(--telegram-white);
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
}

.error-suggestion-box h3 {
    margin: 0 0 1.5rem 0;
    color: var(--telegram-dark);
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--telegram-blue);
}

.error-suggestion-box h3 i {
    color: var(--telegram-blue);
}

/* Lista de grupos en error */
.error-grupos-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.error-grupo-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.error-grupo-item:hover {
    background: #f8f9fa;
}

.error-grupo-icon {
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

.error-grupo-info {
    flex: 1;
    min-width: 0;
}

.error-grupo-info h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.error-grupo-info h4 a {
    color: var(--telegram-dark);
    text-decoration: none;
}

.error-grupo-info h4 a:hover {
    color: var(--telegram-blue);
}

.error-grupo-members {
    font-size: 0.8rem;
    color: var(--telegram-gray);
    display: flex;
    align-items: center;
    gap: 3px;
}

/* Lista de categorías en error */
.error-categories-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.error-category-link {
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

.error-category-link:hover {
    background: #f8f9fa;
    border-color: var(--telegram-blue);
    transform: translateX(3px);
}

.error-category-link span {
    flex: 1;
    font-weight: 500;
    font-size: 0.9rem;
}

.error-category-link small {
    color: var(--telegram-gray);
    font-size: 0.8rem;
}

/* Caja de ayuda */
.error-help-box {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border: 1px solid rgba(0, 136, 204, 0.2);
}

.error-help-box p {
    color: var(--telegram-dark);
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0 0 1.5rem 0;
}

.error-help-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-help {
    flex: 1;
    background: var(--telegram-white);
    color: var(--telegram-blue);
    padding: 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: 1px solid var(--telegram-blue);
}

.btn-help:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
}

/* Sección de estadísticas */
.error-stats-section {
    margin-top: 3rem;
    text-align: center;
}

.error-stats-section h3 {
    color: var(--telegram-dark);
    font-size: 1.5rem;
    margin: 0 0 2rem 0;
}

.error-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.error-stat-item {
    background: var(--telegram-white);
    padding: 2rem 1rem;
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.error-stat-item:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
}

.stat-content .stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: var(--telegram-blue);
    line-height: 1;
}

.stat-content .stat-label {
    display: block;
    font-size: 0.9rem;
    color: var(--telegram-gray);
    margin-top: 0.5rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .error-404-wrapper {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .error-number h1 {
        font-size: 6rem;
    }
    
    .error-number-bg {
        font-size: 8rem;
    }
}

@media (max-width: 768px) {
    .error-404-content {
        padding: 1rem;
    }
    
    .error-message h2 {
        font-size: 2rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .error-actions .btn {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .error-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .error-search-group {
        flex-direction: column;
        border-radius: 10px;
    }
    
    .error-search-btn {
        border-radius: 0 0 10px 10px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .error-number h1 {
        font-size: 4rem;
    }
    
    .error-number-bg {
        font-size: 6rem;
    }
    
    .error-message h2 {
        font-size: 1.5rem;
    }
    
    .error-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .error-help-actions {
        flex-direction: column;
    }
    
    .error-search {
        padding: 1.5rem;
    }
}
</style>

<?php get_footer(); ?>
