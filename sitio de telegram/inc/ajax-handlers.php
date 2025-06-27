/**
 * Manejadores AJAX para el tema Grupos de Telegram
 * 
 * @package GruposTelegram
 * @since 1.0.0
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase principal para manejar todas las peticiones AJAX
 */
class GruposTelegram_Ajax_Handlers {
    
    /**
     * Constructor - Registra todos los hooks AJAX
     */
    public function __construct() {
        // Búsqueda en tiempo real
        add_action('wp_ajax_grupos_live_search', [$this, 'live_search']);
        add_action('wp_ajax_nopriv_grupos_live_search', [$this, 'live_search']);
        
        // Filtros dinámicos
        add_action('wp_ajax_grupos_filter', [$this, 'filter_grupos']);
        add_action('wp_ajax_nopriv_grupos_filter', [$this, 'filter_grupos']);
        
        // Cargar más contenido
        add_action('wp_ajax_load_more_grupos', [$this, 'load_more_grupos']);
        add_action('wp_ajax_nopriv_load_more_grupos', [$this, 'load_more_grupos']);
        
        // Favoritos
        add_action('wp_ajax_toggle_favorite', [$this, 'toggle_favorite']);
        add_action('wp_ajax_nopriv_toggle_favorite', [$this, 'toggle_favorite']);
        
        // Newsletter
        add_action('wp_ajax_subscribe_newsletter', [$this, 'subscribe_newsletter']);
        add_action('wp_ajax_nopriv_subscribe_newsletter', [$this, 'subscribe_newsletter']);
        
        // Contacto
        add_action('wp_ajax_contact_form', [$this, 'handle_contact_form']);
        add_action('wp_ajax_nopriv_contact_form', [$this, 'handle_contact_form']);
        
        // Estadísticas
        add_action('wp_ajax_get_stats', [$this, 'get_site_stats']);
        add_action('wp_ajax_nopriv_get_stats', [$this, 'get_site_stats']);
    }
    
    /**
     * Búsqueda en tiempo real de grupos
     */
    public function live_search() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grupos_ajax_nonce')) {
            wp_die('Acceso denegado');
        }
        
        $query = sanitize_text_field($_POST['query'] ?? '');
        $limit = intval($_POST['limit'] ?? 5);
        
        if (strlen($query) < 3) {
            wp_send_json_error('Query muy corta');
        }
        
        $args = [
            'post_type' => 'grupo',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            's' => $query,
            'meta_query' => [
                [
                    'key' => 'estado_grupo',
                    'value' => 'Activo',
                    'compare' => '='
                ]
            ]
        ];
        
        $grupos = new WP_Query($args);
        $results = [];
        
        if ($grupos->have_posts()) {
            while ($grupos->have_posts()) {
                $grupos->the_post();
                
                $categoria = wp_get_post_terms(get_the_ID(), 'categoria_grupo');
                $categoria_nombre = !empty($categoria) ? $categoria[0]->name : '';
                
                $results[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'excerpt' => wp_trim_words(get_field('descripcion_corta'), 15),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                    'categoria' => $categoria_nombre,
                    'miembros' => number_format(get_field('numero_miembros') ?: 0),
                    'estado' => get_field('estado_grupo')
                ];
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success([
            'results' => $results,
            'total' => $grupos->found_posts
        ]);
    }
    
    /**
     * Filtrar grupos por criterios
     */
    public function filter_grupos() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grupos_ajax_nonce')) {
            wp_die('Acceso denegado');
        }
        
        $categoria = sanitize_text_field($_POST['categoria'] ?? '');
        $estado = sanitize_text_field($_POST['estado'] ?? '');
        $orderby = sanitize_text_field($_POST['orderby'] ?? 'date');
        $order = sanitize_text_field($_POST['order'] ?? 'DESC');
        $page = intval($_POST['page'] ?? 1);
        
        $args = [
            'post_type' => 'grupo',
            'posts_per_page' => 12,
            'paged' => $page,
            'post_status' => 'publish',
            'orderby' => $orderby,
            'order' => $order
        ];
        
        // Filtro por categoría
        if (!empty($categoria)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'categoria_grupo',
                    'field' => 'slug',
                    'terms' => $categoria
                ]
            ];
        }
        
        // Filtro por estado
        if (!empty($estado)) {
            $args['meta_query'] = [
                [
                    'key' => 'estado_grupo',
                    'value' => ucfirst($estado),
                    'compare' => '='
                ]
            ];
        }
        
        // Ordenamiento especial
        if ($orderby === 'members') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'numero_miembros';
        }
        
        $grupos = new WP_Query($args);
        $html = '';
        
        if ($grupos->have_posts()) {
            while ($grupos->have_posts()) {
                $grupos->the_post();
                
                ob_start();
                get_template_part('template-parts/grupo-card', null, [
                    'variant' => 'grid'
                ]);
                $html .= ob_get_clean();
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success([
            'html' => $html,
            'total' => $grupos->found_posts,
            'pages' => $grupos->max_num_pages,
            'current_page' => $page
        ]);
    }
    
    /**
     * Cargar más grupos (paginación infinita)
     */
    public function load_more_grupos() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grupos_ajax_nonce')) {
            wp_die('Acceso denegado');
        }
        
        $page = intval($_POST['page'] ?? 1);
        $posts_per_page = intval($_POST['posts_per_page'] ?? 12);
        
        $args = [
            'post_type' => 'grupo',
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'estado_grupo',
                    'value' => 'Activo',
                    'compare' => '='
                ]
            ]
        ];
        
        $grupos = new WP_Query($args);
        $html = '';
        
        if ($grupos->have_posts()) {
            while ($grupos->have_posts()) {
                $grupos->the_post();
                
                ob_start();
                get_template_part('template-parts/grupo-card');
                $html .= ob_get_clean();
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success([
            'html' => $html,
            'has_more' => $page < $grupos->max_num_pages
        ]);
    }
    
    /**
     * Toggle favoritos
     */
    public function toggle_favorite() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grupos_ajax_nonce')) {
            wp_die('Acceso denegado');
        }
        
        $grupo_id = intval($_POST['grupo_id'] ?? 0);
        
        if (!$grupo_id) {
            wp_send_json_error('ID de grupo inválido');
        }
        
        // Usar cookies para usuarios no registrados
        $favorites_key = 'grupos_favorites';
        $favorites = isset($_COOKIE[$favorites_key]) ? 
                      json_decode(stripslashes($_COOKIE[$favorites_key]), true) : [];
        
        $is_favorite = in_array($grupo_id, $favorites);
        
        if ($is_favorite) {
            $favorites = array_diff($favorites, [$grupo_id]);
            $action = 'removed';
        } else {
            $favorites[] = $grupo_id;
            $action = 'added';
        }
        
        setcookie($favorites_key, json_encode($favorites), time() + (86400 * 30), '/');
        
        wp_send_json_success([
            'action' => $action,
            'total_favorites' => count($favorites)
        ]);
    }
    
    /**
     * Suscripción a newsletter
     */
    public function subscribe_newsletter() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grupos_ajax_nonce')) {
            wp_die('Acceso denegado');
        }
        
        $email = sanitize_email($_POST['email'] ?? '');
        
        if (!is_email($email)) {
            wp_send_json_error('Email inválido');
        }
        
        // Verificar si ya está suscrito
        $subscribers = get_option('grupos_newsletter_subscribers', []);
        
        if (in_array($email, $subscribers)) {
            wp_send_json_error('Ya estás suscrito');
        }
        
        // Añadir suscriptor
        $subscribers[] = $email;
        update_option('grupos_newsletter_subscribers', $subscribers);
        
        // Enviar email de confirmación
        $subject = 'Suscripción confirmada - Grupos Telegram';
        $message = '¡Gracias por suscribirte! Recibirás las últimas novedades de grupos de Telegram.';
        wp_mail($email, $subject, $message);
        
        wp_send_json_success('¡Suscripción exitosa!');
    }
    
    /**
     * Manejar formulario de contacto
     */
    public function handle_contact_form() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grupos_ajax_nonce')) {
            wp_die('Acceso denegado');
        }
        
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $subject = sanitize_text_field($_POST['subject'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');
        
        // Validaciones
        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error('Todos los campos son obligatorios');
        }
        
        if (!is_email($email)) {
            wp_send_json_error('Email inválido');
        }
        
        // Preparar email
        $to = get_option('admin_email');
        $email_subject = '[Grupos Telegram] ' . $subject;
        $email_message = "Nombre: {$name}\n";
        $email_message .= "Email: {$email}\n\n";
        $email_message .= "Mensaje:\n{$message}";
        
        $headers = [
            'From: ' . $name . ' <' . $email . '>',
            'Reply-To: ' . $email
        ];
        
        if (wp_mail($to, $email_subject, $email_message, $headers)) {
            wp_send_json_success('Mensaje enviado exitosamente');
        } else {
            wp_send_json_error('Error al enviar el mensaje');
        }
    }
    
    /**
     * Obtener estadísticas del sitio
     */
    public function get_site_stats() {
        // Cache de 5 minutos
        $cache_key = 'grupos_site_stats';
        $stats = get_transient($cache_key);
        
        if (false === $stats) {
            $total_grupos = wp_count_posts('grupo')->publish;
            $total_categorias = wp_count_terms('categoria_grupo');
            
            // Contar grupos activos
            $grupos_activos = new WP_Query([
                'post_type' => 'grupo',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'meta_query' => [
                    [
                        'key' => 'estado_grupo',
                        'value' => 'Activo',
                        'compare' => '='
                    ]
                ]
            ]);
            
            // Calcular total de miembros
            $total_miembros = 0;
            foreach ($grupos_activos->posts as $grupo_id) {
                $miembros = get_field('numero_miembros', $grupo_id);
                $total_miembros += intval($miembros);
            }
            
            $stats = [
                'total_grupos' => $total_grupos,
                'grupos_activos' => $grupos_activos->found_posts,
                'total_categorias' => $total_categorias,
                'total_miembros' => $total_miembros,
                'timestamp' => current_time('timestamp')
            ];
            
            set_transient($cache_key, $stats, 5 * MINUTE_IN_SECONDS);
        }
        
        wp_send_json_success($stats);
    }
}

// Inicializar la clase
new GruposTelegram_Ajax_Handlers();

/**
 * Funciones auxiliares para AJAX
 */

/**
 * Generar nonce para AJAX
 */
function grupos_get_ajax_nonce() {
    return wp_create_nonce('grupos_ajax_nonce');
}

/**
 * Localizar script con datos AJAX
 */
function grupos_localize_ajax_data() {
    wp_localize_script('grupos-main-js', 'gruposAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => grupos_get_ajax_nonce(),
        'messages' => [
            'loading' => 'Cargando...',
            'error' => 'Error en la petición',
            'no_results' => 'No se encontraron resultados',
            'try_again' => 'Inténtalo de nuevo'
        ]
    ]);
}
add_action('wp_enqueue_scripts', 'grupos_localize_ajax_data');

/**
 * Rate limiting para peticiones AJAX
 */
function grupos_ajax_rate_limit($action = 'default', $limit = 30, $window = 60) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'ajax_rate_limit_' . md5($ip . $action);
    
    $requests = get_transient($key) ?: 0;
    
    if ($requests >= $limit) {
        wp_send_json_error('Demasiadas peticiones. Inténtalo más tarde.', 429);
    }
    
    set_transient($key, $requests + 1, $window);
}

/**
 * Log de actividad AJAX para debugging
 */
function grupos_ajax_log($action, $data = []) {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    $log_entry = [
        'timestamp' => current_time('mysql'),
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'data' => $data
    ];
    
    error_log('GRUPOS AJAX: ' . json_encode($log_entry));
}