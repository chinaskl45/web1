<?php
/**
 * Telegram Groups Theme - Functions
 * 
 * @package Telegram_Groups
 * @version 1.0.0
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuración básica del tema
 */
function telegram_groups_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo', array(
        'height' => 60,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    register_nav_menus(array(
        'primary' => __('Menú Principal', 'telegram-groups'),
        'footer' => __('Menú Footer', 'telegram-groups'),
    ));
    
    // Tamaños de imagen
    add_image_size('grupo-card', 300, 200, true);
    add_image_size('grupo-featured', 400, 300, true);
    add_image_size('grupo-thumbnail', 150, 150, true);
}
add_action('after_setup_theme', 'telegram_groups_setup');

/**
 * Enqueue scripts y estilos
 */
function telegram_groups_scripts() {
    wp_enqueue_style('telegram-groups-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    wp_enqueue_script('telegram-groups-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    
    wp_localize_script('telegram-groups-js', 'telegram_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('telegram_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'telegram_groups_scripts');

/**
 * Encolar assets específicos para la página de grupo individual
 */
function telegram_groups_enqueue_single_assets() {
    if (is_singular('grupo')) {
        // Verificar si los archivos existen antes de encolarlos
        $css_file = get_template_directory() . '/assets/css/single-grupo.css';
        $js_file = get_template_directory() . '/assets/js/single-grupo.js';
        
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'telegram-single-grupo',
                get_template_directory_uri() . '/assets/css/single-grupo.css',
                array(),
                filemtime($css_file)
            );
        }
        
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'telegram-single-grupo',
                get_template_directory_uri() . '/assets/js/single-grupo.js',
                array('jquery'),
                filemtime($js_file),
                true
            );
            
            // Localización para JavaScript
            wp_localize_script('telegram-single-grupo', 'telegramGroupConfig', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('telegram_nonce'),
                'messages' => array(
                    'sending' => __('Enviando...', 'telegram-groups'),
                    'success' => __('Denuncia enviada correctamente. Gracias por tu colaboración.', 'telegram-groups'),
                    'error' => __('Error al enviar la denuncia. Inténtalo de nuevo.', 'telegram-groups'),
                    'copied' => __('Enlace copiado al portapapeles', 'telegram-groups'),
                    'copyError' => __('No se pudo copiar el enlace', 'telegram-groups')
                )
            ));
        }
        
        // Font Awesome si no está ya cargado
        if (!wp_style_is('font-awesome', 'enqueued')) {
            wp_enqueue_style(
                'font-awesome',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
                array(),
                '6.0.0'
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'telegram_groups_enqueue_single_assets');

/**
 * Post Type - Grupos de Telegram
 */
function telegram_groups_create_post_type() {
    $labels = array(
        'name' => __('Grupos', 'telegram-groups'),
        'singular_name' => __('Grupo', 'telegram-groups'),
        'menu_name' => __('Grupos de Telegram', 'telegram-groups'),
        'add_new' => __('Añadir Nuevo', 'telegram-groups'),
        'add_new_item' => __('Añadir Nuevo Grupo', 'telegram-groups'),
        'edit_item' => __('Editar Grupo', 'telegram-groups'),
        'new_item' => __('Nuevo Grupo', 'telegram-groups'),
        'view_item' => __('Ver Grupo', 'telegram-groups'),
        'search_items' => __('Buscar Grupos', 'telegram-groups'),
        'not_found' => __('No se encontraron grupos', 'telegram-groups'),
        'not_found_in_trash' => __('No hay grupos en la papelera', 'telegram-groups'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'grupo'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest' => true,
    );

    register_post_type('grupo', $args);
}
add_action('init', 'telegram_groups_create_post_type');

/**
 * Taxonomías del tema
 */
function telegram_groups_create_taxonomies() {
    // Categorías de grupos
    register_taxonomy('categoria_grupo', 'grupo', array(
        'labels' => array(
            'name' => __('Categorías', 'telegram-groups'),
            'singular_name' => __('Categoría', 'telegram-groups'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'categoria'),
        'show_in_rest' => true,
    ));

    // Ciudades
    register_taxonomy('ciudad_grupo', 'grupo', array(
        'labels' => array(
            'name' => __('Ciudades', 'telegram-groups'),
            'singular_name' => __('Ciudad', 'telegram-groups'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'ciudad'),
        'show_in_rest' => true,
    ));

    // Etiquetas
    register_taxonomy('etiqueta_grupo', 'grupo', array(
        'labels' => array(
            'name' => __('Etiquetas', 'telegram-groups'),
            'singular_name' => __('Etiqueta', 'telegram-groups'),
        ),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'etiqueta'),
        'show_in_rest' => true,
    ));

    // Destacados
    register_taxonomy('destacados', 'grupo', array(
        'labels' => array(
            'name' => __('Destacados', 'telegram-groups'),
            'singular_name' => __('Destacado', 'telegram-groups'),
        ),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'destacado'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'telegram_groups_create_taxonomies');

/**
 * Funciones auxiliares del tema
 */

// Obtener estilo de categoría
if (!function_exists('get_categoria_style')) {
    function get_categoria_style($categoria_slug) {
        $styles = array(
            'gaming' => array('icon' => 'fas fa-gamepad', 'color' => '#9c27b0'),
            'musica' => array('icon' => 'fas fa-music', 'color' => '#f44336'),
            'tecnologia' => array('icon' => 'fas fa-laptop-code', 'color' => '#2196f3'),
            'deportes' => array('icon' => 'fas fa-futbol', 'color' => '#4caf50'),
            'educacion' => array('icon' => 'fas fa-graduation-cap', 'color' => '#ff9800'),
            'entretenimiento' => array('icon' => 'fas fa-film', 'color' => '#e91e63'),
            'noticias' => array('icon' => 'fas fa-newspaper', 'color' => '#607d8b'),
            'negocios' => array('icon' => 'fas fa-briefcase', 'color' => '#795548'),
            'salud' => array('icon' => 'fas fa-heartbeat', 'color' => '#e91e63'),
            'viajes' => array('icon' => 'fas fa-plane', 'color' => '#00bcd4'),
            'comida' => array('icon' => 'fas fa-utensils', 'color' => '#ff5722'),
            'arte' => array('icon' => 'fas fa-palette', 'color' => '#673ab7'),
            'ciencia' => array('icon' => 'fas fa-flask', 'color' => '#009688'),
            'criptomonedas' => array('icon' => 'fab fa-bitcoin', 'color' => '#f57c00'),
            'trading' => array('icon' => 'fas fa-chart-line', 'color' => '#4caf50'),
            'programacion' => array('icon' => 'fas fa-code', 'color' => '#607d8b'),
            'fotografia' => array('icon' => 'fas fa-camera', 'color' => '#ff9800'),
            'default' => array('icon' => 'fas fa-users', 'color' => '#0088cc')
        );
        
        return isset($styles[$categoria_slug]) ? $styles[$categoria_slug] : $styles['default'];
    }
}

// Formatear número de miembros
if (!function_exists('format_member_count')) {
    function format_member_count($numero) {
        if (!$numero) return '0';
        
        if ($numero >= 1000000) {
            return number_format($numero / 1000000, 1) . 'M';
        } elseif ($numero >= 1000) {
            return number_format($numero / 1000, 1) . 'K';
        }
        return number_format($numero);
    }
}

// Tiempo desde última actividad
if (!function_exists('time_since_activity')) {
    function time_since_activity($fecha) {
        if (!$fecha) return __('Sin actividad reciente', 'telegram-groups');
        
        $now = time();
        $activity_time = strtotime($fecha);
        $diff = $now - $activity_time;
        
        if ($diff < 3600) {
            return sprintf(__('Hace %d minutos', 'telegram-groups'), floor($diff / 60));
        } elseif ($diff < 86400) {
            return sprintf(__('Hace %d horas', 'telegram-groups'), floor($diff / 3600));
        } else {
            return sprintf(__('Hace %d días', 'telegram-groups'), floor($diff / 86400));
        }
    }
}

/**
 * Handler AJAX para reportar grupos
 */
function telegram_groups_handle_report() {
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'telegram_nonce')) {
        wp_send_json_error('Nonce verification failed');
        return;
    }
    
    // Sanitizar datos
    $grupo_id = intval($_POST['grupo_id']);
    $motivo = sanitize_text_field($_POST['motivo']);
    $detalles = sanitize_textarea_field($_POST['detalles'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    
    // Validar datos requeridos
    if (empty($grupo_id) || empty($motivo)) {
        wp_send_json_error('Datos requeridos faltantes');
        return;
    }
    
    // Verificar que el grupo existe
    if (!get_post($grupo_id) || get_post_type($grupo_id) !== 'grupo') {
        wp_send_json_error('Grupo no válido');
        return;
    }
    
    // Preparar datos del reporte
    $report_data = array(
        'grupo_id' => $grupo_id,
        'motivo' => $motivo,
        'detalles' => $detalles,
        'email' => $email,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'fecha' => current_time('mysql'),
        'estado' => 'pendiente'
    );
    
    // Guardar en la base de datos
    global $wpdb;
    $table_name = $wpdb->prefix . 'telegram_group_reports';
    
    $result = $wpdb->insert($table_name, $report_data);
    
    if ($result === false) {
        wp_send_json_error('Error al guardar el reporte');
        return;
    }
    
    // Enviar notificación por email al administrador
    telegram_groups_send_report_notification($grupo_id, $motivo, $detalles, $email);
    
    // Incrementar contador de reportes del grupo
    $current_reports = get_post_meta($grupo_id, '_report_count', true) ?: 0;
    update_post_meta($grupo_id, '_report_count', $current_reports + 1);
    
    wp_send_json_success('Reporte enviado correctamente');
}
add_action('wp_ajax_report_group', 'telegram_groups_handle_report');
add_action('wp_ajax_nopriv_report_group', 'telegram_groups_handle_report');

/**
 * Crear tabla de reportes al activar el tema
 */
function telegram_groups_create_reports_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'telegram_group_reports';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        grupo_id int(11) NOT NULL,
        motivo varchar(100) NOT NULL,
        detalles text,
        email varchar(100),
        ip varchar(45),
        user_agent text,
        fecha datetime DEFAULT CURRENT_TIMESTAMP,
        estado varchar(20) DEFAULT 'pendiente',
        PRIMARY KEY (id),
        KEY grupo_id (grupo_id),
        KEY fecha (fecha)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'telegram_groups_create_reports_table');

/**
 * Enviar notificación de reporte por email
 */
function telegram_groups_send_report_notification($grupo_id, $motivo, $detalles, $reporter_email) {
    $grupo_title = get_the_title($grupo_id);
    $grupo_url = get_permalink($grupo_id);
    $admin_email = get_option('admin_email');
    
    $subject = sprintf('[%s] Nuevo reporte de grupo: %s', get_bloginfo('name'), $grupo_title);
    
    $message = sprintf(
        "Se ha recibido un nuevo reporte para el grupo: %s\n\n" .
        "URL del grupo: %s\n" .
        "Motivo: %s\n" .
        "Detalles: %s\n" .
        "Email del reportador: %s\n" .
        "Fecha: %s\n\n" .
        "Puedes revisar el grupo en el admin de WordPress.",
        $grupo_title,
        $grupo_url,
        $motivo,
        $detalles ?: 'No especificado',
        $reporter_email ?: 'No proporcionado',
        current_time('Y-m-d H:i:s')
    );
    
    wp_mail($admin_email, $subject, $message);
}

/**
 * Registrar sidebar específico para grupos
 */
function telegram_groups_register_sidebar() {
    register_sidebar(array(
        'name' => 'Sidebar de Grupos',
        'id' => 'grupo-sidebar',
        'description' => 'Sidebar que aparece en las páginas individuales de grupos',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'telegram_groups_register_sidebar');

/**
 * Agregar metabox para mostrar reportes en el admin
 */
function telegram_groups_add_reports_metabox() {
    add_meta_box(
        'telegram_group_reports',
        'Reportes del Grupo',
        'telegram_groups_reports_metabox_callback',
        'grupo',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'telegram_groups_add_reports_metabox');

/**
 * Callback del metabox de reportes
 */
function telegram_groups_reports_metabox_callback($post) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'telegram_group_reports';
    $reports = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE grupo_id = %d ORDER BY fecha DESC LIMIT 10",
        $post->ID
    ));
    
    $total_reports = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE grupo_id = %d",
        $post->ID
    ));
    
    echo '<p><strong>Total de reportes:</strong> ' . intval($total_reports) . '</p>';
    
    if ($reports) {
        echo '<div style="max-height: 300px; overflow-y: auto;">';
        foreach ($reports as $report) {
            echo '<div style="border-bottom: 1px solid #eee; padding: 10px 0;">';
            echo '<strong>Motivo:</strong> ' . esc_html($report->motivo) . '<br>';
            if ($report->detalles) {
                echo '<strong>Detalles:</strong> ' . esc_html($report->detalles) . '<br>';
            }
            echo '<strong>Fecha:</strong> ' . esc_html($report->fecha) . '<br>';
            echo '<strong>Estado:</strong> ' . esc_html($report->estado);
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No hay reportes para este grupo.</p>';
    }
}

/**
 * Optimización: Precargar recursos críticos
 */
function telegram_groups_preload_resources() {
    if (is_singular('grupo')) {
        $css_file = get_template_directory_uri() . '/assets/css/single-grupo.css';
        $js_file = get_template_directory_uri() . '/assets/js/single-grupo.js';
        
        if (file_exists(get_template_directory() . '/assets/css/single-grupo.css')) {
            echo '<link rel="preload" href="' . $css_file . '" as="style">';
        }
        if (file_exists(get_template_directory() . '/assets/js/single-grupo.js')) {
            echo '<link rel="preload" href="' . $js_file . '" as="script">';
        }
    }
}
add_action('wp_head', 'telegram_groups_preload_resources');

/**
 * Crear contenido de ejemplo si no existe
 */
function crear_contenido_ejemplo() {
    // Solo crear contenido si no hay grupos
    $grupos_existentes = wp_count_posts('grupo');
    if ($grupos_existentes->publish > 0) {
        return; // Ya hay contenido
    }
    
    $grupos_ejemplo = array(
        array(
            'titulo' => 'Grupo de Tecnología',
            'descripcion' => 'Discusiones sobre las últimas tecnologías y programación.',
            'categoria' => 'tecnologia'
        ),
        array(
            'titulo' => 'Deportes y Fitness',  
            'descripcion' => 'Comparte tu rutina de ejercicios y tips deportivos.',
            'categoria' => 'deportes'
        ),
        array(
            'titulo' => 'Gaming Community',
            'descripcion' => 'Gamers unidos, reviews y tips de videojuegos.',
            'categoria' => 'gaming'
        )
    );
    
    foreach ($grupos_ejemplo as $grupo) {
        // Crear el post
        $post_id = wp_insert_post(array(
            'post_title' => $grupo['titulo'],
            'post_content' => $grupo['descripcion'],
            'post_excerpt' => $grupo['descripcion'],
            'post_status' => 'publish',
            'post_type' => 'grupo',
            'meta_input' => array(
                'estado_grupo' => 'Activo',
                'numero_miembros' => rand(50, 5000),
                'enlace_telegram' => 'https://t.me/ejemplo_' . sanitize_title($grupo['titulo'])
            )
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Asignar categoría
            if (taxonomy_exists('categoria_grupo')) {
                $categoria_term = get_term_by('slug', $grupo['categoria'], 'categoria_grupo');
                if (!$categoria_term) {
                    // Crear la categoría si no existe
                    $categoria_term = wp_insert_term(
                        ucfirst($grupo['categoria']),
                        'categoria_grupo',
                        array('slug' => $grupo['categoria'])
                    );
                    if (!is_wp_error($categoria_term)) {
                        wp_set_post_terms($post_id, array($categoria_term['term_id']), 'categoria_grupo');
                    }
                } else {
                    wp_set_post_terms($post_id, array($categoria_term->term_id), 'categoria_grupo');
                }
            }
        }
    }
}
add_action('after_switch_theme', 'crear_contenido_ejemplo');

/**
 * Flush rewrite rules después de registrar post types
 */
function telegram_groups_flush_rewrites() {
    telegram_groups_create_post_type();
    telegram_groups_create_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'telegram_groups_flush_rewrites');

/**
 * Incluir archivos adicionales solo si existen
 */
$inc_files = array(
    '/inc/customizer.php',
    '/inc/template-functions.php'
);

foreach ($inc_files as $file) {
    if (file_exists(get_template_directory() . $file)) {
        require get_template_directory() . $file;
    }
}

/**
 * Debug en desarrollo (solo para administradores)
 */
function debug_telegram_groups() {
    if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('administrator') && isset($_GET['debug'])) {
        $debug_info = array(
            'post_type_grupo' => post_type_exists('grupo'),
            'taxonomy_categoria' => taxonomy_exists('categoria_grupo'),
            'taxonomy_ciudad' => taxonomy_exists('ciudad_grupo'),
            'taxonomy_destacados' => taxonomy_exists('destacados'),
            'taxonomy_etiqueta' => taxonomy_exists('etiqueta_grupo'),
            'total_grupos' => wp_count_posts('grupo'),
            'css_file_exists' => file_exists(get_template_directory() . '/assets/css/single-grupo.css'),
            'js_file_exists' => file_exists(get_template_directory() . '/assets/js/single-grupo.js')
        );
        
        echo '<pre style="background: #f0f0f0; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo 'DEBUG Telegram Groups: ' . print_r($debug_info, true);
        echo '</pre>';
    }
}
add_action('wp_footer', 'debug_telegram_groups');