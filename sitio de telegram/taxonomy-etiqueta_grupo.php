<?php get_header(); ?>

<style>
/* Variables CSS base */
:root {
    --telegram-blue: #0088cc;
    --telegram-light-blue: #229ED9;
    --telegram-bg: #f4f4f5;
    --telegram-white: #ffffff;
    --telegram-dark: #2c3e50;
    --telegram-gray: #95a5a6;
}

/* Reset y estilos base */
* { box-sizing: border-box; }

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

/* Header de etiqueta */
.etiqueta-header {
    background: linear-gradient(135deg, #8e44ad, #9b59b6);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    text-align: center;
    border-radius: 0 0 20px 20px;
}

.etiqueta-header-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.etiqueta-icon {
    font-size: 4rem;
    color: white;
}

.etiqueta-info {
    text-align: left;
}

.etiqueta-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    color: white;
    font-family: 'Courier New', monospace;
}

.etiqueta-description {
    font-size: 1.2rem;
    margin: 0 0 1.5rem 0;
    color: white;
    opacity: 0.9;
}

.etiqueta-stats {
    display: flex;
    gap: 2rem;
}

.etiqueta-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
}

.etiqueta-stats strong {
    font-size: 1.2rem;
    font-weight: 700;
}

/* Breadcrumbs */
.etiqueta-breadcrumbs {
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
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.breadcrumb-item a:hover {
    background: rgba(0, 136, 204, 0.1);
}

.breadcrumb-item.active {
    color: var(--telegram-gray);
    font-weight: 600;
    font-family: 'Courier New', monospace;
}

/* Layout principal */
.etiqueta-content-wrapper {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
}

/* Sidebar */
.etiqueta-filters {
    position: sticky;
    top: 100px;
}

.filters-sticky {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
    border: 1px solid #e9ecef;
}

.filters-title {
    margin: 0 0 1.5rem 0;
    color: var(--telegram-dark);
    font-size: 1.2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #8e44ad;
}

.filters-title i {
    color: #8e44ad;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--telegram-dark);
    font-size: 0.9rem;
}

.filter-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
    background: white;
}

.filter-group select:focus {
    outline: none;
    border-color: #8e44ad;
}

.btn-filter-apply {
    width: 100%;
    background: linear-gradient(135deg, #8e44ad, #9b59b6);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-filter-apply:hover {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
}

/* Etiquetas relacionadas */
.etiquetas-relacionadas,
.top-grupos-etiqueta {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.etiquetas-relacionadas h4,
.top-grupos-etiqueta h4 {
    margin: 0 0 1rem 0;
    color: var(--telegram-dark);
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.etiquetas-relacionadas h4 i,
.top-grupos-etiqueta h4 i {
    color: #8e44ad;
}

.etiquetas-relacionadas-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.etiqueta-relacionada-tag {
    background: linear-gradient(135deg, #8e44ad, #9b59b6);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    font-family: 'Courier New', monospace;
    transition: all 0.3s ease;
}

.etiqueta-relacionada-tag:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(142, 68, 173, 0.3);
    color: white;
    text-decoration: none;
}

/* Contenido principal */
.etiqueta-main-content {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 25px rgba(0, 136, 204, 0.1);
    border: 1px solid #e9ecef;
    min-height: 400px;
}

.etiqueta-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.results-info {
    font-weight: 600;
    color: var(--telegram-dark);
}

.view-toggle {
    display: flex;
    gap: 0.25rem;
    background: white;
    padding: 0.25rem;
    border-radius: 8px;
}

.view-toggle-btn {
    padding: 0.5rem 0.75rem;
    border: none;
    background: transparent;
    color: var(--telegram-gray);
    border-radius: 6px;
    cursor: pointer;
}

.view-toggle-btn.active,
.view-toggle-btn:hover {
    background: #8e44ad;
    color: white;
}

/* Sin resultados */
.no-etiqueta-results {
    text-align: center;
    padding: 4rem 2rem;
}

.no-etiqueta-results .no-results-icon {
    font-size: 5rem;
    color: #8e44ad;
    opacity: 0.3;
    margin-bottom: 2rem;
}

.no-etiqueta-results h3 {
    color: var(--telegram-dark);
    font-size: 2rem;
    margin: 0 0 1rem 0;
}

.no-etiqueta-results p {
    color: var(--telegram-gray);
    font-size: 1.1rem;
    margin: 0 0 3rem 0;
}

.no-results-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-primary {
    background: linear-gradient(135deg, #8e44ad, #9b59b6);
    color: white;
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

.btn-secondary {
    background: white;
    color: #8e44ad;
    border: 2px solid #8e44ad;
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: #8e44ad;
    color: white;
    text-decoration: none;
}

/* Responsive */
@media (max-width: 1024px) {
    .etiqueta-content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .etiqueta-filters {
        position: static;
        order: 2;
    }
    
    .etiqueta-main-content {
        order: 1;
    }
}

@media (max-width: 768px) {
    .etiqueta-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .etiqueta-title {
        font-size: 2rem;
    }
    
    .etiqueta-stats {
        justify-content: center;
    }
    
    .container {
        padding: 0 15px;
    }
    
    .etiqueta-toolbar {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<?php
// Obtener término actual
$current_term = get_queried_object();

// Obtener filtros
$current_categoria = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
$current_ciudad = isset($_GET['ciudad']) ? sanitize_text_field($_GET['ciudad']) : '';
$current_orden = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : 'relevancia';
?>

<main id="primary" class="site-main taxonomy-etiqueta-page">
    <div class="container">
        
        <!-- Header de etiqueta -->
        <header class="etiqueta-header">
            <div class="etiqueta-header-content">
                <div class="etiqueta-icon">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div class="etiqueta-info">
                    <h1 class="etiqueta-title">#<?php echo $current_term->name; ?></h1>
                    <p class="etiqueta-description">
                        <?php 
                        if ($current_term->description) {
                            echo esc_html($current_term->description);
                        } else {
                            printf(__('Todos los grupos relacionados con %s. Encuentra la comunidad perfecta para tus intereses.', 'telegram-groups'), $current_term->name);
                        }
                        ?>
                    </p>
                    <div class="etiqueta-stats">
                        <span class="stat-item">
                            <i class="fas fa-users"></i>
                            <strong><?php echo $current_term->count; ?></strong>
                            <?php _e('grupos', 'telegram-groups'); ?>
                        </span>
                        <span class="stat-item">
                            <i class="fas fa-check-circle"></i>
                            <strong><?php echo $current_term->count; ?></strong>
                            <?php _e('activos', 'telegram-groups'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Breadcrumbs -->
        <nav class="etiqueta-breadcrumbs" aria-label="<?php _e('Navegación', 'telegram-groups'); ?>">
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
                    <a href="<?php echo home_url('/etiquetas/'); ?>">
                        <?php _e('Etiquetas', 'telegram-groups'); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    #<?php echo $current_term->name; ?>
                </li>
            </ol>
        </nav>

        <div class="etiqueta-content-wrapper">
            
            <!-- Filtros laterales -->
            <aside class="etiqueta-filters">
                <div class="filters-sticky">
                    <h3 class="filters-title">
                        <i class="fas fa-filter"></i>
                        <?php _e('Refinar Búsqueda', 'telegram-groups'); ?>
                    </h3>
                    
                    <form class="filters-form" method="get">
                        <input type="hidden" name="etiqueta" value="<?php echo $current_term->slug; ?>">
                        
                        <div class="filter-group">
                            <label for="categoria-filter">
                                <i class="fas fa-th-large"></i>
                                <?php _e('Categoría', 'telegram-groups'); ?>
                            </label>
                            <select name="categoria" id="categoria-filter">
                                <option value=""><?php _e('Todas las categorías', 'telegram-groups'); ?></option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="ciudad-filter">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php _e('Ciudad', 'telegram-groups'); ?>
                            </label>
                            <select name="ciudad" id="ciudad-filter">
                                <option value=""><?php _e('Todas las ciudades', 'telegram-groups'); ?></option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-search"></i>
                            <?php _e('Aplicar Filtros', 'telegram-groups'); ?>
                        </button>
                    </form>
                    
                    <!-- Etiquetas relacionadas -->
                    <div class="etiquetas-relacionadas">
                        <h4>
                            <i class="fas fa-tags"></i>
                            <?php _e('Etiquetas Relacionadas', 'telegram-groups'); ?>
                        </h4>
                        <div class="etiquetas-relacionadas-list">
                            <a href="#" class="etiqueta-relacionada-tag">#música</a>
                            <a href="#" class="etiqueta-relacionada-tag">#romance</a>
                            <a href="#" class="etiqueta-relacionada-tag">#pareja</a>
                        </div>
                    </div>
                    
                </div>
            </aside>
            
            <!-- Contenido principal -->
            <div class="etiqueta-main-content">
                
                <!-- Toolbar -->
                <div class="etiqueta-toolbar">
                    <div class="results-info">
                        <?php
                        global $wp_query;
                        $total_found = $wp_query->found_posts;
                        
                        if ($total_found > 0) :
                            printf(
                                _n(
                                    'Mostrando %d grupo con #%s',
                                    'Mostrando %d grupos con #%s',
                                    $total_found,
                                    'telegram-groups'
                                ),
                                $total_found,
                                $current_term->name
                            );
                        else :
                            printf(__('No se encontraron grupos con #%s', 'telegram-groups'), $current_term->name);
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
                <div class="etiqueta-results">
                    
                    <?php if (have_posts()) : ?>
                        
                        <div class="grupos-grid grid-view active">
                            <?php while (have_posts()) : the_post(); ?>
                                <div class="grupo-card">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p><?php the_excerpt(); ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                    <?php else : ?>
                        
                        <!-- Sin resultados -->
                        <div class="no-etiqueta-results">
                            <div class="no-results-icon">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <h3><?php printf(__('No hay grupos con #%s', 'telegram-groups'), $current_term->name); ?></h3>
                            <p><?php _e('Aún no se han etiquetado grupos con este término, pero pronto habrá contenido disponible.', 'telegram-groups'); ?></p>
                            
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
        
    </div>
</main>

<?php get_footer(); ?>