<?php
/**
 * Template Part: Hero Section
 * 
 * Componente reutilizable para secciones hero del tema Grupos Telegram
 * 
 * @package GruposTelegram
 * @version 1.0.0
 * 
 * Parámetros disponibles:
 * @param string $hero_title         Título principal
 * @param string $hero_subtitle      Subtítulo opcional
 * @param string $hero_description   Descripción del hero
 * @param bool   $show_search        Mostrar buscador (default: true)
 * @param bool   $show_stats         Mostrar estadísticas (default: true)
 * @param array  $hero_buttons       Array de botones personalizados
 * @param string $hero_style         Estilo del hero (default, minimal, full)
 * @param string $background_image   Imagen de fondo opcional
 */

// Establecer valores por defecto para los parámetros
$hero_title = $hero_title ?? 'La Mayor Comunidad de Grupos de Telegram';
$hero_subtitle = $hero_subtitle ?? '';
$hero_description = $hero_description ?? 'Bienvenido a la mayor comunidad de <strong>Grupos de Telegram</strong> organizados por aficiones, temáticas, categorías e intereses.';
$show_search = $show_search ?? true;
$show_stats = $show_stats ?? true;
$hero_buttons = $hero_buttons ?? array();
$hero_style = $hero_style ?? 'default';
$background_image = $background_image ?? '';

// Obtener estadísticas si se van a mostrar
if ($show_stats) {
    $total_grupos = wp_count_posts('grupo')->publish;
    $total_categorias = wp_count_terms('categoria_grupo');
    $total_miembros = obtener_total_miembros();
    $grupos_activos = obtener_grupos_activos();
}

// Generar clases CSS dinámicas
$hero_classes = [
    'hero-section',
    'telegram-gradient',
    'hero-style-' . $hero_style
];

if ($background_image) {
    $hero_classes[] = 'hero-with-bg';
}

$hero_class_string = implode(' ', $hero_classes);
?>

<section class="<?php echo esc_attr($hero_class_string); ?>" 
         <?php if ($background_image): ?>style="background-image: linear-gradient(135deg, rgba(0,136,204,0.8), rgba(34,158,217,0.8)), url('<?php echo esc_url($background_image); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
    
    <!-- Overlay para mejor legibilidad -->
    <div class="hero-overlay"></div>
    
    <div class="container hero-container">
        <div class="hero-content">
            
            <!-- Título Principal -->
            <?php if ($hero_subtitle): ?>
                <div class="hero-subtitle">
                    <?php echo wp_kses_post($hero_subtitle); ?>
                </div>
            <?php endif; ?>
            
            <h1 class="hero-title">
                <?php echo wp_kses_post($hero_title); ?>
            </h1>
            
            <?php if ($hero_description): ?>
                <p class="hero-description">
                    <?php echo wp_kses_post($hero_description); ?>
                </p>
            <?php endif; ?>
            
            <!-- Buscador Principal -->
            <?php if ($show_search): ?>
                <div class="hero-search">
                    <form class="search-form hero-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="search-input-wrapper">
                            <input type="search" 
                                   id="hero-search" 
                                   name="s" 
                                   placeholder="<?php esc_attr_e('Buscar grupos por tema, ciudad, interés...', 'grupos-telegram'); ?>"
                                   class="search-input"
                                   value="<?php echo esc_attr(get_search_query()); ?>"
                                   autocomplete="off">
                            <input type="hidden" name="post_type" value="grupo">
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                                <span class="sr-only"><?php esc_html_e('Buscar', 'grupos-telegram'); ?></span>
                            </button>
                        </div>
                        
                        <!-- Sugerencias de búsqueda -->
                        <div class="search-suggestions">
                            <span class="suggestions-label">
                                <?php esc_html_e('Búsquedas populares:', 'grupos-telegram'); ?>
                            </span>
                            <?php
                            $sugerencias_populares = [
                                'gaming' => 'Gaming',
                                'tecnologia' => 'Tecnología',
                                'musica' => 'Música',
                                'deportes' => 'Deportes'
                            ];
                            
                            foreach ($sugerencias_populares as $slug => $nombre):
                                $search_url = add_query_arg([
                                    's' => $slug,
                                    'post_type' => 'grupo'
                                ], home_url('/'));
                            ?>
                                <a href="<?php echo esc_url($search_url); ?>" class="suggestion-tag">
                                    #<?php echo esc_html($nombre); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
            
            <!-- Botones de Acción Personalizados -->
            <?php if (!empty($hero_buttons)): ?>
                <div class="hero-buttons">
                    <?php foreach ($hero_buttons as $button): ?>
                        <a href="<?php echo esc_url($button['url'] ?? '#'); ?>" 
                           class="hero-btn <?php echo esc_attr($button['class'] ?? 'btn-primary'); ?>"
                           <?php if ($button['target'] ?? false): ?>target="_blank" rel="noopener"<?php endif; ?>>
                            <?php if ($button['icon'] ?? false): ?>
                                <i class="<?php echo esc_attr($button['icon']); ?>"></i>
                            <?php endif; ?>
                            <?php echo esc_html($button['text'] ?? 'Botón'); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Estadísticas Dinámicas -->
            <?php if ($show_stats): ?>
                <div class="hero-stats">
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-number" data-count="<?php echo esc_attr($total_grupos); ?>">0</div>
                        <div class="stat-label">
                            <?php esc_html_e('Grupos Activos', 'grupos-telegram'); ?>
                        </div>
                    </div>
                    
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-number" data-count="<?php echo esc_attr(number_format($total_miembros)); ?>">0</div>
                        <div class="stat-label">
                            <?php esc_html_e('Miembros Totales', 'grupos-telegram'); ?>
                        </div>
                    </div>
                    
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-number" data-count="<?php echo esc_attr($total_categorias); ?>">0</div>
                        <div class="stat-label">
                            <?php esc_html_e('Categorías', 'grupos-telegram'); ?>
                        </div>
                    </div>
                    
                    <?php if ($grupos_activos): ?>
                        <div class="stat-item" data-aos="fade-up" data-aos-delay="400">
                            <div class="stat-number" data-count="<?php echo esc_attr($grupos_activos); ?>">0</div>
                            <div class="stat-label">
                                <?php esc_html_e('Grupos Hoy', 'grupos-telegram'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Elementos decorativos (opcional) -->
        <div class="hero-decorations">
            <div class="decoration decoration-1">
                <i class="fab fa-telegram-plane"></i>
            </div>
            <div class="decoration decoration-2">
                <i class="fas fa-users"></i>
            </div>
            <div class="decoration decoration-3">
                <i class="fas fa-heart"></i>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <div class="scroll-arrow">
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
</section>