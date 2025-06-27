<?php
/**
 * Template part para mostrar tarjeta de categoría
 * 
 * @package GruposTelegram
 * @subpackage Template_Parts
 * @since 1.0.0
 * 
 * @param WP_Term $categoria - Objeto de término de categoría
 * @param string $card_class - Clases CSS adicionales (opcional)
 * @param bool $show_count - Mostrar contador de grupos (opcional, default: true)
 * @param string $size - Tamaño de la tarjeta: small, medium, large (opcional, default: medium)
 */

// Obtener parámetros pasados
$categoria = $args['categoria'] ?? null;
$card_class = $args['card_class'] ?? '';
$show_count = $args['show_count'] ?? true;
$size = $args['size'] ?? 'medium';

// Validar que existe la categoría
if (!$categoria || is_wp_error($categoria)) {
    return;
}

// Obtener estilo de la categoría (icono y color)
$estilo_categoria = obtener_estilo_categoria($categoria->slug);

// Generar clases CSS
$classes = array(
    'categoria-card',
    'categoria-' . $categoria->slug,
    'size-' . $size,
    'hover-scale',
    $card_class
);

// Determinar tamaños según el size
$icon_size = array(
    'small' => 'text-2xl',
    'medium' => 'text-4xl',
    'large' => 'text-5xl'
);

$padding = array(
    'small' => 'p-4',
    'medium' => 'p-6',
    'large' => 'p-8'
);
?>

<!-- Tarjeta de Categoría -->
<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <a href="<?php echo esc_url(get_term_link($categoria)); ?>" 
       class="categoria-link block text-center <?php echo $padding[$size]; ?> rounded-xl 
              bg-white border border-gray-200 transition-all duration-300 
              hover:shadow-lg hover:border-blue-300 group"
       aria-label="Ver grupos de <?php echo esc_attr($categoria->name); ?>">
        
        <!-- Icono de la categoría -->
        <div class="categoria-icon <?php echo $icon_size[$size]; ?> mb-4 
                    transition-transform duration-300 group-hover:scale-110"
             style="color: <?php echo esc_attr($estilo_categoria['color']); ?>">
            <i class="<?php echo esc_attr($estilo_categoria['icon']); ?>" 
               aria-hidden="true"></i>
        </div>
        
        <!-- Nombre de la categoría -->
        <h3 class="categoria-nombre font-semibold text-gray-800 mb-2 
                   group-hover:text-blue-600 transition-colors duration-300
                   <?php echo $size === 'large' ? 'text-xl' : ($size === 'small' ? 'text-sm' : 'text-base'); ?>">
            <?php echo esc_html($categoria->name); ?>
        </h3>
        
        <!-- Contador de grupos (opcional) -->
        <?php if ($show_count) : ?>
            <p class="categoria-contador text-sm text-gray-600 
                      group-hover:text-gray-700 transition-colors duration-300">
                <?php 
                if ($categoria->count == 1) {
                    printf('%d grupo', $categoria->count);
                } else {
                    printf('%s grupos', number_format($categoria->count));
                }
                ?>
            </p>
        <?php endif; ?>
        
        <!-- Descripción (si existe y es tamaño large) -->
        <?php if ($size === 'large' && !empty($categoria->description)) : ?>
            <p class="categoria-descripcion text-xs text-gray-500 mt-2 line-clamp-2">
                <?php echo esc_html(wp_trim_words($categoria->description, 10)); ?>
            </p>
        <?php endif; ?>
        
        <!-- Indicador de enlace -->
        <div class="categoria-arrow mt-3 opacity-0 group-hover:opacity-100 
                    transition-opacity duration-300">
            <i class="fas fa-arrow-right text-blue-500 text-sm"></i>
        </div>
        
    </a>
</div>

<!-- Esquema JSON-LD para SEO (solo en tamaño large) -->
<?php if ($size === 'large') : ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "<?php echo esc_js($categoria->name); ?>",
        "description": "<?php echo esc_js($categoria->description); ?>",
        "url": "<?php echo esc_url(get_term_link($categoria)); ?>",
        "numberOfItems": <?php echo intval($categoria->count); ?>
    }
    </script>
<?php endif; ?>