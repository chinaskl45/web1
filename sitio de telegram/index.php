<?php get_header(); ?>

<?php
// CÓDIGO DE DEBUG - REMOVER DESPUÉS DE ARREGLAR
if (current_user_can('administrator')) {
    echo "<!-- DEBUG INFO:\n";
    echo "Post type 'grupo' exists: " . (post_type_exists('grupo') ? 'YES' : 'NO') . "\n";
    echo "Taxonomy 'categoria_grupo' exists: " . (taxonomy_exists('categoria_grupo') ? 'YES' : 'NO') . "\n";
    echo "Taxonomy 'destacados' exists: " . (taxonomy_exists('destacados') ? 'YES' : 'NO') . "\n";
    echo "Taxonomy 'ciudad_grupo' exists: " . (taxonomy_exists('ciudad_grupo') ? 'YES' : 'NO') . "\n";
    
    $total_grupos = wp_count_posts('grupo');
    echo "Total grupos: " . print_r($total_grupos, true) . "\n";
    echo "-->";
}
?>

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

/* Reset y base */
* {
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: var(--telegram-dark);
    background-color: var(--telegram-bg);
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    padding: 4rem 0;
    text-align: center;
    margin-bottom: 3rem;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.hero-description {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.6;
}

/* Buscador principal */
.hero-search {
    max-width: 600px;
    margin: 0 auto 2rem;
}

.search-form {
    background: var(--telegram-white);
    border-radius: 50px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    display: flex;
}

.search-input-group {
    display: flex;
    width: 100%;
}

.search-input {
    flex: 1;
    padding: 1.25rem 1.5rem;
    border: none;
    outline: none;
    font-size: 1.1rem;
    color: var(--telegram-dark);
}

.search-button {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: none;
    padding: 1.25rem 2rem;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
}

.search-button:hover {
    background: var(--telegram-light-blue);
}

/* Estadísticas hero */
.hero-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    display: block;
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 0.25rem;
}

/* Layout principal */
.home-content {
    background: var(--telegram-white);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
    margin-bottom: 2rem;
}

/* Secciones */
.home-section {
    margin-bottom: 4rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid var(--telegram-blue);
}

.section-title {
    font-size: 2rem;
    color: var(--telegram-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
}

.section-title i {
    color: var(--telegram-blue);
    font-size: 1.5rem;
}

.section-link {
    color: var(--telegram-blue);
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    border: 2px solid var(--telegram-blue);
    background: transparent;
}

.section-link:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    transform: translateY(-2px);
}

/* 1. Fichas de Destacados */
.destacados-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.ficha-destacado {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    border: 2px solid #f39c12;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.ficha-destacado::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f39c12, #e67e22);
}

.ficha-destacado:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(243, 156, 18, 0.3);
}

.ficha-destacado .destacado-icon {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
}

.ficha-destacado h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: #d35400;
}

.ficha-destacado h3 a {
    color: #d35400;
    text-decoration: none;
}

.ficha-destacado h3 a:hover {
    color: #e67e22;
}

.ficha-destacado p {
    margin: 0;
    color: #8e44ad;
    font-size: 0.9rem;
    line-height: 1.4;
}

/* 2. Fichas de Tipos de Grupo */
.tipos-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.ficha-tipo {
    background: var(--telegram-white);
    border-radius: 12px;
    padding: 1.5rem 1rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 136, 204, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.ficha-tipo:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 136, 204, 0.2);
    border-color: var(--telegram-blue);
}

.ficha-tipo .tipo-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}

.ficha-tipo h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--telegram-dark);
}

.ficha-tipo h4 a {
    color: var(--telegram-dark);
    text-decoration: none;
}

.ficha-tipo h4 a:hover {
    color: var(--telegram-blue);
}

.ficha-tipo .tipo-count {
    font-size: 0.85rem;
    color: var(--telegram-gray);
    margin: 0;
}

/* 3. Todos los Grupos (16 grupos) */
.todos-grupos-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.ficha-grupo {
    background: var(--telegram-white);
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0, 136, 204, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.ficha-grupo:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 136, 204, 0.2);
}

.ficha-grupo .grupo-imagen {
    height: 120px;
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    position: relative;
}

.ficha-grupo .grupo-content {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.ficha-grupo h3 {
    margin: 0 0 0.75rem 0;
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.3;
}

.ficha-grupo h3 a {
    color: var(--telegram-dark);
    text-decoration: none;
}

.ficha-grupo h3 a:hover {
    color: var(--telegram-blue);
}

.ficha-grupo p {
    margin: 0;
    color: var(--telegram-gray);
    font-size: 0.9rem;
    line-height: 1.4;
    flex: 1;
}

/* 4. Ciudades en 2 columnas */
.ciudades-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.ciudades-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.ciudad-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--telegram-white);
    border-radius: 8px;
    text-decoration: none;
    color: var(--telegram-dark);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.ciudad-link:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    transform: translateX(5px);
    text-decoration: none;
}

.ciudad-link i {
    color: var(--telegram-blue);
    font-size: 1.2rem;
    width: 20px;
    text-align: center;
}

.ciudad-link:hover i {
    color: var(--telegram-white);
}

.ciudad-nombre {
    flex: 1;
    font-weight: 600;
    font-size: 1rem;
}

.ciudad-count {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Sin contenido */
.no-content {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--telegram-gray);
    font-style: italic;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px dashed #e9ecef;
}

.no-content i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
    display: block;
}

/* Responsive */
@media (max-width: 1024px) {
    .destacados-grid,
    .todos-grupos-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .tipos-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .destacados-grid,
    .todos-grupos-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .tipos-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .ciudades-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .home-content {
        padding: 1.5rem;
    }
    
    .search-form {
        flex-direction: column;
        border-radius: 10px;
    }
    
    .search-button {
        border-radius: 0 0 10px 10px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .hero-section {
        padding: 2rem 0;
    }
    
    .hero-title {
        font-size: 1.5rem;
    }
    
    .destacados-grid,
    .todos-grupos-grid,
    .tipos-grid {
        grid-template-columns: 1fr;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.home-section {
    animation: fadeInUp 0.6s ease-out;
}

.ficha-destacado,
.ficha-tipo,
.ficha-grupo,
.ciudad-link {
    animation: fadeInUp 0.6s ease-out;
}
</style>

<main id="primary" class="site-main">
    
    <!-- Hero Section con buscador -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <?php echo get_theme_mod('hero_title', __('La Mayor Comunidad de Grupos de Telegram', 'telegram-groups')); ?>
                </h1>
                <p class="hero-description">
                    <?php echo get_theme_mod('hero_description', __('Bienvenido a la mayor comunidad de Grupos de Telegram organizados por aficiones, temáticas, categorías e intereses.', 'telegram-groups')); ?>
                </p>
                
                <!-- Buscador principal -->
                <div class="hero-search">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="search-input-group">
                            <input type="search" 
                                   name="s" 
                                   placeholder="<?php _e('Buscar grupos, categorías o contenido...', 'telegram-groups'); ?>"
                                   value="<?php echo get_search_query(); ?>"
                                   class="search-input"
                                   required>
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Estadísticas rápidas mejoradas -->
                <div class="hero-stats">
                    <?php
                    $total_grupos = 0;
                    $total_categorias = 0;
                    $total_ciudades = 0;
                    
                    // Contar grupos de forma segura
                    if (post_type_exists('grupo')) {
                        $count_posts = wp_count_posts('grupo');
                        $total_grupos = isset($count_posts->publish) ? $count_posts->publish : 0;
                    }
                    
                    // Contar categorías de forma segura
                    if (taxonomy_exists('categoria_grupo')) {
                        $total_categorias = wp_count_terms(array(
                            'taxonomy' => 'categoria_grupo',
                            'hide_empty' => false
                        ));
                        if (is_wp_error($total_categorias)) $total_categorias = 0;
                    }
                    
                    // Contar ciudades de forma segura
                    if (taxonomy_exists('ciudad_grupo')) {
                        $total_ciudades = wp_count_terms(array(
                            'taxonomy' => 'ciudad_grupo',
                            'hide_empty' => false
                        ));
                        if (is_wp_error($total_ciudades)) $total_ciudades = 0;
                    }
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($total_grupos); ?></span>
                        <span class="stat-label"><?php _e('Grupos Activos', 'telegram-groups'); ?></span>
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
    </section>

    <div class="container">
        <div class="home-content">
            
            <!-- 1. FICHAS DE DESTACADOS MEJORADO -->
            <section class="home-section grupos-destacados">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-star"></i>
                        <?php _e('Grupos Destacados', 'telegram-groups'); ?>
                    </h2>
                    

                    <?php 
$destacado_term = get_term_by('slug', 'destacado', 'destacados');
$destacado_link = $destacado_term ? get_term_link($destacado_term) : '#';
if (is_wp_error($destacado_link)) {
    $destacado_link = '#';
}
?>
<a href="<?php echo $destacado_link; ?>" class="section-link">


                        <?php _e('Ver todos los destacados', 'telegram-groups'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="destacados-grid">
                    <?php
                    // Primero intentar con taxonomía 'destacados'
                    $grupos_destacados = new WP_Query(array(
                        'post_type' => 'grupo',
                        'posts_per_page' => 8,
                        'post_status' => 'publish',
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
                    
                    // Si no hay resultados, usar meta_key como alternativa
                    if (!$grupos_destacados->have_posts()) {
                        wp_reset_postdata();
                        $grupos_destacados = new WP_Query(array(
                            'post_type' => 'grupo',
                            'posts_per_page' => 8,
                            'post_status' => 'publish',
                            'meta_query' => array(
                                array(
                                    'key' => 'destacado',
                                    'value' => '1',
                                    'compare' => '='
                                ),
                                array(
                                    'key' => 'estado_grupo',
                                    'value' => 'Activo',
                                    'compare' => '='
                                )
                            )
                        ));
                    }
                    
                    // Si aún no hay resultados, mostrar grupos recientes
                    if (!$grupos_destacados->have_posts()) {
                        wp_reset_postdata();
                        $grupos_destacados = new WP_Query(array(
                            'post_type' => 'grupo',
                            'posts_per_page' => 8,
                            'post_status' => 'publish',
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));
                    }
                    
                    if ($grupos_destacados->have_posts()) :
                        while ($grupos_destacados->have_posts()) : $grupos_destacados->the_post();
                            $categoria = wp_get_post_terms(get_the_ID(), 'categoria_grupo', array('fields' => 'all'));
                            $categoria_principal = !empty($categoria) ? $categoria[0] : null;
                            $categoria_style = $categoria_principal && function_exists('get_categoria_style') ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
                    ?>
                        <article class="ficha-destacado">
                            <div class="destacado-icon">
                                <i class="<?php echo $categoria_style['icon']; ?>"></i>
                            </div>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php if (get_the_excerpt()) : ?>
                                <p><?php echo wp_trim_words(get_the_excerpt(), 12, '...'); ?></p>
                            <?php else : ?>
                                <p><?php echo wp_trim_words(get_the_content(), 12, '...'); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                        <div class="no-content">
                            <i class="fas fa-star"></i>
                            <p><?php _e('No hay grupos destacados disponibles aún.', 'telegram-groups'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 2. FICHAS DE TIPOS DE GRUPO MEJORADO -->
            <section class="home-section tipos-grupo">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-th-large"></i>
                        <?php _e('Tipos de Grupos', 'telegram-groups'); ?>
                    </h2>
                    <a href="<?php echo home_url('/categorias'); ?>" class="section-link">
                        <?php _e('Ver todas las categorías', 'telegram-groups'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="tipos-grid">
                    <?php
                    $categorias = array();
                    
                    if (taxonomy_exists('categoria_grupo')) {
                        $categorias = get_terms(array(
                            'taxonomy' => 'categoria_grupo',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 12
                        ));
                    }
                    
                    if ($categorias && !is_wp_error($categorias)) :
                        foreach ($categorias as $categoria) :
                            $categoria_style = function_exists('get_categoria_style') ? get_categoria_style($categoria->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
                    ?>
                        <div class="ficha-tipo">
                            <i class="tipo-icon <?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                            <h4><a href="<?php echo get_term_link($categoria); ?>"><?php echo $categoria->name; ?></a></h4>
                            <p class="tipo-count"><?php echo $categoria->count; ?> grupos</p>
                        </div>
                    <?php endforeach; else : ?>
                        <div class="no-content">
                            <i class="fas fa-th-large"></i>
                            <p><?php _e('No hay categorías disponibles.', 'telegram-groups'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 3. TODOS LOS GRUPOS MEJORADO -->
            <section class="home-section todos-grupos">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-users"></i>
                        <?php _e('Todos los Grupos', 'telegram-groups'); ?>
                    </h2>
                    <a href="<?php echo get_post_type_archive_link('grupo'); ?>" class="section-link">
                        <?php _e('Ver todos los grupos', 'telegram-groups'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="todos-grupos-grid">
                    <?php
                    $todos_grupos = new WP_Query(array(
                        'post_type' => 'grupo',
                        'posts_per_page' => 16,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                        'meta_query' => array(
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
                        )
                    ));
                    
                    if ($todos_grupos->have_posts()) :
                        while ($todos_grupos->have_posts()) : $todos_grupos->the_post();
                            $categoria = wp_get_post_terms(get_the_ID(), 'categoria_grupo', array('fields' => 'all'));
                            $categoria_principal = !empty($categoria) ? $categoria[0] : null;
                            $categoria_style = $categoria_principal && function_exists('get_categoria_style') ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
                    ?>
                        <article class="ficha-grupo">
                            <div class="grupo-imagen" style="background: <?php echo $categoria_style['color']; ?>">
                                <i class="<?php echo $categoria_style['icon']; ?>"></i>
                            </div>
                            <div class="grupo-content">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <?php if (get_the_excerpt()) : ?>
                                    <p><?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?></p>
                                <?php else : ?>
                                    <p><?php echo wp_trim_words(get_the_content(), 15, '...'); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                        <div class="no-content">
                            <i class="fas fa-users"></i>
                            <p><?php _e('No hay grupos disponibles aún.', 'telegram-groups'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 4. CIUDADES EN 2 COLUMNAS MEJORADO -->
            <section class="home-section ciudades-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php _e('Explorar por Ciudades', 'telegram-groups'); ?>
                    </h2>
                    <a href="<?php echo home_url('/ciudades'); ?>" class="section-link">
                        <?php _e('Ver todas las ciudades', 'telegram-groups'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div
                                 
                <div class="ciudades-grid">
                    <?php
                    $ciudades = array();
                    
                    if (taxonomy_exists('ciudad_grupo')) {
                        $ciudades = get_terms(array(
                            'taxonomy' => 'ciudad_grupo',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 20
                        ));
                    }
                    
                    if ($ciudades && !is_wp_error($ciudades)) :
                        foreach ($ciudades as $ciudad) :
                    ?>
                        <a href="<?php echo get_term_link($ciudad); ?>" class="ciudad-link">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="ciudad-nombre"><?php echo $ciudad->name; ?></span>
                            <span class="ciudad-count">(<?php echo $ciudad->count; ?>)</span>
                        </a>
                    <?php endforeach; else : ?>
                        <div class="no-content">
                            <i class="fas fa-map-marker-alt"></i>
                            <p><?php _e('No hay ciudades disponibles.', 'telegram-groups'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            
        </div>
    </div>
    
</main>

<?php get_footer(); ?>