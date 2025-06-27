<?php
/**
 * Funciones auxiliares del tema Grupos de Telegram
 * 
 * @package GruposTelegram
 * @version 1.0.0
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * FUNCIONES PARA ACF (ADVANCED CUSTOM FIELDS)
 * =================================================================
 */

/**
 * Obtiene el valor de un campo ACF con fallback
 */
function gt_get_field($field_name, $post_id = null, $default = '') {
    if (!function_exists('get_field')) {
        return $default;
    }
    
    $value = get_field($field_name, $post_id);
    return !empty($value) ? $value : $default;
}

/**
 * Obtiene la imagen ACF con fallback
 */
function gt_get_image_field($field_name, $post_id = null, $size = 'medium') {
    $image = gt_get_field($field_name, $post_id);
    
    if (is_array($image) && isset($image['sizes'][$size])) {
        return $image['sizes'][$size];
    }
    
    if (is_numeric($image)) {
        $image_url = wp_get_attachment_image_url($image, $size);
        return $image_url ? $image_url : '';
    }
    
    return '';
}

/**
 * Valida si un enlace de Telegram es válido
 */
function gt_validate_telegram_link($link) {
    $pattern = '/^https?:\/\/(t\.me|telegram\.me)\/.+$/i';
    return preg_match($pattern, $link);
}

/**
 * =================================================================
 * FUNCIONES DE FORMATO Y DISPLAY
 * =================================================================
 */

/**
 * Formatea el número de miembros de un grupo
 */
function gt_format_member_count($count) {
    $count = intval($count);
    
    if ($count >= 1000000) {
        return round($count / 1000000, 1) . 'M';
    } elseif ($count >= 1000) {
        return round($count / 1000, 1) . 'K';
    }
    
    return number_format($count);
}

/**
 * Obtiene el tiempo transcurrido desde una fecha
 */
function gt_time_ago($date) {
    $time_ago = strtotime($date);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    
    if ($time_difference < 60) {
        return 'Hace un momento';
    } elseif ($time_difference < 3600) {
        $minutes = floor($time_difference / 60);
        return 'Hace ' . $minutes . ' minuto' . ($minutes > 1 ? 's' : '');
    } elseif ($time_difference < 86400) {
        $hours = floor($time_difference / 3600);
        return 'Hace ' . $hours . ' hora' . ($hours > 1 ? 's' : '');
    } elseif ($time_difference < 2592000) {
        $days = floor($time_difference / 86400);
        return 'Hace ' . $days . ' día' . ($days > 1 ? 's' : '');
    } else {
        return date('d/m/Y', $time_ago);
    }
}

/**
 * Trunca el texto a una longitud específica
 */
function gt_truncate_text($text, $length = 150, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Limpia y formatea las etiquetas de un grupo
 */
function gt_format_tags($tags_string) {
    if (empty($tags_string)) {
        return array();
    }
    
    $tags = explode(',', $tags_string);
    $formatted_tags = array();
    
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            // Añadir # si no lo tiene
            if (substr($tag, 0, 1) !== '#') {
                $tag = '#' . $tag;
            }
            $formatted_tags[] = $tag;
        }
    }
    
    return $formatted_tags;
}

/**
 * =================================================================
 * FUNCIONES DE ESTADÍSTICAS Y CACHE
 * =================================================================
 */

/**
 * Obtiene el total de grupos activos (con cache)
 */
function gt_get_total_active_groups() {
    $cache_key = 'gt_total_active_groups';
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $args = array(
        'post_type' => 'grupo',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'estado_grupo',
                'value' => 'Activo',
                'compare' => '='
            )
        ),
        'fields' => 'ids'
    );
    
    $query = new WP_Query($args);
    $total = $query->found_posts;
    
    // Cache por 1 hora
    set_transient($cache_key, $total, HOUR_IN_SECONDS);
    
    return $total;
}

/**
 * Obtiene el total de miembros de todos los grupos
 */
function gt_get_total_members() {
    $cache_key = 'gt_total_members';
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    global $wpdb;
    
    $total = $wpdb->get_var("
        SELECT SUM(CAST(pm.meta_value AS UNSIGNED)) 
        FROM {$wpdb->postmeta} pm 
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID 
        WHERE pm.meta_key = 'numero_miembros' 
        AND p.post_type = 'grupo' 
        AND p.post_status = 'publish'
    ");
    
    $total = intval($total);
    
    // Cache por 2 horas
    set_transient($cache_key, $total, 2 * HOUR_IN_SECONDS);
    
    return $total;
}

/**
 * Limpia el cache de estadísticas
 */
function gt_clear_stats_cache() {
    delete_transient('gt_total_active_groups');
    delete_transient('gt_total_members');
    delete_transient('gt_categories_count');
}

/**
 * =================================================================
 * FUNCIONES DE TAXONOMÍAS
 * =================================================================
 */

/**
 * Obtiene el estilo (icono y color) de una categoría
 */
function gt_get_category_style($category_slug) {
    $styles = array(
        'gaming' => array(
            'icon' => 'fas fa-gamepad',
            'color' => '#9c27b0'
        ),
        'musica' => array(
            'icon' => 'fas fa-music',
            'color' => '#f44336'
        ),
        'tecnologia' => array(
            'icon' => 'fas fa-laptop-code',
            'color' => '#2196f3'
        ),
        'deportes' => array(
            'icon' => 'fas fa-futbol',
            'color' => '#4caf50'
        ),
        'educacion' => array(
            'icon' => 'fas fa-graduation-cap',
            'color' => '#ff9800'
        ),
        'entretenimiento' => array(
            'icon' => 'fas fa-film',
            'color' => '#e91e63'
        )
    );
    
    return isset($styles[$category_slug]) ? 
           $styles[$category_slug] : 
           array('icon' => 'fas fa-users', 'color' => '#0088cc');
}

/**
 * Obtiene las categorías más populares
 */
function gt_get_popular_categories($limit = 6) {
    $cache_key = 'gt_popular_categories_' . $limit;
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $categories = get_terms(array(
        'taxonomy' => 'categoria_grupo',
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => $limit,
        'hide_empty' => true
    ));
    
    // Cache por 30 minutos
    set_transient($cache_key, $categories, 30 * MINUTE_IN_SECONDS);
    
    return $categories;
}

/**
 * =================================================================
 * FUNCIONES DE NAVEGACIÓN Y BREADCRUMBS
 * =================================================================
 */

/**
 * Genera breadcrumbs para el sitio
 */
function gt_breadcrumbs() {
    if (is_home() || is_front_page()) {
        return;
    }
    
    $breadcrumbs = array();
    $breadcrumbs[] = '<a href="' . home_url() . '"><i class="fas fa-home"></i> Inicio</a>';
    
    if (is_single() && get_post_type() == 'grupo') {
        $breadcrumbs[] = '<a href="' . get_post_type_archive_link('grupo') . '">Grupos</a>';
        
        $categories = wp_get_post_terms(get_the_ID(), 'categoria_grupo');
        if (!empty($categories)) {
            $category = $categories[0];
            $breadcrumbs[] = '<a href="' . get_term_link($category) . '">' . $category->name . '</a>';
        }
        
        $breadcrumbs[] = '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_tax('categoria_grupo')) {
        $breadcrumbs[] = '<a href="' . get_post_type_archive_link('grupo') . '">Grupos</a>';
        $breadcrumbs[] = '<span class="current">' . single_term_title('', false) . '</span>';
    } elseif (is_post_type_archive('grupo')) {
        $breadcrumbs[] = '<span class="current">Grupos</span>';
    } elseif (is_search()) {
        $breadcrumbs[] = '<span class="current">Resultados para: ' . get_search_query() . '</span>';
    } elseif (is_404()) {
        $breadcrumbs[] = '<span class="current">Página no encontrada</span>';
    }
    
    if (!empty($breadcrumbs)) {
        echo '<nav class="breadcrumbs"><div class="container">' . implode(' <i class="fas fa-chevron-right"></i> ', $breadcrumbs) . '</div></nav>';
    }
}

/**
 * =================================================================
 * FUNCIONES DE SEGURIDAD Y VALIDACIÓN
 * =================================================================
 */

/**
 * Sanitiza y valida un enlace de Telegram
 */
function gt_sanitize_telegram_link($link) {
    $link = esc_url_raw($link);
    
    if (!gt_validate_telegram_link($link)) {
        return '';
    }
    
    return $link;
}

/**
 * Valida el número de miembros
 */
function gt_validate_member_count($count) {
    $count = intval($count);
    return ($count >= 0 && $count <= 200000) ? $count : 0;
}

/**
 * =================================================================
 * HOOKS PARA LIMPIAR CACHE
 * =================================================================
 */

/**
 * Limpia cache cuando se guarda un grupo
 */
function gt_clear_cache_on_save($post_id) {
    if (get_post_type($post_id) == 'grupo') {
        gt_clear_stats_cache();
    }
}
add_action('save_post', 'gt_clear_cache_on_save');

/**
 * Limpia cache cuando se elimina un grupo
 */
function gt_clear_cache_on_delete($post_id) {
    if (get_post_type($post_id) == 'grupo') {
        gt_clear_stats_cache();
    }
}
add_action('delete_post', 'gt_clear_cache_on_delete');

/**
 * =================================================================
 * FUNCIONES DE COMPATIBILIDAD
 * =================================================================
 */

/**
 * Verifica si ACF está activo
 */
function gt_is_acf_active() {
    return function_exists('get_field') && class_exists('ACF');
}

/**
 * Mensaje de error si ACF no está activo
 */
function gt_acf_admin_notice() {
    if (!gt_is_acf_active()) {
        echo '<div class="notice notice-error">
            <p><strong>Grupos Telegram Theme:</strong> 
            Este tema requiere el plugin Advanced Custom Fields PRO para funcionar correctamente.</p>
        </div>';
    }
}
add_action('admin_notices', 'gt_acf_admin_notice');