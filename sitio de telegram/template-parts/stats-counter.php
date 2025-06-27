<?php
/**
 * Template Part: Stats Counter
 * 
 * Componente reutilizable para mostrar contadores de estadísticas
 * del sitio con animaciones y efectos visuales.
 * 
 * @package GruposTelegram
 * @since 1.0.0
 * 
 * Variables disponibles:
 * - $stats_args: Array con configuración del componente
 */

// Obtener argumentos del componente
$stats_args = get_query_var('stats_args', array());

// Configuración por defecto
$defaults = array(
    'show_stats' => array('grupos', 'miembros', 'categorias', 'activos'),
    'layout' => 'horizontal', // 'horizontal', 'vertical', 'grid'
    'animation' => true,
    'color_scheme' => 'telegram', // 'telegram', 'gradient', 'colorful'
    'size' => 'medium', // 'small', 'medium', 'large'
    'show_icons' => true,
    'show_labels' => true,
    'category_id' => null, // Para estadísticas de categoría específica
    'custom_stats' => array() // Para estadísticas personalizadas
);

$args = wp_parse_args($stats_args, $defaults);

// Función para obtener estadísticas del sitio
function obtener_estadisticas_sitio($category_id = null) {
    $stats = array();
    
    // Total de grupos
    if ($category_id) {
        $grupos_query = new WP_Query(array(
            'post_type' => 'grupo',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'categoria_grupo',
                    'field' => 'term_id',
                    'terms' => $category_id
                )
            )
        ));
        $stats['grupos'] = $grupos_query->found_posts;
        wp_reset_postdata();
    } else {
        $stats['grupos'] = wp_count_posts('grupo')->publish;
    }
    
    // Total de miembros (suma de todos los grupos)
    $total_miembros = 0;
    $grupos_activos = 0;
    
    $query_args = array(
        'post_type' => 'grupo',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'numero_miembros',
                'compare' => 'EXISTS'
            )
        )
    );
    
    if ($category_id) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'categoria_grupo',
                'field' => 'term_id',
                'terms' => $category_id
            )
        );
    }
    
    $grupos = new WP_Query($query_args);
    
    if ($grupos->have_posts()) {
        while ($grupos->have_posts()) {
            $grupos->the_post();
            $miembros = get_field('numero_miembros');
            $estado = get_field('estado_grupo');
            
            if ($miembros) {
                $total_miembros += intval($miembros);
            }
            
            if ($estado === 'Activo') {
                $grupos_activos++;
            }
        }
    }
    wp_reset_postdata();
    
    $stats['miembros'] = $total_miembros;
    $stats['activos'] = $grupos_activos;
    
    // Total de categorías
    $categorias = get_terms(array(
        'taxonomy' => 'categoria_grupo',
        'hide_empty' => true
    ));
    $stats['categorias'] = count($categorias);
    
    return $stats;
}

// Obtener estadísticas
$estadisticas = obtener_estadisticas_sitio($args['category_id']);

// Configuración de estadísticas a mostrar
$stats_config = array(
    'grupos' => array(
        'label' => 'Grupos Totales',
        'icon' => 'fas fa-users',
        'color' => '#0088cc',
        'value' => $estadisticas['grupos']
    ),
    'miembros' => array(
        'label' => 'Miembros Totales',
        'icon' => 'fas fa-globe',
        'color' => '#229ED9',
        'value' => $estadisticas['miembros'],
        'format' => 'number'
    ),
    'categorias' => array(
        'label' => 'Categorías',
        'icon' => 'fas fa-tags',
        'color' => '#00acc1',
        'value' => $estadisticas['categorias']
    ),
    'activos' => array(
        'label' => 'Grupos Activos',
        'icon' => 'fas fa-heart',
        'color' => '#4caf50',
        'value' => $estadisticas['activos']
    )
);

// Agregar estadísticas personalizadas si existen
if (!empty($args['custom_stats'])) {
    $stats_config = array_merge($stats_config, $args['custom_stats']);
}

// Filtrar estadísticas a mostrar
$stats_to_show = array();
foreach ($args['show_stats'] as $stat_key) {
    if (isset($stats_config[$stat_key])) {
        $stats_to_show[$stat_key] = $stats_config[$stat_key];
    }
}

// Determinar clases CSS basadas en configuración
$container_classes = array('stats-counter');
$container_classes[] = 'layout-' . $args['layout'];
$container_classes[] = 'size-' . $args['size'];
$container_classes[] = 'color-scheme-' . $args['color_scheme'];

if ($args['animation']) {
    $container_classes[] = 'has-animation';
}

$container_class = implode(' ', $container_classes);
?>

<div class="">
    <div class="stats-container">
        <?php foreach ($stats_to_show as $key => $stat) : ?>
            <div class="stat-item stat-" 
                 data-count=""
                 data-animation="">
                
                <?php if ($args['show_icons']) : ?>
                    <div class="stat-icon" 
                         style="color: ">
                        <i class=""></i>
                    </div>
                <?php endif; ?>
                
                <div class="stat-content">
                    <div class="stat-number" 
                         data-final-value=""
                         data-format="">
                        <?php 
                        if (isset($stat['format']) && $stat['format'] === 'number') {
                            echo number_format($stat['value']);
                        } else {
                            echo esc_html($stat['value']);
                        }
                        ?>
                    </div>
                    
                    <?php if ($args['show_labels']) : ?>
                        <div class="stat-label">
                            <?php echo esc_html($stat['label']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Efecto de progreso opcional -->
                <?php if ($args['color_scheme'] === 'gradient') : ?>
                    <div class="stat-progress">
                        <div class="progress-bar" 
                             style="background: linear-gradient(45deg, , 88)"></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- JavaScript para animaciones (si está habilitado) -->
<?php if ($args['animation']) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para animar contadores
    function animateCounter(element) {
        const finalValue = parseInt(element.dataset.finalValue) || 0;
        const format = element.dataset.format || 'default';
        const duration = 2000; // 2 segundos
        const steps = 60;
        const increment = finalValue / steps;
        let currentValue = 0;
        let step = 0;
        
        const timer = setInterval(function() {
            step++;
            currentValue = Math.min(currentValue + increment, finalValue);
            
            if (format === 'number') {
                element.textContent = Math.floor(currentValue).toLocaleString();
            } else {
                element.textContent = Math.floor(currentValue);
            }
            
            if (step >= steps || currentValue >= finalValue) {
                clearInterval(timer);
                if (format === 'number') {
                    element.textContent = finalValue.toLocaleString();
                } else {
                    element.textContent = finalValue;
                }
            }
        }, duration / steps);
    }
    
    // Intersection Observer para activar animaciones cuando sean visibles
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                statNumbers.forEach(animateCounter);
                
                // Agregar clase para animaciones CSS
                entry.target.classList.add('stats-animated');
                
                // Dejar de observar una vez animado
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.5 // Activar cuando el 50% sea visible
    });
    
    // Observar todos los contadores de estadísticas
    const statsCounters = document.querySelectorAll('.stats-counter.has-animation');
    statsCounters.forEach(function(counter) {
        observer.observe(counter);
    });
});
</script>
<?php endif; ?>

<!-- Estilos CSS específicos del componente -->
<style>
/* Estilos base del contador de estadísticas */
.stats-counter {
    padding: 2rem 0;
}

.stats-container {
    display: flex;
    gap: 2rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

/* Layouts */
.layout-horizontal .stats-container {
    flex-direction: row;
}

.layout-vertical .stats-container {
    flex-direction: column;
    gap: 1rem;
}

.layout-grid .stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

/* Elemento de estadística */
.stat-item {
    text-align: center;
    padding: 1rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 136, 204, 0.15);
}

/* Icono */
.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.stat-item:hover .stat-icon {
    transform: scale(1.1);
}

/* Número */
.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #0088cc;
    margin-bottom: 0.5rem;
    line-height: 1;
}

/* Etiqueta */
.stat-label {
    font-size: 0.9rem;
    color: #666;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Tamaños */
.size-small .stat-icon { font-size: 1.8rem; }
.size-small .stat-number { font-size: 1.8rem; }
.size-small .stat-label { font-size: 0.8rem; }

.size-large .stat-icon { font-size: 3.5rem; }
.size-large .stat-number { font-size: 3.5rem; }
.size-large .stat-label { font-size: 1.1rem; }

/* Esquemas de color */
.color-scheme-telegram .stat-item {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
}

.color-scheme-gradient .stat-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.color-scheme-gradient .stat-number,
.color-scheme-gradient .stat-label {
    color: white;
}

/* Animaciones */
.has-animation .stat-item {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease;
}

.stats-animated .stat-item {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
    .layout-horizontal .stats-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .layout-grid .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-number { font-size: 2rem; }
    .stat-icon { font-size: 2rem; }
}

@media (max-width: 480px) {
    .layout-grid .stats-container {
        grid-template-columns: 1fr;
    }
}
</style>